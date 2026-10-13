<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddSmsSenderIdToCompaniesTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('companies')) {
            return;
        }

        if (!Schema::hasColumn('companies', 'sms_sender_id')) {
            Schema::table('companies', function (Blueprint $table) {
                $table->string('sms_sender_id', 11)->nullable()->after('app_name');
            });
        }

        DB::table('companies')
            ->where(function ($query) {
                $query->whereRaw('LOWER(cname) LIKE ?', ['%sanga%'])
                    ->orWhereRaw('LOWER(app_name) LIKE ?', ['%sanga%']);
            })
            ->update(['sms_sender_id' => 'Sanga Cargo']);
    }

    public function down()
    {
        if (Schema::hasTable('companies') && Schema::hasColumn('companies', 'sms_sender_id')) {
            Schema::table('companies', function (Blueprint $table) {
                $table->dropColumn('sms_sender_id');
            });
        }
    }
}
