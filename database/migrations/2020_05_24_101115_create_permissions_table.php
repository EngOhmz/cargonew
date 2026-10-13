<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('permissions')){
        Schema::create('permissions', function (Blueprint $table) {
            $table->id('id');
            $table->string('slug');
            $table->unsignedBigInteger('sys_module_id');
            $table->foreign('sys_module_id')->references('id')->on('sys_modules')->onDelete('cascade');
            $table->timestamps();
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
        Schema::dropIfExists('permissions');
    }
}
