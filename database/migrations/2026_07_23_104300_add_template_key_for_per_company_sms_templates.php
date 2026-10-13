<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddTemplateKeyForPerCompanySmsTemplates extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('sms', 'template_key')) {
            Schema::table('sms', function (Blueprint $table) {
                $table->unsignedTinyInteger('template_key')->nullable()->after('status');
                $table->index(['status', 'company_id', 'template_key'], 'sms_template_lookup_idx');
            });
        }

        // Mark legacy global templates (ids 1-4) as system defaults.
        foreach ([1, 2, 3, 4] as $key) {
            DB::table('sms')
                ->where('id', $key)
                ->where('status', 'template')
                ->update([
                    'template_key' => $key,
                    'company_id' => null,
                    'updated_at' => now(),
                ]);
        }

        // Any other rows already marked template with company_id but no key: leave alone.
    }

    public function down()
    {
        if (Schema::hasColumn('sms', 'template_key')) {
            Schema::table('sms', function (Blueprint $table) {
                $table->dropIndex('sms_template_lookup_idx');
                $table->dropColumn('template_key');
            });
        }
    }
}
