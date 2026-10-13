<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueColToCustomerPacelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('customer_pacels')) {
            return;
        }

        Schema::table('customer_pacels', function (Blueprint $table) {
            if (!Schema::hasColumn('customer_pacels', 'unique_col')) {
                $table->string('unique_col')->nullable()->after('receipt');
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
        if (!Schema::hasTable('customer_pacels')) {
            return;
        }

        Schema::table('customer_pacels', function (Blueprint $table) {
            if (Schema::hasColumn('customer_pacels', 'unique_col')) {
                $table->dropColumn('unique_col');
            }
        });
    }
}
