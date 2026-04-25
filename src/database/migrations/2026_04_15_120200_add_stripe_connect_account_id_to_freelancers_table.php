<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStripeConnectAccountIdToFreelancersTable extends Migration
{
    public function up()
    {
        Schema::table('freelancers', function (Blueprint $table) {
            if (!Schema::hasColumn('freelancers', 'stripe_connect_account_id')) {
                $table->string('stripe_connect_account_id')->nullable()->after('icon_path');
            }
        });
    }

    public function down()
    {
        Schema::table('freelancers', function (Blueprint $table) {
            $table->dropColumn(['stripe_connect_account_id']);
        });
    }
}
