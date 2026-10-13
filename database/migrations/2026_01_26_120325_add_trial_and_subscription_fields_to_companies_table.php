<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTrialAndSubscriptionFieldsToCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            if (!Schema::hasColumn('companies', 'trial_start_at')) {
                $table->dateTime('trial_start_at')->nullable()->after('personalPhone');
            }
            if (!Schema::hasColumn('companies', 'trial_end_at')) {
                $table->dateTime('trial_end_at')->nullable()->after('trial_start_at');
            }
            if (!Schema::hasColumn('companies', 'subscription_start_at')) {
                $table->dateTime('subscription_start_at')->nullable()->after('trial_end_at');
            }
            if (!Schema::hasColumn('companies', 'subscription_end_at')) {
                $table->dateTime('subscription_end_at')->nullable()->after('subscription_start_at');
            }
            if (!Schema::hasColumn('companies', 'is_disabled')) {
                $table->boolean('is_disabled')->default(false)->after('subscription_end_at');
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
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['trial_start_at', 'trial_end_at', 'subscription_start_at', 'subscription_end_at', 'is_disabled']);
        });
    }
}
