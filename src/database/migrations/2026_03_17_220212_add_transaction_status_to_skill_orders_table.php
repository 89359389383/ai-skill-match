<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTransactionStatusToSkillOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('skill_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('skill_orders', 'transaction_status')) {
                $table->enum('transaction_status', ['in_progress', 'delivered', 'completed'])
                    ->default('in_progress')
                    ->after('purchased_at');
            }
            if (!Schema::hasColumn('skill_orders', 'delivered_at')) {
                $table->timestamp('delivered_at')->nullable()->after('transaction_status');
            }
            if (!Schema::hasColumn('skill_orders', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('delivered_at');
            }
        });

        // インデックス追加（Laravel 11 では Doctrine 非依存のため、直接追加）
        Schema::table('skill_orders', function (Blueprint $table) {
            if (Schema::hasColumn('skill_orders', 'transaction_status')) {
                $table->index(['buyer_user_id', 'transaction_status']);
                $table->index(['skill_listing_id', 'transaction_status']);
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
        Schema::table('skill_orders', function (Blueprint $table) {
            $table->dropIndex(['buyer_user_id', 'transaction_status']);
            $table->dropIndex(['skill_listing_id', 'transaction_status']);
            $table->dropColumn(['transaction_status', 'delivered_at', 'completed_at']);
        });
    }
}
