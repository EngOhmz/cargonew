<?php

namespace App\Support;

class ParcelQuantitySummary
{
    /**
     * Break down parcel quantity across store, truck, and delivered.
     *
     * @return array{
     *     total: int,
     *     stoo: int,
     *     on_car: int,
     *     dropped: int,
     *     has_remaining_stoo: bool,
     *     is_partially_loaded: bool,
     *     is_fully_accounted: bool
     * }
     */
    public static function breakdown($parcel): array
    {
        $total = (int) ($parcel->idadi ?? 0);
        $stoo = (int) ($parcel->idadi_stoo ?? 0);
        $onCar = (int) ($parcel->idadi_kupakia ?? 0);
        $dropped = (int) ($parcel->idadi_shusha ?? 0);

        // If store qty was not kept in sync, derive remaining from total − gari − imeshushwa
        $accounted = $stoo + $onCar + $dropped;
        if ($total > 0 && $accounted !== $total) {
            $computedStoo = max(0, $total - $onCar - $dropped);
            if ($stoo === 0 && $computedStoo > 0) {
                $stoo = $computedStoo;
            }
        }

        return [
            'total' => $total,
            'stoo' => $stoo,
            'on_car' => $onCar,
            'dropped' => $dropped,
            'has_remaining_stoo' => $stoo > 0,
            'is_partially_loaded' => $stoo > 0 && ($onCar > 0 || $dropped > 0),
            'is_fully_accounted' => ($stoo + $onCar + $dropped) === $total,
        ];
    }
}
