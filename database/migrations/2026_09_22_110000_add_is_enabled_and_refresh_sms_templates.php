<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddIsEnabledAndRefreshSmsTemplates extends Migration
{
    public function up()
    {
        if (Schema::hasTable('sms') && !Schema::hasColumn('sms', 'is_enabled')) {
            Schema::table('sms', function (Blueprint $table) {
                $table->boolean('is_enabled')->default(true)->after('template_key');
            });
        }

        if (!Schema::hasTable('sms') || !Schema::hasColumn('sms', 'template_key')) {
            return;
        }

        $messages = [
            1 => 'Habari. [Company] imepokea mzigo wako kutoka [Supplier]. Idadi: [Packages]. Namba ya kufuatilia (REF): [Ref]. Tutakujulisha mzigo ukifika. Asante.',
            2 => 'Mzigo wako umepakiwa na uko njiani. Gari: [Vehicle]. Idadi: [Packages].',
            3 => 'Habari. Mzigo wako umefika [Destination] ([Company]). Idadi: [Packages]. Kiasi kinachohitajika: TZS [Amount]. Tafadhali fika kuchukua au wasiliana na ofisi. Asante.',
            4 => 'Malipo yako ya TZS [Amount] yamethibitishwa. Asante kwa kutumia [Company].',
        ];

        // Active: 1 (umepokelewa), 3 (umefika). Disabled but kept: 2, 4.
        foreach ([1, 2, 3, 4] as $key) {
            $enabled = in_array($key, [1, 3], true) ? 1 : 0;
            DB::table('sms')
                ->where('status', 'template')
                ->where('template_key', $key)
                ->update([
                    'is_enabled' => $enabled,
                    'message' => $messages[$key],
                    'updated_at' => now(),
                ]);
        }
    }

    public function down()
    {
        if (Schema::hasTable('sms') && Schema::hasColumn('sms', 'is_enabled')) {
            Schema::table('sms', function (Blueprint $table) {
                $table->dropColumn('is_enabled');
            });
        }
    }
}
