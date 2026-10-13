<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Skip if the table already exists to avoid "table already exists" error
        if (Schema::hasTable('cars')) {
            return;
        }

        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->string('carNumber');
            $table->string('driver_id')->nullable();
            $table->date('closeDate')->nullable();
            $table->string('status')->comment('1-arrived, 2- start pack, 3- close, 4- offload');
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
        Schema::dropIfExists('cars');
    }
}
