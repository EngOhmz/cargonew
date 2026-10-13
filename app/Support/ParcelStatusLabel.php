<?php

namespace App\Support;

class ParcelStatusLabel
{
    /**
     * Human-readable Swahili label for parcel cargo status.
     * When activity is "kupakia", the linked car status determines the displayed stage.
     */
    public static function label(
        ?string $activity,
        $carStatus = null,
        $idadiShusha = null,
        $idadi = null
    ): string {
        $activity = strtolower(trim((string) ($activity ?? '')));

        if ($activity === 'kushusha') {
            if ($idadi !== null && $idadiShusha !== null
                && (int) $idadi > 0 && (int) $idadiShusha >= (int) $idadi) {
                return 'Imeshushwa';
            }

            return 'Inashushwa';
        }

        if ($activity === 'malipo') {
            return 'Malipo imesasishwa';
        }

        if ($activity === 'ipo njiani') {
            if ($idadiShusha !== null && (int) $idadiShusha > 0) {
                if ($idadi !== null && (int) $idadi > 0 && (int) $idadiShusha >= (int) $idadi) {
                    return 'Imeshushwa';
                }

                return 'Inashushwa';
            }

            return 'Ipo njiani';
        }

        if ($activity === 'kupakia') {
            return self::labelForKupakia($carStatus);
        }

        if ($activity === 'kusajiliwa' || $activity === '') {
            return 'Imesajiliwa';
        }

        if ($activity === 'imeshafika') {
            return 'Imefika';
        }

        if ($activity === 'inasubiri') {
            return 'Inasubiri';
        }

        return ucfirst($activity);
    }

    private static function labelForKupakia($carStatus): string
    {
        if ($carStatus === null || $carStatus === '') {
            return 'Inapakiwa';
        }

        switch ((string) $carStatus) {
            case '0':
                return 'Ipo njiani';
            case '1':
                return 'Imefika';
            case '2':
                return 'Inapakiwa';
            case '3':
                return 'Imefungwa';
            case '5':
                return 'Inasubiri';
            default:
                return 'Inapakiwa';
        }
    }

    /** Tailwind badge classes for web views. */
    public static function badgeClasses(?string $activity, $carStatus = null): string
    {
        $label = self::label($activity, $carStatus);

        switch ($label) {
            case 'Ipo njiani':
                return 'bg-blue-100 text-blue-700';
            case 'Imefika':
                return 'bg-indigo-100 text-indigo-700';
            case 'Inapakiwa':
                return 'bg-green-100 text-green-700';
            case 'Imefungwa':
                return 'bg-gray-200 text-gray-800';
            case 'Inasubiri':
                return 'bg-yellow-100 text-yellow-700';
            case 'Inashushwa':
                return 'bg-orange-100 text-orange-700';
            case 'Imeshushwa':
                return 'bg-red-100 text-red-700';
            case 'Malipo imesasishwa':
                return 'bg-purple-100 text-purple-700';
            default:
                return 'bg-gray-100 text-gray-700';
        }
    }

    /** Append activity_label (and car_status when known) to a parcel API payload. */
    public static function enrichPayload(array $payload, $parcel, $car = null): array
    {
        if ($car === null && !empty($parcel->car_id)) {
            if (method_exists($parcel, 'relationLoaded') && $parcel->relationLoaded('car')) {
                $car = $parcel->car;
            } else {
                $car = \App\Models\Management\Car::find($parcel->car_id);
            }
        }

        $payload['activity'] = $parcel->activity;
        $payload['activity_label'] = self::label(
            $parcel->activity,
            $car ? $car->status : null,
            $parcel->idadi_shusha ?? null,
            $parcel->idadi ?? null
        );

        if ($car) {
            $payload['car_status'] = (string) $car->status;
        }

        return $payload;
    }
}
