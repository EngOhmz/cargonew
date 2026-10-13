<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProviderToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('users')){
        Schema::table('users', function (Blueprint $table) {
            $table->string('provider')->nullable();
            $table->string('provider_id')->nullable();
        });
    }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         // drop the keys
    // Schema::table('users', function (Blueprint $table) {
    //     $table->dropForeign(['provider']);
    //     $table->dropForeign(['provider_id']);
    // });
        // Schema::table('users', function (Blueprint $table) {
        //     $table->dropColumn('provider');
        //     $table->dropColumn('provider_id');
        // });

        // Schema::dropIfExists('role_user');
    }
}
