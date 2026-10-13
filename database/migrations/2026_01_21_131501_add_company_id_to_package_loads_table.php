<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompanyIdToPackageLoadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('package_loads', function (Blueprint $table) {
            if (!Schema::hasColumn('package_loads', 'company_id')) {
                $table->unsignedBigInteger('company_id')->nullable()->after('user_id');
                $table->index('company_id');
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
        Schema::table('package_loads', function (Blueprint $table) {
            if (Schema::hasColumn('package_loads', 'company_id')) {
                $table->dropIndex(['company_id']);
                $table->dropColumn('company_id');
            }
        });
    }
}
