<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropDestinationFromDropOffPointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Safely drop destination column if it exists
        if (Schema::hasTable('drop_off_points') && Schema::hasColumn('drop_off_points', 'destination')) {
            Schema::table('drop_off_points', function (Blueprint $table) {
                $table->dropColumn('destination');
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
        // Recreate destination column as nullable string if rollback is needed
        if (Schema::hasTable('drop_off_points') && !Schema::hasColumn('drop_off_points', 'destination')) {
            Schema::table('drop_off_points', function (Blueprint $table) {
                $table->string('destination')->nullable()->after('drop_off_point');
            });
        }
    }
}


