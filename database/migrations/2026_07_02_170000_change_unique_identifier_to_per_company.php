<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeUniqueIdentifierToPerCompany extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('customers') || !Schema::hasColumn('customers', 'unique_identifier')) {
            return;
        }

        Schema::table('customers', function (Blueprint $table) {
            if ($this->indexExists('customers', 'customers_unique_identifier_unique')) {
                $table->dropUnique('customers_unique_identifier_unique');
            }
        });

        Schema::table('customers', function (Blueprint $table) {
            if (!$this->indexExists('customers', 'customers_company_unique_identifier_unique')) {
                $table->unique(['company_id', 'unique_identifier'], 'customers_company_unique_identifier_unique');
            }
        });
    }

    public function down()
    {
        if (!Schema::hasTable('customers')) {
            return;
        }

        Schema::table('customers', function (Blueprint $table) {
            if ($this->indexExists('customers', 'customers_company_unique_identifier_unique')) {
                $table->dropUnique('customers_company_unique_identifier_unique');
            }
        });

        Schema::table('customers', function (Blueprint $table) {
            if (!$this->indexExists('customers', 'customers_unique_identifier_unique')) {
                $table->unique('unique_identifier', 'customers_unique_identifier_unique');
            }
        });
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $connection = Schema::getConnection();
        $database = $connection->getDatabaseName();

        $result = $connection->select(
            'SELECT COUNT(*) AS cnt FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ?',
            [$database, $table, $indexName]
        );

        return !empty($result) && (int) $result[0]->cnt > 0;
    }
}
