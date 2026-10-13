<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddParentIdToSysModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sys_modules', function (Blueprint $table) {
            if (!Schema::hasColumn('sys_modules', 'parent_id')) {
                $table->unsignedBigInteger('parent_id')->nullable()->after('id');
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
        Schema::table('sys_modules', function (Blueprint $table) {
            $table->dropColumn('parent_id');
        });
    }
}
