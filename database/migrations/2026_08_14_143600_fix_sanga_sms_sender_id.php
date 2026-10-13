<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FixSangaSmsSenderId extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('companies') || !Schema::hasColumn('companies', 'sms_sender_id')) {
            return;
        }

        DB::table('companies')
            ->where(function ($query) {
                $query->where('sms_sender_id', 'SangaCargo')
                    ->orWhereRaw('LOWER(cname) LIKE ?', ['%sanga%'])
                    ->orWhereRaw('LOWER(app_name) LIKE ?', ['%sanga%']);
            })
            ->update(['sms_sender_id' => 'Sanga Cargo']);
    }

    public function down()
    {
        if (!Schema::hasTable('companies') || !Schema::hasColumn('companies', 'sms_sender_id')) {
            return;
        }

        DB::table('companies')
            ->where('sms_sender_id', 'Sanga Cargo')
            ->update(['sms_sender_id' => 'SangaCargo']);
    }
}
