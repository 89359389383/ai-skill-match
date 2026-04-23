<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRefundColumnsToSkillOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('skill_orders', function (Blueprint $table) {
            $table->string('refund_status')->default('none')->after('payout_status')->comment('none, requested, refunding, refunded, failed, partial_refunded');
            $table->string('stripe_refund_id')->nullable()->after('stripe_transfer_id');
            $table->timestamp('refunded_at')->nullable()->after('completed_at');
            $table->timestamp('transfer_reversed_at')->nullable()->after('refunded_at');
            $table->integer('reversal_amount')->nullable()->after('transfer_reversed_at');
            $table->string('dispute_status')->default('none')->after('reversal_amount')->comment('none, needs_response, won, lost');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('skill_orders', function (Blueprint $table) {
            $table->dropColumn([
                'refund_status',
                'stripe_refund_id',
                'refunded_at',
                'transfer_reversed_at',
                'reversal_amount',
                'dispute_status',
            ]);
        });
    }
}
