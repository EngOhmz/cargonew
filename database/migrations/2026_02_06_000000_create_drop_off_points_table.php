<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDropOffPointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('drop_off_points')) {
            Schema::create('drop_off_points', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('car_id');
                $table->unsignedBigInteger('driver_id');
                $table->unsignedBigInteger('driver_route_id')->nullable();
                $table->string('drop_off_point'); // name/label of the point
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('company_id')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('drop_off_points');
    }
}


