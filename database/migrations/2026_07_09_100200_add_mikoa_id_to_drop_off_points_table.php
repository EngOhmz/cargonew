<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('drop_off_points')) {
            return;
        }

        Schema::table('drop_off_points', function (Blueprint $table) {
            if (!Schema::hasColumn('drop_off_points', 'mikoa_id')) {
                $table->unsignedBigInteger('mikoa_id')->nullable()->after('drop_off_point');
            }
            if (!Schema::hasColumn('drop_off_points', 'stop_order')) {
                $table->unsignedSmallInteger('stop_order')->default(0)->after('mikoa_id');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('drop_off_points')) {
            return;
        }

        Schema::table('drop_off_points', function (Blueprint $table) {
            if (Schema::hasColumn('drop_off_points', 'stop_order')) {
                $table->dropColumn('stop_order');
            }
            if (Schema::hasColumn('drop_off_points', 'mikoa_id')) {
                $table->dropColumn('mikoa_id');
            }
        });
    }
};
