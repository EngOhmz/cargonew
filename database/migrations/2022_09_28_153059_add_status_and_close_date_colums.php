<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusAndCloseDateColums extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('driver_routes', function (Blueprint $table) {
            // Add columns only if they do not already exist
            if (!Schema::hasColumn('driver_routes', 'status')) {
                $table->string('status')->comment('1-arrived, 2- start pack, 3- close, 4- offload')->nullable();
            }
            if (!Schema::hasColumn('driver_routes', 'closeDate')) {
                $table->string('closeDate')->default('0000-00-00')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('driver_routes', function (Blueprint $table) {
            //
            $table->dropColumn(['status', 'closeDate']);
        });
    }
}
