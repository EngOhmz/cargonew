<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ParcelDropAnalysis
{
    /**
     * @return array{njiani: Collection, mwisho: Collection, summary: array{njiani_qty: int, mwisho_qty: int, njiani_amount: float, mwisho_amount: float}}
     */
    public static function forParcel(int $parcelId, ?string $fallbackDestination = null): array
    {
        $rows = self::queryRows([$parcelId]);

        return self::buildFromRows($rows, $fallbackDestination);
    }

    /**
     * @param  array<int>  $parcelIds
     * @return array<int, array{njiani: Collection, mwisho: Collection, summary: array}>
     */
    public static function forParcels(array $parcelIds): array
    {
        $parcelIds = array_values(array_filter(array_map('intval', $parcelIds)));
        if ($parcelIds === []) {
            return [];
        }

        $rows = self::queryRows($parcelIds);
        $destinations = DB::table('customer_pacels')
            ->whereIn('id', $parcelIds)
            ->pluck('mzigo_unapokwenda', 'id');

        $result = [];
        foreach ($parcelIds as $parcelId) {
            $parcelRows = $rows->where('customer_pacel_id', $parcelId)->values();
            $result[$parcelId] = self::buildFromRows(
                $parcelRows,
                $destinations[$parcelId] ?? null
            );
        }

        return $result;
    }

    private static function queryRows(array $parcelIds): Collection
    {
        return DB::table('drop_off_parcels')
            ->leftJoin('drop_off_points', 'drop_off_parcels.drop_off_point_id', '=', 'drop_off_points.id')
            ->leftJoin('driver_routes', 'drop_off_parcels.driver_route_id', '=', 'driver_routes.id')
            ->leftJoin('pacel_histories', 'drop_off_parcels.pacel_history_id', '=', 'pacel_histories.id')
            ->leftJoin('cars', 'drop_off_parcels.car_id', '=', 'cars.id')
            ->whereIn('drop_off_parcels.customer_pacel_id', $parcelIds)
            ->orderBy('drop_off_parcels.created_at', 'asc')
            ->select(
                'drop_off_parcels.customer_pacel_id',
                'drop_off_parcels.idadi_kushusha',
                'drop_off_parcels.amount_received',
                'drop_off_parcels.is_destination',
                'drop_off_parcels.created_at',
                'drop_off_points.drop_off_point',
                'driver_routes.to as route_destination',
                'pacel_histories.receipt as history_receipt',
                'cars.carNumber as car_name'
            )
            ->get();
    }

    /**
     * @return array{njiani: Collection, mwisho: Collection, summary: array{njiani_qty: int, mwisho_qty: int, njiani_amount: float, mwisho_amount: float}}
     */
    private static function buildFromRows(Collection $rows, ?string $fallbackDestination = null): array
    {
        $njiani = collect();
        $mwisho = collect();
        $summary = [
            'njiani_qty' => 0,
            'mwisho_qty' => 0,
            'njiani_amount' => 0.0,
            'mwisho_amount' => 0.0,
        ];

        foreach ($rows as $row) {
            $qty = (int) ($row->idadi_kushusha ?? 0);
            $amount = (float) ($row->amount_received ?? 0);
            $isDestination = (int) ($row->is_destination ?? 0) === 1;

            $entry = (object) [
                'location' => $isDestination
                    ? ($row->history_receipt ?: $row->route_destination ?: $fallbackDestination ?: 'Mwisho wa safari')
                    : ($row->drop_off_point ?: 'Kituo cha njiani'),
                'idadi_kushusha' => $qty,
                'amount_received' => $amount,
                'car_name' => $row->car_name,
                'created_at' => $row->created_at,
            ];

            if ($isDestination) {
                $mwisho->push($entry);
                $summary['mwisho_qty'] += $qty;
                $summary['mwisho_amount'] += $amount;
            } else {
                $njiani->push($entry);
                $summary['njiani_qty'] += $qty;
                $summary['njiani_amount'] += $amount;
            }
        }

        return [
            'njiani' => $njiani,
            'mwisho' => $mwisho,
            'summary' => $summary,
        ];
    }
}
