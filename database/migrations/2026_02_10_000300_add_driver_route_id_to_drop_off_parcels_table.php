<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDriverRouteIdToDropOffParcelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('drop_off_parcels') && !Schema::hasColumn('drop_off_parcels', 'driver_route_id')) {
            Schema::table('drop_off_parcels', function (Blueprint $table) {
                $table->unsignedBigInteger('driver_route_id')->nullable()->after('drop_off_point_id');
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
        if (Schema::hasTable('drop_off_parcels') && Schema::hasColumn('drop_off_parcels', 'driver_route_id')) {
            Schema::table('drop_off_parcels', function (Blueprint $table) {
                $table->dropColumn('driver_route_id');
            });
        }
    }
}


