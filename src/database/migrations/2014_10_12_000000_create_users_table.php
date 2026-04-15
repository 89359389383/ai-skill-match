<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            // ID
            $table->id();
            
            // メールアドレス
            $table->string('email')->unique();
            
            // メール認証日時
            $table->timestamp('email_verified_at')->nullable();
            
            // パスワード
            $table->string('password');
            
            // ロール（企業/フリーランサー/購入者）
            $table->enum('role', ['company', 'freelancer', 'buyer']);
            
            // ログイン記憶用トークン
            $table->rememberToken();
            
            // 作成日時・更新日時
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}