<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ExtendSkillOrdersForStripeCheckout extends Migration
{
    public function up()
    {
        Schema::table('skill_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('skill_orders', 'payment_type')) {
                $table->enum('payment_type', ['escrow', 'instant'])
                    ->default('escrow')
                    ->after('status');
            }
            if (!Schema::hasColumn('skill_orders', 'payout_status')) {
                $table->enum('payout_status', ['not_transferred', 'transferred', 'failed'])
                    ->default('not_transferred')
                    ->after('transaction_status');
            }
            if (!Schema::hasColumn('skill_orders', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('purchased_at');
            }
            if (!Schema::hasColumn('skill_orders', 'checkout_cancelled_at')) {
                $table->timestamp('checkout_cancelled_at')->nullable()->after('completed_at');
            }
            if (!Schema::hasColumn('skill_orders', 'stripe_checkout_session_id')) {
                $table->string('stripe_checkout_session_id')->nullable()->after('checkout_cancelled_at');
            }
            if (!Schema::hasColumn('skill_orders', 'stripe_payment_intent_id')) {
                $table->string('stripe_payment_intent_id')->nullable()->after('stripe_checkout_session_id');
            }
            if (!Schema::hasColumn('skill_orders', 'stripe_charge_id')) {
                $table->string('stripe_charge_id')->nullable()->after('stripe_payment_intent_id');
            }
            if (!Schema::hasColumn('skill_orders', 'stripe_webhook_event_id')) {
                $table->string('stripe_webhook_event_id')->nullable()->after('stripe_charge_id');
            }
            if (!Schema::hasColumn('skill_orders', 'last_webhook_type')) {
                $table->string('last_webhook_type')->nullable()->after('stripe_webhook_event_id');
            }
            if (!Schema::hasColumn('skill_orders', 'last_webhook_received_at')) {
                $table->timestamp('last_webhook_received_at')->nullable()->after('last_webhook_type');
            }
            if (!Schema::hasColumn('skill_orders', 'stripe_transfer_id')) {
                $table->string('stripe_transfer_id')->nullable()->after('last_webhook_received_at');
            }
            if (!Schema::hasColumn('skill_orders', 'transferred_at')) {
                $table->timestamp('transferred_at')->nullable()->after('stripe_transfer_id');
            }
            if (!Schema::hasColumn('skill_orders', 'transfer_attempts')) {
                $table->unsignedInteger('transfer_attempts')->default(0)->after('transferred_at');
            }
            if (!Schema::hasColumn('skill_orders', 'last_transfer_error')) {
                $table->text('last_transfer_error')->nullable()->after('transfer_attempts');
            }
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE skill_orders MODIFY transaction_status ENUM('waiting_payment','in_progress','delivered','completed') NOT NULL DEFAULT 'waiting_payment'");
        }

        Schema::table('skill_orders', function (Blueprint $table) {
            $table->index('status');
            $table->index('payment_type');
            $table->index('payout_status');
            $table->index('stripe_checkout_session_id');
            $table->unique('stripe_webhook_event_id');
            $table->index('stripe_transfer_id');
        });
    }

    public function down()
    {
        Schema::table('skill_orders', function (Blueprint $table) {
            $table->dropUnique(['stripe_webhook_event_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['payment_type']);
            $table->dropIndex(['payout_status']);
            $table->dropIndex(['stripe_checkout_session_id']);
            $table->dropIndex(['stripe_transfer_id']);

            $table->dropColumn([
                'payment_type',
                'payout_status',
                'paid_at',
                'checkout_cancelled_at',
                'stripe_checkout_session_id',
                'stripe_payment_intent_id',
                'stripe_charge_id',
                'stripe_webhook_event_id',
                'last_webhook_type',
                'last_webhook_received_at',
                'stripe_transfer_id',
                'transferred_at',
                'transfer_attempts',
                'last_transfer_error',
            ]);
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE skill_orders MODIFY transaction_status ENUM('in_progress','delivered','completed') NOT NULL DEFAULT 'in_progress'");
        }
    }
}
