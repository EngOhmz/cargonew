<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMikoaColumnsToDeliveryAgentsTable extends Migration
{
    /**
     * Source mikoa: default region for all agents (set in app from company's first mikoa).
     * Destination mikoa: optional coverage region (same list as /mikoa/manage).
     */
    public function up()
    {
        if (!Schema::hasTable('delivery_agents')) {
            return;
        }
        Schema::table('delivery_agents', function (Blueprint $table) {
            if (!Schema::hasColumn('delivery_agents', 'source_mikoa_id')) {
                $table->unsignedBigInteger('source_mikoa_id')->nullable()->after('company_id');
            }
            if (!Schema::hasColumn('delivery_agents', 'destination_mikoa_id')) {
                $table->unsignedBigInteger('destination_mikoa_id')->nullable()->after('source_mikoa_id');
            }
        });
    }

    public function down()
    {
        if (!Schema::hasTable('delivery_agents')) {
            return;
        }
        Schema::table('delivery_agents', function (Blueprint $table) {
            if (Schema::hasColumn('delivery_agents', 'destination_mikoa_id')) {
                $table->dropColumn('destination_mikoa_id');
            }
            if (Schema::hasColumn('delivery_agents', 'source_mikoa_id')) {
                $table->dropColumn('source_mikoa_id');
            }
        });
    }
}
