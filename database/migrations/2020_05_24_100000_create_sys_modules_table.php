<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSysModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {  
        if(!Schema::hasTable('sys_modules')){
        Schema::create('sys_modules', function (Blueprint $table) {
            $table->id('id');
            $table->string('slug');
            $table->timestamps();
            $table->softDeletes();
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
        Schema::dropIfExists('sys_modules');
    }
}
