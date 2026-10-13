<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentSettingsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('document_settings')) {
            Schema::create('document_settings', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('company_id')->nullable();
                $table->string('header_line1', 255)->nullable()->comment('Tunasafirisha parcel kutoka e.g. TUNASAFRISHA MIZIGO KUTOKA DAR ES SALAAM');
                $table->string('header_line2', 255)->nullable()->comment('Kwenda destinations e.g. KWENDA NZEGA,KAHAMA,ISAKA,MASUMBWE, NA LUGUNGA');
                $table->string('office_1_label', 100)->nullable()->comment('e.g. HEAD OFFICE');
                $table->string('office_1_place', 100)->nullable()->comment('e.g. DAR ES SALAAM');
                $table->string('office_1_contact', 100)->nullable()->comment('Phone/contact');
                $table->string('office_2_label', 100)->nullable();
                $table->string('office_2_place', 100)->nullable();
                $table->string('office_2_contact', 100)->nullable();
                $table->string('office_3_label', 100)->nullable();
                $table->string('office_3_place', 100)->nullable();
                $table->string('office_3_contact', 100)->nullable();
                $table->timestamps();
                $table->index('company_id');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('document_settings');
    }
}
