<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMikoaTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('mikoa')) {
            Schema::create('mikoa', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('company_id')->nullable()->index();
                $table->string('regional_name', 255);
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('mikoa');
    }
}
