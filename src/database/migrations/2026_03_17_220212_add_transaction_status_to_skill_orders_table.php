<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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
            if (!Schema::hasColumn('skill_orders', 'transaction_status')) {
                return;
            }

            // 既存インデックス名が存在する場合は重複作成を避ける
            // ※ MySQL はインデックス名が同一だと重複エラーになる
            $dbName = DB::connection()->getDatabaseName();
            $tableName = 'skill_orders';

            $buyerIndexName = 'skill_orders_buyer_user_id_transaction_status_index';
            $listingIndexName = 'skill_orders_skill_listing_id_transaction_status_index';

            $buyerIndexExists = DB::table('information_schema.statistics')
                ->where('table_schema', $dbName)
                ->where('table_name', $tableName)
                ->where('index_name', $buyerIndexName)
                ->exists();

            $listingIndexExists = DB::table('information_schema.statistics')
                ->where('table_schema', $dbName)
                ->where('table_name', $tableName)
                ->where('index_name', $listingIndexName)
                ->exists();

            if (!$buyerIndexExists) {
                $table->index(['buyer_user_id', 'transaction_status'], $buyerIndexName);
            }
            if (!$listingIndexExists) {
                $table->index(['skill_listing_id', 'transaction_status'], $listingIndexName);
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
