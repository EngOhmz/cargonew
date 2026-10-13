<?php

namespace App\Support;

use App\Models\Sms;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SmsUsageReport
{
    /**
     * SMS rows that count toward usage (exclude template rows).
     */
    public static function usageQuery(?int $companyId = null, ?string $from = null, ?string $to = null)
    {
        $query = Sms::query()
            ->where('status', '!=', 'template')
            ->whereNotIn('id', Sms::TEMPLATE_IDS);

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        return $query;
    }

    /**
     * @return array{sent:int,failed:int,pending:int,total:int}
     */
    public static function counts(?int $companyId = null, ?string $from = null, ?string $to = null): array
    {
        $base = self::usageQuery($companyId, $from, $to);

        $sent = (clone $base)->where('status', 'sent')->count();
        $failed = (clone $base)->where('status', 'failed')->count();
        $pending = (clone $base)->where('status', 'pending')->count();

        return [
            'sent' => $sent,
            'failed' => $failed,
            'pending' => $pending,
            'total' => $sent + $failed + $pending,
        ];
    }

    /**
     * Per-company SMS counts for billing overview.
     *
     * @return Collection<int, object{company_id:int,sent:int,failed:int,total:int}>
     */
    public static function countsByCompany(?string $from = null, ?string $to = null): Collection
    {
        $query = self::usageQuery(null, $from, $to)
            ->whereNotNull('company_id')
            ->select(
                'company_id',
                DB::raw("SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as sent"),
                DB::raw("SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed"),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('company_id');

        return $query->get()->keyBy('company_id');
    }

    public static function defaultMonthRange(): array
    {
        return [
            'from' => now()->startOfMonth()->toDateString(),
            'to' => now()->endOfMonth()->toDateString(),
        ];
    }
}
