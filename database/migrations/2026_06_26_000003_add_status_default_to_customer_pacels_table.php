<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddStatusDefaultToCustomerPacelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('customer_pacels') || !Schema::hasColumn('customer_pacels', 'status')) {
            return;
        }

        DB::statement("ALTER TABLE customer_pacels MODIFY status VARCHAR(255) NOT NULL DEFAULT 'active'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No safe rollback for default change without doctrine/dbal.
    }
}
