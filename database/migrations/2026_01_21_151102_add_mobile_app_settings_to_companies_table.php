<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMobileAppSettingsToCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            if (!Schema::hasColumn('companies', 'app_name')) {
                $table->string('app_name')->nullable()->after('cname');
            }
            if (!Schema::hasColumn('companies', 'app_logo')) {
                $table->string('app_logo')->nullable()->after('app_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'app_logo')) {
                $table->dropColumn('app_logo');
            }
            if (Schema::hasColumn('companies', 'app_name')) {
                $table->dropColumn('app_name');
            }
        });
    }
}
