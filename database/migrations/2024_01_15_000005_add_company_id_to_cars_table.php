<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompanyIdToCarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cars', function (Blueprint $table) {
            if (!Schema::hasColumn('cars', 'company_id')) {
                $table->unsignedBigInteger('company_id')->nullable()->after('driver_id');
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
        Schema::table('cars', function (Blueprint $table) {
            if (Schema::hasColumn('cars', 'company_id')) {
                $table->dropIndex(['company_id']);
                $table->dropColumn('company_id');
            }
        });
    }
}








