<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddElaIliyopokelewaToCustomerPacelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('customer_pacels') && !Schema::hasColumn('customer_pacels', 'ela_iliyopokelewa')) {
            Schema::table('customer_pacels', function (Blueprint $table) {
                $table->string('ela_iliyopokelewa')->default('0')->nullable()->after('receipt');
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
        if (Schema::hasTable('customer_pacels') && Schema::hasColumn('customer_pacels', 'ela_iliyopokelewa')) {
            Schema::table('customer_pacels', function (Blueprint $table) {
                $table->dropColumn('ela_iliyopokelewa');
            });
        }
    }
}


