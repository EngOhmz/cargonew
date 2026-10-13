<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompanyIdToUsersCarsSelectionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_cars_selection', function (Blueprint $table) {
            if (!Schema::hasColumn('users_cars_selection', 'company_id')) {
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
        Schema::table('users_cars_selection', function (Blueprint $table) {
            if (Schema::hasColumn('users_cars_selection', 'company_id')) {
                $table->dropIndex(['company_id']);
                $table->dropColumn('company_id');
            }
        });
    }
}
