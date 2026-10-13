<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DisableAllSmsTemplates extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('sms') || !Schema::hasColumn('sms', 'is_enabled')) {
            return;
        }

        DB::table('sms')
            ->where('status', 'template')
            ->whereIn('template_key', [1, 2, 3, 4])
            ->update([
                'is_enabled' => 0,
                'updated_at' => now(),
            ]);
    }

    public function down()
    {
        if (!Schema::hasTable('sms') || !Schema::hasColumn('sms', 'is_enabled')) {
            return;
        }

        DB::table('sms')
            ->where('status', 'template')
            ->whereIn('template_key', [1, 3])
            ->update([
                'is_enabled' => 1,
                'updated_at' => now(),
            ]);
    }
}
