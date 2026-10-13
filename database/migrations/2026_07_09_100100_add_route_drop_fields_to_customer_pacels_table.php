<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('customer_pacels')) {
            return;
        }

        Schema::table('customer_pacels', function (Blueprint $table) {
            if (!Schema::hasColumn('customer_pacels', 'cargo_route_id')) {
                $table->unsignedBigInteger('cargo_route_id')->nullable()->after('mzigo_unapokwenda');
            }
            if (!Schema::hasColumn('customer_pacels', 'drop_mikoa_id')) {
                $table->unsignedBigInteger('drop_mikoa_id')->nullable()->after('cargo_route_id');
            }
        });

        if (!Schema::hasTable('pacel_histories')) {
            return;
        }

        Schema::table('pacel_histories', function (Blueprint $table) {
            if (!Schema::hasColumn('pacel_histories', 'cargo_route_id')) {
                $table->unsignedBigInteger('cargo_route_id')->nullable()->after('mzigo_unapokwenda');
            }
            if (!Schema::hasColumn('pacel_histories', 'drop_mikoa_id')) {
                $table->unsignedBigInteger('drop_mikoa_id')->nullable()->after('cargo_route_id');
            }
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('customer_pacels')) {
            Schema::table('customer_pacels', function (Blueprint $table) {
                if (Schema::hasColumn('customer_pacels', 'drop_mikoa_id')) {
                    $table->dropColumn('drop_mikoa_id');
                }
                if (Schema::hasColumn('customer_pacels', 'cargo_route_id')) {
                    $table->dropColumn('cargo_route_id');
                }
            });
        }

        if (Schema::hasTable('pacel_histories')) {
            Schema::table('pacel_histories', function (Blueprint $table) {
                if (Schema::hasColumn('pacel_histories', 'drop_mikoa_id')) {
                    $table->dropColumn('drop_mikoa_id');
                }
                if (Schema::hasColumn('pacel_histories', 'cargo_route_id')) {
                    $table->dropColumn('cargo_route_id');
                }
            });
        }
    }
};
