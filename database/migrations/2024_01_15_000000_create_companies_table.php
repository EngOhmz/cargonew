<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('cname');
            $table->unsignedBigInteger('mandatory_id')->nullable();
            $table->unsignedBigInteger('companyDeclarartion_id')->nullable();
            $table->string('organization')->nullable();
            $table->date('regDate')->nullable();
            $table->string('regNo')->nullable();
            $table->string('employeeNo')->nullable();
            $table->string('regHead')->nullable();
            $table->string('employeeBox')->nullable();
            $table->string('tinNo')->nullable();
            $table->string('phone')->nullable();
            $table->string('contactName')->nullable();
            $table->string('email')->nullable();
            $table->string('personalPhone')->nullable();
            $table->timestamps();
            
            $table->index('mandatory_id');
            $table->index('companyDeclarartion_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('companies');
    }
}








