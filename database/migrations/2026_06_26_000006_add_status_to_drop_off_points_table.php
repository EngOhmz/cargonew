<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToDropOffPointsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('drop_off_points')) {
            return;
        }

        Schema::table('drop_off_points', function (Blueprint $table) {
            if (!Schema::hasColumn('drop_off_points', 'status')) {
                $table->unsignedTinyInteger('status')->default(0)->after('drop_off_point');
            }
        });
    }

    public function down()
    {
        if (!Schema::hasTable('drop_off_points')) {
            return;
        }

        Schema::table('drop_off_points', function (Blueprint $table) {
            if (Schema::hasColumn('drop_off_points', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
}
