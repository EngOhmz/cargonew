<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class Sms extends Model
{
    protected $table = 'sms';

    public const TEMPLATE_KEYS = [1, 2, 3, 4];

    /** Templates that actually send SMS. All four are kept; none send until re-enabled. */
    public const ACTIVE_TEMPLATE_KEYS = [];

    /** @deprecated Use TEMPLATE_KEYS — kept for older code paths */
    public const TEMPLATE_IDS = [1, 2, 3, 4];

    public const TEMPLATE_LABELS = [
        1 => 'Mzigo umepokelewa ofisini (imezimwa)',
        2 => 'Mzigo umepakiwa / uko njiani (imezimwa)',
        3 => 'Mzigo umefika eneo la kusudi (imezimwa)',
        4 => 'Malipo yamethibitishwa (imezimwa)',
    ];

    public const DEFAULT_MESSAGES = [
        1 => 'Habari. [Company] imepokea mzigo wako kutoka [Supplier]. Idadi: [Packages]. Namba ya kufuatilia (REF): [Ref]. Tutakujulisha mzigo ukifika. Asante.',
        2 => 'Mzigo wako umepakiwa na uko njiani. Gari: [Vehicle]. Idadi: [Packages].',
        3 => 'Habari. Mzigo wako umefika [Destination] ([Company]). Idadi: [Packages]. Kiasi kinachohitajika: TZS [Amount]. Tafadhali fika kuchukua au wasiliana na ofisi. Asante.',
        4 => 'Malipo yako ya TZS [Amount] yamethibitishwa. Asante kwa kutumia [Company].',
    ];

    protected $fillable = [
        'customer_id',
        'phone_number',
        'message',
        'status',
        'template_key',
        'is_enabled',
        'shoot_id',
        'response',
        'company_id',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    public function customer()
    {
        return $this->belongsTo(\App\Models\Customer\Customer::class, 'customer_id');
    }

    public static function isActiveTemplateKey(int $key): bool
    {
        return in_array($key, self::ACTIVE_TEMPLATE_KEYS, true);
    }

    public function isSendingEnabled(): bool
    {
        $key = (int) ($this->template_key ?: 0);
        if (!self::isActiveTemplateKey($key)) {
            return false;
        }

        if (Schema::hasColumn($this->getTable(), 'is_enabled')) {
            return (bool) $this->is_enabled;
        }

        return true;
    }

    /**
     * System-wide default templates (shared baseline, not edited by companies).
     */
    public function scopeGlobalDefaults($query)
    {
        return $query->where('status', 'template')
            ->whereNull('company_id')
            ->whereIn('template_key', self::TEMPLATE_KEYS);
    }

    /**
     * Templates belonging to one company.
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('status', 'template')
            ->where('company_id', $companyId)
            ->whereIn('template_key', self::TEMPLATE_KEYS);
    }

    /**
     * @deprecated Prefer forCompany / globalDefaults
     */
    public function scopeTemplates($query)
    {
        return $query->where('status', 'template')->whereIn('id', self::TEMPLATE_IDS);
    }

    public function isTemplate(): bool
    {
        return $this->status === 'template'
            && $this->template_key
            && in_array((int) $this->template_key, self::TEMPLATE_KEYS, true);
    }

    public function getTemplateLabelAttribute(): string
    {
        $key = (int) ($this->template_key ?: $this->id);

        return self::TEMPLATE_LABELS[$key] ?? 'Kiolezo cha SMS';
    }

    public static function defaultForKey(int $key): ?self
    {
        $row = static::globalDefaults()->where('template_key', $key)->first();
        if ($row) {
            return $row;
        }

        // Legacy fallback before template_key migration
        return static::where('status', 'template')->where('id', $key)->first();
    }

    /**
     * Resolve template for a company (own copy), falling back to global default.
     * Lazily creates the company copy on first use.
     */
    public static function resolveTemplate(int $key, $companyId = null): ?self
    {
        if (!in_array($key, self::TEMPLATE_KEYS, true)) {
            return null;
        }

        if ($companyId) {
            $companyTemplate = static::forCompany($companyId)->where('template_key', $key)->first();
            if ($companyTemplate) {
                return $companyTemplate;
            }

            $default = static::defaultForKey($key);
            $message = $default
                ? $default->message
                : (self::DEFAULT_MESSAGES[$key] ?? '');

            $attrs = [
                'message' => $message,
                'phone_number' => null,
            ];
            if (Schema::hasColumn((new static)->getTable(), 'is_enabled')) {
                $attrs['is_enabled'] = self::isActiveTemplateKey($key);
            }

            return static::firstOrCreate(
                [
                    'status' => 'template',
                    'company_id' => $companyId,
                    'template_key' => $key,
                ],
                $attrs
            );
        }

        return static::defaultForKey($key);
    }

    /**
     * Ensure all 4 templates exist for a company; return them ordered.
     * Disabled templates are kept for viewing/editing but do not send.
     */
    public static function ensureCompanyTemplates($companyId): Collection
    {
        if (!$companyId) {
            return static::globalDefaults()->orderBy('template_key')->get();
        }

        foreach (self::TEMPLATE_KEYS as $key) {
            $tpl = static::resolveTemplate($key, $companyId);
            if ($tpl && Schema::hasColumn($tpl->getTable(), 'is_enabled')) {
                $shouldEnable = self::isActiveTemplateKey((int) $key);
                if ((bool) $tpl->is_enabled !== $shouldEnable) {
                    $tpl->is_enabled = $shouldEnable;
                    $tpl->save();
                }
            }
        }

        return static::forCompany($companyId)->orderBy('template_key')->get();
    }
}
