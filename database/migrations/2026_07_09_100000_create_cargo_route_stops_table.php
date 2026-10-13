<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('cargo_route_stops')) {
            return;
        }

        Schema::create('cargo_route_stops', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cargo_route_id');
            $table->unsignedBigInteger('mikoa_id');
            $table->unsignedSmallInteger('stop_order')->default(0);
            $table->boolean('is_final')->default(false);
            $table->timestamps();

            $table->index('cargo_route_id');
            $table->unique(
                ['cargo_route_id', 'mikoa_id'],
                'cargo_route_stops_route_mikoa_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cargo_route_stops');
    }
};
