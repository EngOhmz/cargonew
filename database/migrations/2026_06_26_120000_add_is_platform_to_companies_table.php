<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsPlatformToCompaniesTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('companies')) {
            return;
        }

        if (!Schema::hasColumn('companies', 'is_platform')) {
            Schema::table('companies', function (Blueprint $table) {
                $table->boolean('is_platform')->default(false)->after('is_disabled');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('companies') && Schema::hasColumn('companies', 'is_platform')) {
            Schema::table('companies', function (Blueprint $table) {
                $table->dropColumn('is_platform');
            });
        }
    }
}
