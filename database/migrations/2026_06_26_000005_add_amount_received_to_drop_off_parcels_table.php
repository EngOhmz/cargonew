<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAmountReceivedToDropOffParcelsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('drop_off_parcels')) {
            return;
        }

        Schema::table('drop_off_parcels', function (Blueprint $table) {
            if (!Schema::hasColumn('drop_off_parcels', 'amount_received')) {
                $table->decimal('amount_received', 15, 2)->default(0)->after('idadi_kushusha');
            }
        });
    }

    public function down()
    {
        if (!Schema::hasTable('drop_off_parcels')) {
            return;
        }

        Schema::table('drop_off_parcels', function (Blueprint $table) {
            if (Schema::hasColumn('drop_off_parcels', 'amount_received')) {
                $table->dropColumn('amount_received');
            }
        });
    }
}
