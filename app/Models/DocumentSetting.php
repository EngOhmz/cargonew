<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentSetting extends Model
{
    protected $table = 'document_settings';

    protected $fillable = [
        'company_id',
        'header_line1',
        'header_line2',
        'office_1_label',
        'office_1_place',
        'office_1_contact',
        'office_1_contact_2',
        'office_1_contact_3',
        'office_2_label',
        'office_2_place',
        'office_2_contact',
        'office_3_label',
        'office_3_place',
        'office_3_contact',
        'kanuni_1',
        'kanuni_2',
        'kanuni_3',
        'kanuni_4',
        'logo_type',
        'system_logo_title',
        'tin_number'
    ];

    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class, 'company_id');
    }

    /**
     * Get document settings for the given company (or first global row).
     */
    public static function forCompany($companyId)
    {
        if (!$companyId) {
            return static::whereNull('company_id')->first();
        }

        return static::where('company_id', $companyId)->first() ?? static::whereNull('company_id')->first();
    }
}
