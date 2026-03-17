<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSkillOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('skill_orders', function (Blueprint $table) {
            // ID
            $table->id();

            // 購入対象
            $table->foreignId('skill_listing_id')
                ->constrained('skill_listings')
                ->cascadeOnDelete();

            // 購入者（共通ユーザー）
            $table->foreignId('buyer_user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // 決済金額（注文時点のスナップショット）
            $table->unsignedInteger('amount');

            // 決済ステータス：pending / paid / cancelled（※決済処理は別途実装）
            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');

            // 購入日時
            $table->timestamp('purchased_at')->nullable();

            // ==========================
            // 取引（チャット）用ステータス
            // ==========================
            // ステータス：in_progress（取引中）/ delivered（納品済み）/ completed（完了）
            $table->enum('transaction_status', ['in_progress', 'delivered', 'completed'])->default('in_progress');

            // 納品日時
            $table->timestamp('delivered_at')->nullable();

            // 完了日時
            $table->timestamp('completed_at')->nullable();

            // 作成日時・更新日時
            $table->timestamps();

            $table->index(['skill_listing_id', 'buyer_user_id']);
            $table->index(['buyer_user_id', 'transaction_status']);
            $table->index(['skill_listing_id', 'transaction_status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('skill_orders');
    }
}

