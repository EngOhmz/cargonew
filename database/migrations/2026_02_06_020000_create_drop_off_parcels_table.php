<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDropOffParcelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('drop_off_parcels')) {
            return;
        }

        Schema::create('drop_off_parcels', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('drop_off_point_id');
            $table->unsignedBigInteger('customer_pacel_id');
            $table->unsignedBigInteger('pacel_history_id')->nullable();
            $table->unsignedBigInteger('car_id')->nullable();
            $table->unsignedBigInteger('driver_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->integer('idadi_kushusha'); // quantity offloaded at this drop-off
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('drop_off_parcels');
    }
}


