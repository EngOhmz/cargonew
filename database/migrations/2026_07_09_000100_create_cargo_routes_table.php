<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('cargo_routes')) {
            return;
        }

        Schema::create('cargo_routes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('from_mikoa_id');
            $table->unsignedBigInteger('to_mikoa_id');
            $table->timestamps();

            $table->index('company_id');
            $table->unique(
                ['company_id', 'from_mikoa_id', 'to_mikoa_id'],
                'cargo_routes_company_from_to_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cargo_routes');
    }
};
