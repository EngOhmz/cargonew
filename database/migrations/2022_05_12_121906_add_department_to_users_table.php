<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDepartmentToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Guard each column so we don't try to add it if it already exists
        Schema::table('users', function (Blueprint $table) {
            $table->after('password', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'department_id')) {
                    $table->string('department_id')->nullable();
                }
                if (!Schema::hasColumn('users', 'designation_id')) {
                    $table->string('designation_id')->nullable();
                }
                if (!Schema::hasColumn('users', 'member_id')) {
                    $table->integer('member_id')->nullable();
                }
                if (!Schema::hasColumn('users', 'visitor_id')) {
                    $table->integer('visitor_id')->nullable();
                }
                if (!Schema::hasColumn('users', 'is_active')) {
                    $table->integer('is_active')->default('0');
                }
                if (!Schema::hasColumn('users', 'provider')) {
                    $table->string('provider')->nullable();
                }
                if (!Schema::hasColumn('users', 'provider_id')) {
                    $table->string('provider_id')->nullable();
                }
                if (!Schema::hasColumn('users', 'added_by')) {
                    $table->integer('added_by')->nullable();
                }
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    //     Schema::table('users', function (Blueprint $table) {
    //         $table->dropColumn('department_id');
    //         $table->dropColumn('designation_id');
    //     });
    }
}
