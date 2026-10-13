<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car_history', function (Blueprint $table) {
            $table->id();
            $table->string('car_id');
            $table->string('driver_id');
            $table->string('status')->comment('1-arrived, 2- start pack, 3- close, 4- offload');
            $table->string('startPacking');
            $table->string('endPacking')->default('0000-00-00')->nullable();
            $table->string('user_id')->nullable();
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
        Schema::dropIfExists('car_history');
    }
}
