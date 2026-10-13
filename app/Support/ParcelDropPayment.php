<?php

namespace App\Support;

class ParcelDropPayment
{
    /**
     * Suggested payment when dropping [idadiKushusha] units (capped by remaining parcel balance).
     * Partial payments below this are allowed; this is a guide only.
     */
    public static function requiredAmount($parcel, int $idadiKushusha): float
    {
        if ($idadiKushusha <= 0) {
            return 0.0;
        }

        $remaining = self::maxAllowedAmount($parcel);
        if ($remaining <= 0) {
            return 0.0;
        }

        $idadiTotal = max(1, (int) $parcel->idadi);
        $bei = (float) str_replace(',', '', (string) ($parcel->bei ?? 0));
        $jumla = (float) str_replace(',', '', (string) ($parcel->jumla ?? 0));
        if ($bei <= 0) {
            $bei = $jumla / $idadiTotal;
        }

        return min($bei * $idadiKushusha, $remaining);
    }

    /**
     * Maximum amount that can still be received for this parcel (remaining balance).
     */
    public static function maxAllowedAmount($parcel): float
    {
        $jumla = (float) str_replace(',', '', (string) ($parcel->jumla ?? 0));
        $received = (float) str_replace(',', '', (string) ($parcel->ela_iliyopokelewa ?? 0));

        return max(0.0, $jumla - $received);
    }

    public static function amountsMatch(float $required, float $received): bool
    {
        return abs($required - $received) < 0.01;
    }
}
