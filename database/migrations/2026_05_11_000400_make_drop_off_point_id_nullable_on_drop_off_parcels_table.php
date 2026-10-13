<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MakeDropOffPointIdNullableOnDropOffParcelsTable extends Migration
{
    /**
     * Destination-only routes may record drops without a drop_off_points row.
     */
    public function up()
    {
        if (!Schema::hasTable('drop_off_parcels') || !Schema::hasColumn('drop_off_parcels', 'drop_off_point_id')) {
            return;
        }

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE drop_off_parcels MODIFY drop_off_point_id BIGINT UNSIGNED NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        if (!Schema::hasTable('drop_off_parcels') || !Schema::hasColumn('drop_off_parcels', 'drop_off_point_id')) {
            return;
        }

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE drop_off_parcels MODIFY drop_off_point_id BIGINT UNSIGNED NOT NULL');
        }
    }
}
