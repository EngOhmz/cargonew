<?php

namespace App\Services;

use App\Models\CargoRoute;
use App\Models\CargoRouteStop;
use App\Models\DropOffPoint;
use App\Models\DriverRoute;
use App\Models\Mikoa;
use Carbon\Carbon;
use Illuminate\Support\Str;

class CargoRouteService
{
    public function allowsEnrouteDrops(): bool
    {
        return (bool) config('app.allow_enroute_drops', false);
    }

    public function normalizeDestinationLabel(?string $value): string
    {
        return Str::lower(trim(preg_replace('/\s+/', ' ', (string) $value)));
    }

    public function destinationsMatch(?string $a, ?string $b): bool
    {
        $left = $this->normalizeDestinationLabel($a);
        $right = $this->normalizeDestinationLabel($b);

        if ($left === '' || $right === '') {
            return false;
        }

        return $left === $right
            || str_contains($left, $right)
            || str_contains($right, $left);
    }

    /**
     * Ensure the route's final stop (to_mikoa) exists on cargo_route_stops.
     */
    public function syncFinalStop(CargoRoute $route): void
    {
        if (!$route->to_mikoa_id) {
            return;
        }

        $maxOrder = CargoRouteStop::where('cargo_route_id', $route->id)->max('stop_order');

        CargoRouteStop::updateOrCreate(
            [
                'cargo_route_id' => $route->id,
                'mikoa_id' => $route->to_mikoa_id,
            ],
            [
                'stop_order' => is_null($maxOrder) ? 99 : max((int) $maxOrder, 99),
                'is_final' => true,
            ]
        );

        CargoRouteStop::where('cargo_route_id', $route->id)
            ->where('mikoa_id', '!=', $route->to_mikoa_id)
            ->update(['is_final' => false]);
    }

    /**
     * Resolve registration fields for one parcel line.
     *
     * @return array{from:string,to:string,drop:string,cargo_route_id:?int,drop_mikoa_id:?int}
     */
    public function resolveParcelRouteFields(?int $cargoRouteId, ?int $dropMikoaId, ?int $companyId): array
    {
        if (!$cargoRouteId || !$companyId) {
            return [
                'from' => '#',
                'to' => '#',
                'drop' => '#',
                'cargo_route_id' => null,
                'drop_mikoa_id' => null,
            ];
        }

        $route = CargoRoute::with(['fromMikoa', 'toMikoa', 'stops.mikoa'])
            ->where('company_id', $companyId)
            ->find($cargoRouteId);

        if (!$route) {
            return [
                'from' => '#',
                'to' => '#',
                'drop' => '#',
                'cargo_route_id' => null,
                'drop_mikoa_id' => null,
            ];
        }

        $from = $route->fromMikoa->regional_name ?? '#';
        $routeEnd = $route->toMikoa->regional_name ?? '#';

        $dropMikoa = null;
        if ($dropMikoaId) {
            $dropMikoa = Mikoa::where('company_id', $companyId)->find($dropMikoaId);
        }

        if (!$dropMikoa) {
            $finalStop = $route->stops->firstWhere('is_final', true)
                ?? $route->stops->sortByDesc('stop_order')->first();
            $dropMikoaId = ($finalStop ? $finalStop->mikoa_id : null) ?? $route->to_mikoa_id;
            $dropMikoa = ($finalStop && $finalStop->mikoa) ? $finalStop->mikoa : $route->toMikoa;
        }

        $dropName = ($dropMikoa && $dropMikoa->regional_name) ? $dropMikoa->regional_name : $routeEnd;

        return [
            'from' => $from,
            'to' => $routeEnd,
            'drop' => $dropName,
            'cargo_route_id' => $route->id,
            'drop_mikoa_id' => $dropMikoaId,
        ];
    }

    public function validateDropMikoaOnRoute(?int $cargoRouteId, ?int $dropMikoaId, ?int $companyId): bool
    {
        if (!$cargoRouteId || !$dropMikoaId || !$companyId) {
            return false;
        }

        return CargoRouteStop::where('cargo_route_id', $cargoRouteId)
            ->where('mikoa_id', $dropMikoaId)
            ->whereHas('cargoRoute', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
            ->exists();
    }

    /**
     * Labels a parcel may be offloaded at for an active driver route on a car.
     */
    public function allowedPackDestinationsForDriverRoute(DriverRoute $route, ?int $companyId = null): array
    {
        $labels = [];

        if (!empty($route->to)) {
            $labels[] = (string) $route->to;
        }

        $points = DropOffPoint::with('mikoa')
            ->where('driver_route_id', $route->id)
            ->orderBy('stop_order')
            ->orderBy('id')
            ->get();

        foreach ($points as $point) {
            if (!empty($point->drop_off_point)) {
                $labels[] = (string) $point->drop_off_point;
            }
            if ($point->mikoa_id && $point->relationLoaded('mikoa') && $point->mikoa) {
                $labels[] = (string) $point->mikoa->regional_name;
            } elseif ($point->mikoa_id) {
                $name = Mikoa::where('id', $point->mikoa_id)->value('regional_name');
                if ($name) {
                    $labels[] = (string) $name;
                }
            }
        }

        if ($companyId) {
            $cargoRoute = $this->findMatchingCargoRoute($route->from, $route->to, $companyId);
            if ($cargoRoute) {
                $this->syncFinalStop($cargoRoute);
                $stops = CargoRouteStop::with('mikoa')
                    ->where('cargo_route_id', $cargoRoute->id)
                    ->orderBy('stop_order')
                    ->get();

                foreach ($stops as $stop) {
                    if ($stop->mikoa && !empty($stop->mikoa->regional_name)) {
                        $labels[] = (string) $stop->mikoa->regional_name;
                    }
                }
            }
        }

        return array_values(array_unique(array_filter($labels)));
    }

    public function parcelDestinationMatchesLabels(?string $parcelDestination, array $labels): bool
    {
        foreach ($labels as $label) {
            if ($this->destinationsMatch($parcelDestination, $label)) {
                return true;
            }
        }

        return false;
    }

    public function parcelMatchesDropPoint($parcel, ?string $dropLabel, ?int $dropMikoaId = null): bool
    {
        $dest = is_array($parcel)
            ? ($parcel['mzigo_unapokwenda'] ?? '')
            : ($parcel->mzigo_unapokwenda ?? '');

        $parcelDropId = is_array($parcel)
            ? ($parcel['drop_mikoa_id'] ?? null)
            : ($parcel->drop_mikoa_id ?? null);

        if ($dropMikoaId) {
            if ($parcelDropId && (int) $parcelDropId === (int) $dropMikoaId) {
                return true;
            }

            $mikoaName = Mikoa::where('id', $dropMikoaId)->value('regional_name');
            if ($mikoaName && $this->destinationsMatch($dest, $mikoaName)) {
                return true;
            }
        }

        if ($parcelDropId && !$dropMikoaId && $dropLabel) {
            $parcelMikoaName = Mikoa::where('id', $parcelDropId)->value('regional_name');
            if ($parcelMikoaName && $this->destinationsMatch($parcelMikoaName, $dropLabel)) {
                return true;
            }
        }

        return $this->destinationsMatch($dest, $dropLabel);
    }

    /**
     * Parcel belongs at the route's final destination (not an intermediate stop).
     */
    public function parcelMatchesFinalDestination($parcel, ?string $routeToLabel, ?int $companyId = null): bool
    {
        // Madekenya: unload everything remaining on the car at the final destination.
        if (!$this->allowsEnrouteDrops()) {
            return true;
        }

        $dest = is_array($parcel)
            ? ($parcel['mzigo_unapokwenda'] ?? '')
            : ($parcel->mzigo_unapokwenda ?? '');

        $parcelDropId = is_array($parcel)
            ? ($parcel['drop_mikoa_id'] ?? null)
            : ($parcel->drop_mikoa_id ?? null);

        $cargoRouteId = is_array($parcel)
            ? ($parcel['cargo_route_id'] ?? null)
            : ($parcel->cargo_route_id ?? null);

        if ($parcelDropId && $cargoRouteId) {
            $isFinal = CargoRouteStop::where('cargo_route_id', $cargoRouteId)
                ->where('mikoa_id', $parcelDropId)
                ->where('is_final', true)
                ->exists();

            if ($isFinal) {
                return $this->destinationsMatch($dest, $routeToLabel)
                    || $this->destinationsMatch(
                        Mikoa::where('id', $parcelDropId)->value('regional_name'),
                        $routeToLabel
                    );
            }

            return false;
        }

        if ($parcelDropId && $companyId) {
            $dropName = Mikoa::where('id', $parcelDropId)->value('regional_name');
            if ($dropName && !$this->destinationsMatch($dropName, $routeToLabel)) {
                return false;
            }
        }

        return $this->destinationsMatch($dest, $routeToLabel);
    }

    public function findMatchingCargoRoute(?string $from, ?string $to, ?int $companyId): ?CargoRoute
    {
        if (!$companyId) {
            return null;
        }

        $routes = CargoRoute::with(['fromMikoa', 'toMikoa'])
            ->where('company_id', $companyId)
            ->get();

        foreach ($routes as $route) {
            $fromName = $route->fromMikoa->regional_name ?? '';
            $toName = $route->toMikoa->regional_name ?? '';

            if ($this->destinationsMatch($from, $fromName) && $this->destinationsMatch($to, $toName)) {
                return $route;
            }
        }

        return null;
    }

    public function resolveMikoaIdForLabel(?string $label, ?int $companyId): ?int
    {
        if (!$label || !$companyId) {
            return null;
        }

        foreach (Mikoa::where('company_id', $companyId)->get(['id', 'regional_name']) as $mikoa) {
            if ($this->destinationsMatch($label, $mikoa->regional_name)) {
                return (int) $mikoa->id;
            }
        }

        return null;
    }

    public function backfillDropPointMikoaId(DropOffPoint $point, ?int $companyId): void
    {
        if ($point->mikoa_id || !$companyId) {
            return;
        }

        $mikoaId = $this->resolveMikoaIdForLabel($point->drop_off_point, $companyId);
        if ($mikoaId) {
            $point->mikoa_id = $mikoaId;
            $point->save();
        }
    }

    /**
     * Create driver drop-off points from cargo route intermediate stops (web-configured).
     */
    public function syncDriverRouteDropPointsFromCargoStops(DriverRoute $driverRoute, ?int $companyId): void
    {
        if (!$this->allowsEnrouteDrops() || !$companyId) {
            return;
        }

        $cargoRoute = $this->findMatchingCargoRoute($driverRoute->from, $driverRoute->to, $companyId);
        if (!$cargoRoute) {
            return;
        }

        $this->syncFinalStop($cargoRoute);

        $stops = CargoRouteStop::with('mikoa')
            ->where('cargo_route_id', $cargoRoute->id)
            ->where('is_final', false)
            ->orderBy('stop_order')
            ->get();

        foreach ($stops as $stop) {
            $mikoaName = $stop->mikoa->regional_name ?? '';
            if (!$mikoaName || !$stop->mikoa_id) {
                continue;
            }

            $exists = DropOffPoint::where('driver_route_id', $driverRoute->id)
                ->where(function ($q) use ($stop, $mikoaName) {
                    $q->where('mikoa_id', $stop->mikoa_id)
                        ->orWhere('drop_off_point', $mikoaName);
                })
                ->exists();

            if ($exists) {
                continue;
            }

            DropOffPoint::create([
                'car_id' => $driverRoute->car_id,
                'driver_id' => $driverRoute->driver_id,
                'driver_route_id' => $driverRoute->id,
                'drop_off_point' => $mikoaName,
                'mikoa_id' => $stop->mikoa_id,
                'stop_order' => $stop->stop_order,
                'company_id' => $companyId,
            ]);
        }
    }

    /**
     * Cargo route stops for a driver route (for API / app display).
     */
    public function cargoStopsForDriverRoute(DriverRoute $driverRoute, ?int $companyId): array
    {
        $cargoRoute = $this->findMatchingCargoRoute($driverRoute->from, $driverRoute->to, $companyId);
        if (!$cargoRoute) {
            return [];
        }

        $this->syncFinalStop($cargoRoute);

        $stops = CargoRouteStop::with('mikoa')
            ->where('cargo_route_id', $cargoRoute->id)
            ->orderBy('stop_order')
            ->get();

        if (!$this->allowsEnrouteDrops()) {
            $stops = $stops->where('is_final', true);
        }

        return $stops
            ->map(function (CargoRouteStop $stop) {
                return [
                    'mikoa_id' => $stop->mikoa_id,
                    'name' => $stop->mikoa->regional_name ?? '?',
                    'is_final' => (bool) $stop->is_final,
                    'stop_order' => (int) $stop->stop_order,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * Stops keyed by cargo_route_id for JSON in registration forms.
     */
    public function stopsJsonForCompany(?int $companyId): array
    {
        if (!$companyId) {
            return [];
        }

        $routes = CargoRoute::with(['stops.mikoa'])
            ->where('company_id', $companyId)
            ->get();

        $out = [];
        foreach ($routes as $route) {
            $this->syncFinalStop($route);
            $route->load('stops.mikoa');

            $stops = $route->stops->sortBy('stop_order')->values();
            if (!$this->allowsEnrouteDrops()) {
                $stops = $stops->where('is_final', true)->values();
            }

            $out[(string) $route->id] = $stops
                ->map(function (CargoRouteStop $stop) {
                    return [
                        'mikoa_id' => $stop->mikoa_id,
                        'name' => $stop->mikoa->regional_name ?? '?',
                        'is_final' => (bool) $stop->is_final,
                        'stop_order' => (int) $stop->stop_order,
                    ];
                })
                ->all();
        }

        return $out;
    }

    /**
     * Validate and normalize split quantities per stop for one parcel line.
     *
     * @param array<int,int|string> $splitRows mikoa_id => qty
     * @return array{ok:bool,message:?string,lines:array<int,array{mikoa_id:int,quantity:int,stop_order:int,name:string,is_final:bool}>}
     */
    public function validateDestinationSplit(
        array $splitRows,
        int $totalQuantity,
        ?int $cargoRouteId,
        ?int $companyId
    ): array {
        if (!$cargoRouteId || !$companyId || $totalQuantity <= 0) {
            return ['ok' => false, 'message' => 'Route na idadi zinahitajika', 'lines' => []];
        }

        if (!$this->allowsEnrouteDrops()) {
            return $this->finalDestinationOnlyLines($totalQuantity, $cargoRouteId, $companyId);
        }

        $route = CargoRoute::with(['stops.mikoa'])
            ->where('company_id', $companyId)
            ->find($cargoRouteId);

        if (!$route) {
            return ['ok' => false, 'message' => 'Route haijapatikana', 'lines' => []];
        }

        $this->syncFinalStop($route);
        $allowedStops = $route->stops->keyBy('mikoa_id');
        $lines = [];
        $sum = 0;

        foreach ($splitRows as $mikoaId => $qty) {
            $mikoaId = (int) $mikoaId;
            $qty = (int) $qty;
            if ($qty <= 0) {
                continue;
            }

            $stop = $allowedStops->get($mikoaId);
            if (!$stop) {
                return [
                    'ok' => false,
                    'message' => 'Kituo si sahihi kwa route uliyochagua',
                    'lines' => [],
                ];
            }

            $sum += $qty;
            $lines[] = [
                'mikoa_id' => $mikoaId,
                'quantity' => $qty,
                'stop_order' => (int) $stop->stop_order,
                'name' => $stop->mikoa->regional_name ?? '?',
                'is_final' => (bool) $stop->is_final,
            ];
        }

        if (empty($lines)) {
            return ['ok' => false, 'message' => 'Gawa idadi kwa angalau kituo kimoja', 'lines' => []];
        }

        if ($sum > $totalQuantity) {
            return [
                'ok' => false,
                'message' => "Jumla ya mgawanyo ($sum) imezidi idadi ya mzigo ($totalQuantity). Punguza idadi kwa kila kituo.",
                'lines' => [],
            ];
        }

        if ($sum < $totalQuantity) {
            return [
                'ok' => false,
                'message' => "Jumla ya mgawanyo ($sum) ni chini ya idadi ya mzigo ($totalQuantity). Gawa idadi zote kwa vituo.",
                'lines' => [],
            ];
        }

        usort($lines, function ($a, $b) {
            return $a['stop_order'] <=> $b['stop_order'];
        });

        return ['ok' => true, 'message' => null, 'lines' => $lines];
    }

    /**
     * Madekenya: all quantity goes to the route final destination only.
     */
    public function finalDestinationOnlyLines(int $totalQuantity, ?int $cargoRouteId, ?int $companyId): array
    {
        $route = CargoRoute::with(['stops.mikoa', 'toMikoa'])
            ->where('company_id', $companyId)
            ->find($cargoRouteId);

        if (!$route) {
            return ['ok' => false, 'message' => 'Route haijapatikana', 'lines' => []];
        }

        $this->syncFinalStop($route);
        $route->load('stops.mikoa');

        $stop = $route->stops->firstWhere('is_final', true);
        $mikoaId = $stop ? $stop->mikoa_id : $route->to_mikoa_id;
        $mikoa = $stop && $stop->mikoa ? $stop->mikoa : $route->toMikoa;
        $name = optional($mikoa)->regional_name ?? '?';

        if (!$mikoaId) {
            return ['ok' => false, 'message' => 'Route haina kituo cha mwisho', 'lines' => []];
        }

        return [
            'ok' => true,
            'message' => null,
            'lines' => [[
                'mikoa_id' => (int) $mikoaId,
                'quantity' => $totalQuantity,
                'stop_order' => (int) ($stop->stop_order ?? 99),
                'name' => $name,
                'is_final' => true,
            ]],
        ];
    }

    public function buildDropLabelFromLines(array $lines): string
    {
        $parts = [];
        foreach ($lines as $line) {
            $parts[] = ($line['name'] ?? '?') . ' (' . (int) $line['quantity'] . ')';
        }

        return implode(', ', $parts);
    }

    public function createDestinationsForParcel($parcel, array $lines, ?int $companyId): void
    {
        foreach ($lines as $line) {
            \App\Models\Customer\CustomerPacelDestination::create([
                'customer_pacel_id' => $parcel->id,
                'cargo_route_id' => $parcel->cargo_route_id,
                'drop_mikoa_id' => $line['mikoa_id'],
                'quantity' => $line['quantity'],
                'idadi_kupakia' => 0,
                'idadi_shusha' => 0,
                'stop_order' => $line['stop_order'],
                'company_id' => $companyId,
            ]);
        }
    }

    /**
     * Allocate packed quantity across destination lines (stop order).
     */
    public function allocatePackToDestinations($parcel, int $packQty): void
    {
        $lines = $parcel->destinations()->orderBy('stop_order')->orderBy('id')->get();
        if ($lines->isEmpty()) {
            return;
        }

        $remaining = $packQty;
        foreach ($lines as $line) {
            if ($remaining <= 0) {
                break;
            }

            $onTruck = (int) $line->idadi_kupakia;
            $lineCap = (int) $line->quantity;
            $canPack = max(0, $lineCap - $onTruck);
            $add = min($remaining, $canPack);

            if ($add > 0) {
                $line->idadi_kupakia = $onTruck + $add;
                $line->save();
                $remaining -= $add;
            }
        }
    }

    /**
     * Return packed quantity from destination lines when unpacking (kushusha back to store).
     */
    public function deallocatePackFromDestinations($parcel, int $unpackQty): void
    {
        $lines = $parcel->destinations()->orderByDesc('stop_order')->orderByDesc('id')->get();
        if ($lines->isEmpty()) {
            return;
        }

        $remaining = $unpackQty;
        foreach ($lines as $line) {
            if ($remaining <= 0) {
                break;
            }

            $onTruck = (int) $line->idadi_kupakia;
            $remove = min($remaining, $onTruck);
            if ($remove > 0) {
                $line->idadi_kupakia = $onTruck - $remove;
                $line->save();
                $remaining -= $remove;
            }
        }
    }

    public function dropFromDestinationLine($line, int $dropQty): void
    {
        $line->idadi_kupakia = max(0, (int) $line->idadi_kupakia - $dropQty);
        $line->idadi_shusha = (int) $line->idadi_shusha + $dropQty;
        $line->save();
    }

    /**
     * Drop qty across destination lines (final unload when no specific line is sent).
     */
    public function dropAcrossDestinationLines($parcel, int $dropQty): void
    {
        $lines = $parcel->destinations()->orderByDesc('stop_order')->orderByDesc('id')->get();
        if ($lines->isEmpty()) {
            return;
        }

        $remaining = $dropQty;
        foreach ($lines as $line) {
            if ($remaining <= 0) {
                break;
            }
            $onTruck = (int) $line->idadi_kupakia;
            $take = min($remaining, $onTruck);
            if ($take > 0) {
                $this->dropFromDestinationLine($line, $take);
                $remaining -= $take;
            }
        }
    }

    /**
     * Quantity still on the truck for this parcel (header or destination lines).
     */
    public function packedQuantityOnTruck($parcel): int
    {
        $header = (int) ($parcel->idadi_kupakia ?? 0);
        $lines = 0;
        if (method_exists($parcel, 'destinations')) {
            if ($parcel->relationLoaded('destinations')) {
                $lines = (int) $parcel->destinations->sum(function ($line) {
                    return (int) $line->idadi_kupakia;
                });
            } else {
                $lines = (int) $parcel->destinations()->sum('idadi_kupakia');
            }
        }

        return max($header, $lines);
    }

    public function parcelLineMatchesDropPoint($line, ?string $dropLabel, ?int $dropMikoaId = null): bool
    {
        if ($dropMikoaId && (int) $line->drop_mikoa_id === (int) $dropMikoaId) {
            return true;
        }

        $name = $line->relationLoaded('dropMikoa') && $line->dropMikoa
            ? $line->dropMikoa->regional_name
            : Mikoa::where('id', $line->drop_mikoa_id)->value('regional_name');

        return $this->destinationsMatch($name, $dropLabel);
    }

    public function parcelLineMatchesFinalDestination($line, ?string $routeToLabel): bool
    {
        $name = $line->relationLoaded('dropMikoa') && $line->dropMikoa
            ? $line->dropMikoa->regional_name
            : Mikoa::where('id', $line->drop_mikoa_id)->value('regional_name');

        // Madekenya: no en-route drops — any remaining stock may be unloaded at destination.
        if (!$this->allowsEnrouteDrops()) {
            if ($routeToLabel && $name && $this->destinationsMatch($name, $routeToLabel)) {
                return true;
            }

            // Still allow unload at final even if label mismatch / legacy intermediate lines.
            return true;
        }

        $isFinal = CargoRouteStop::where('cargo_route_id', $line->cargo_route_id)
            ->where('mikoa_id', $line->drop_mikoa_id)
            ->where('is_final', true)
            ->exists();

        if (!$isFinal) {
            return false;
        }

        return $this->destinationsMatch($name, $routeToLabel);
    }

    /**
     * Expand parcels on a car into drop rows (one per destination line with stock on truck).
     *
     * @return array<int,array<string,mixed>>
     */
    public function expandParcelsForDropList($pacels, ?string $dropLabel, ?int $dropMikoaId, bool $isFinalDestinationDrop): array
    {
        $rows = [];
        $finalOnly = $isFinalDestinationDrop && !$this->allowsEnrouteDrops();

        foreach ($pacels as $parcel) {
            $parcel->loadMissing(['destinations.dropMikoa']);

            if ($parcel->destinations->isEmpty()) {
                if ($isFinalDestinationDrop) {
                    if (!$finalOnly && !$this->parcelMatchesFinalDestination($parcel, $dropLabel, $parcel->company_id)) {
                        continue;
                    }
                } elseif (!$this->parcelMatchesDropPoint($parcel, $dropLabel, $dropMikoaId)) {
                    continue;
                }

                if ((int) $parcel->idadi_kupakia <= 0) {
                    continue;
                }

                $rows[] = $this->formatDropParcelRow($parcel, null);
                continue;
            }

            $addedLineRows = 0;
            foreach ($parcel->destinations as $line) {
                if ((int) $line->idadi_kupakia <= 0) {
                    continue;
                }

                if ($isFinalDestinationDrop) {
                    if (!$this->parcelLineMatchesFinalDestination($line, $dropLabel)) {
                        continue;
                    }
                } elseif (!$this->parcelLineMatchesDropPoint($line, $dropLabel, $dropMikoaId)) {
                    continue;
                }

                $rows[] = $this->formatDropParcelRow($parcel, $line);
                $addedLineRows++;
            }

            // Packed on parcel but destination lines not updated (legacy pack) — still show at final.
            if ($addedLineRows === 0 && (int) $parcel->idadi_kupakia > 0) {
                if ($finalOnly || ($isFinalDestinationDrop && $this->parcelMatchesFinalDestination($parcel, $dropLabel, $parcel->company_id))) {
                    $rows[] = $this->formatDropParcelRow($parcel, null);
                } elseif (!$isFinalDestinationDrop && $this->parcelMatchesDropPoint($parcel, $dropLabel, $dropMikoaId)) {
                    $rows[] = $this->formatDropParcelRow($parcel, null);
                }
            }
        }

        return $rows;
    }

    protected function formatDropParcelRow($parcel, $line = null): array
    {
        $jumla = (float) str_replace(',', '', $parcel->jumla);
        $received = (float) str_replace(',', '', $parcel->ela_iliyopokelewa ?? 0);
        $remaining = $jumla - $received;

        $dropName = $line && $line->dropMikoa
            ? $line->dropMikoa->regional_name
            : ($parcel->mzigo_unapokwenda ?? '');

        $idadiKupakia = $line ? (int) $line->idadi_kupakia : (int) $parcel->idadi_kupakia;

        return [
            'customer_pacel_id' => $parcel->id,
            'customer_pacel_destination_id' => $line ? $line->id : null,
            'mteja' => $parcel->mteja,
            'mpokeaji' => $parcel->mpokeaji,
            'name' => $parcel->name,
            'mzigo_unapokwenda' => $dropName,
            'drop_mikoa_id' => $line ? $line->drop_mikoa_id : $parcel->drop_mikoa_id,
            'cargo_route_id' => $line ? $line->cargo_route_id : $parcel->cargo_route_id,
            'idadi' => $line ? (int) $line->quantity : (int) $parcel->idadi,
            'idadi_kupakia' => $idadiKupakia,
            'bei' => (float) str_replace(',', '', $parcel->bei ?? 0),
            'jumla_kuu' => $jumla,
            'ela_iliyopokelewa' => $received,
            'hela_iliyobaki' => $remaining,
            'reference_no' => $parcel->delivery,
            'created_at' => $parcel->created_at
                ? Carbon::parse($parcel->created_at)->setTimezone('Africa/Nairobi')->toDateTimeString()
                : null,
        ];
    }
}
