<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHeadOfficeContactsToDocumentSettings extends Migration
{
    public function up()
    {
        Schema::table('document_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('document_settings', 'office_1_contact_2')) {
                $table->string('office_1_contact_2', 100)->nullable()->after('office_1_contact')->comment('Head office contact 2');
            }
            if (!Schema::hasColumn('document_settings', 'office_1_contact_3')) {
                $table->string('office_1_contact_3', 100)->nullable()->after('office_1_contact_2')->comment('Head office contact 3');
            }
        });
    }

    public function down()
    {
        Schema::table('document_settings', function (Blueprint $table) {
            $table->dropColumn(['office_1_contact_2', 'office_1_contact_3']);
        });
    }
}
