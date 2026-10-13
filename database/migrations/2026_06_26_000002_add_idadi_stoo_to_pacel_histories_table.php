<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddIdadiStooToPacelHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('pacel_histories')) {
            return;
        }

        Schema::table('pacel_histories', function (Blueprint $table) {
            if (!Schema::hasColumn('pacel_histories', 'idadi_stoo')) {
                $table->string('idadi_stoo')->nullable()->after('idadi');
            }
        });

        if (Schema::hasColumn('pacel_histories', 'status')) {
            DB::statement("ALTER TABLE pacel_histories MODIFY status VARCHAR(255) NOT NULL DEFAULT 'active'");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasTable('pacel_histories')) {
            return;
        }

        Schema::table('pacel_histories', function (Blueprint $table) {
            if (Schema::hasColumn('pacel_histories', 'idadi_stoo')) {
                $table->dropColumn('idadi_stoo');
            }
        });
    }
}
