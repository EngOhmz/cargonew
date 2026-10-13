<?php

use App\Models\CargoRoute;
use App\Services\CargoRouteService;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('cargo_route_stops')) {
            return;
        }

        $service = app(CargoRouteService::class);

        CargoRoute::query()->each(function (CargoRoute $route) use ($service) {
            $service->syncFinalStop($route);
        });
    }

    public function down(): void
    {
        // Non-destructive backfill — no rollback.
    }
};
