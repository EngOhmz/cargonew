<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddStartDateToDriverRoutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('driver_routes')) {
            return;
        }

        Schema::table('driver_routes', function (Blueprint $table) {
            if (!Schema::hasColumn('driver_routes', 'start_date')) {
                $table->date('start_date')->nullable()->after('to');
            }
        });

        if (Schema::hasColumn('driver_routes', 'start_date')) {
            DB::table('driver_routes')
                ->whereNull('start_date')
                ->update(['start_date' => DB::raw('DATE(created_at)')]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasTable('driver_routes')) {
            return;
        }

        Schema::table('driver_routes', function (Blueprint $table) {
            if (Schema::hasColumn('driver_routes', 'start_date')) {
                $table->dropColumn('start_date');
            }
        });
    }
}
