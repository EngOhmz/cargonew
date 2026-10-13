<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParcelCarAssignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('parcel_car_assignments')) {
            Schema::create('parcel_car_assignments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('customer_id');
                $table->unsignedBigInteger('car_id');
                $table->unsignedBigInteger('pacel_id');
                $table->integer('idadi')->default(0);
                $table->unsignedBigInteger('company_id')->nullable();
                $table->timestamps();

                // Index for faster lookups
                $table->index(['customer_id', 'car_id', 'pacel_id']);
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
        Schema::dropIfExists('parcel_car_assignments');
    }
}
