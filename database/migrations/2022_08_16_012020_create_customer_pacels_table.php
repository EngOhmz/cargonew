<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerPacelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Skip if the table already exists
        if (Schema::hasTable('customer_pacels')) {
            return;
        }

        Schema::create('customer_pacels', function (Blueprint $table) {
            $table->id();
            $table->string('mteja');
            $table->string('mpokeaji');
            $table->string('name');
            $table->string('idadi')->default('1');
            $table->string('idadi_stoo')->nullable();
            $table->string('bei')->nullable();
            $table->string('receipt');
            $table->string('ela_iliyopokelewa')->default('0')->nullable();
            $table->string('jumla')->default('0')->nullable();
            $table->string('activity');
            $table->string('mzigo_unapotoka');
            $table->string('mzigo_unapokwenda');
            $table->string('car_id')->nullable();
            $table->string('customer_id')->nullable();
            $table->string('driver_id')->nullable();
            $table->string('idadi_kupakia')->default('0');
            $table->string('idadi_shusha')->default('0');
            $table->string('delivery')->nullable();
            $table->string('hashtag')->nullable();
            $table->string('status');
            $table->integer('added_by');
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
        Schema::dropIfExists('customer_pacels');
    }
}
