<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKanuniToDocumentSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('document_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('document_settings', 'kanuni_1')) {
                $table->text('kanuni_1')->nullable();
            }
            if (!Schema::hasColumn('document_settings', 'kanuni_2')) {
                $table->text('kanuni_2')->nullable();
            }
            if (!Schema::hasColumn('document_settings', 'kanuni_3')) {
                $table->text('kanuni_3')->nullable();
            }
            if (!Schema::hasColumn('document_settings', 'kanuni_4')) {
                $table->text('kanuni_4')->nullable();
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
        Schema::table('document_settings', function (Blueprint $table) {
            $table->dropColumn(['kanuni_1', 'kanuni_2', 'kanuni_3', 'kanuni_4']);
        });
    }
}
