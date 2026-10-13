<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddIndexesToPacelHistoriesForManifest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pacel_histories', function (Blueprint $table) {
            // Add composite index for car_manifest queries
            // This index optimizes: WHERE car_id = X AND created_at BETWEEN Y AND Z AND activity = 'kupakia' AND idadi_kupakia != '0'
            if (!$this->indexExists('pacel_histories', 'pacel_histories_car_created_activity_index')) {
                $table->index(['car_id', 'created_at', 'activity', 'idadi_kupakia'], 'pacel_histories_car_created_activity_index');
            }
            
            // Add index for pacel_id lookups
            if (!$this->indexExists('pacel_histories', 'pacel_histories_pacel_id_activity_index')) {
                $table->index(['pacel_id', 'activity'], 'pacel_histories_pacel_id_activity_index');
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
        Schema::table('pacel_histories', function (Blueprint $table) {
            if ($this->indexExists('pacel_histories', 'pacel_histories_car_created_activity_index')) {
                $table->dropIndex('pacel_histories_car_created_activity_index');
            }
            if ($this->indexExists('pacel_histories', 'pacel_histories_pacel_id_activity_index')) {
                $table->dropIndex('pacel_histories_pacel_id_activity_index');
            }
        });
    }

    /**
     * Check if index exists
     */
    private function indexExists($table, $indexName)
    {
        try {
            $result = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);
            return !empty($result);
        } catch (\Exception $e) {
            return false;
        }
    }
}


