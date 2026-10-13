<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsDestinationToDropOffParcelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('drop_off_parcels') && !Schema::hasColumn('drop_off_parcels', 'is_destination')) {
            Schema::table('drop_off_parcels', function (Blueprint $table) {
                $table->boolean('is_destination')->default(0)->after('idadi_kushusha');
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
        if (Schema::hasTable('drop_off_parcels') && Schema::hasColumn('drop_off_parcels', 'is_destination')) {
            Schema::table('drop_off_parcels', function (Blueprint $table) {
                $table->dropColumn('is_destination');
            });
        }
    }
}


