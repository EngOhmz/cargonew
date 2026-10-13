<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTempolaryPacelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Skip if the table already exists
        if (Schema::hasTable('tempolary_pacels')) {
            return;
        }

        Schema::create('tempolary_pacels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('idadi')->default('1');
            $table->string('bei')->nullable();
            $table->string('ela_iliyopokelewa')->default('0')->nullable();
            $table->string('jumla')->default('0')->nullable();
            $table->string('mzigo_unapotoka')->default('#')->nullable();
            $table->string('mzigo_unapokwenda');
            $table->string('receipt');
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
        Schema::dropIfExists('tempolary_pacels');
    }
}
