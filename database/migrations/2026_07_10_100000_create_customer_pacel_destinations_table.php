<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerPacelDestinationsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('customer_pacel_destinations')) {
            return;
        }

        Schema::create('customer_pacel_destinations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_pacel_id');
            $table->unsignedBigInteger('cargo_route_id')->nullable();
            $table->unsignedBigInteger('drop_mikoa_id');
            $table->unsignedInteger('quantity')->default(0);
            $table->unsignedInteger('idadi_kupakia')->default(0);
            $table->unsignedInteger('idadi_shusha')->default(0);
            $table->unsignedInteger('stop_order')->default(0);
            $table->unsignedBigInteger('company_id')->nullable();
            $table->timestamps();

            $table->index(['customer_pacel_id', 'drop_mikoa_id'], 'cpd_pacel_mikoa_idx');
            $table->index(['company_id', 'drop_mikoa_id'], 'cpd_company_mikoa_idx');
        });
    }

    public function down()
    {
        Schema::dropIfExists('customer_pacel_destinations');
    }
}
