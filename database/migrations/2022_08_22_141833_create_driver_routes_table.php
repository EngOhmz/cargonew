<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDriverRoutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Skip if the table already exists
        if (Schema::hasTable('driver_routes')) {
            return;
        }

        Schema::create('driver_routes', function (Blueprint $table) {
            $table->id();
            $table->string('car_id');
            $table->string('driver_id');
            $table->string('from');
            $table->string('to');
            $table->string('user_id');
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
        Schema::dropIfExists('driver_routes');
    }
}
