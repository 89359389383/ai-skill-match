<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            // ID
            $table->id();
            
            // ユーザーID（外部キー）
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // 企業名
            $table->string('name');

            // 会社概要
            $table->text('overview')->nullable();

            // 連絡先名
            $table->string('contact_name')->nullable();
            
            // 部署名
            $table->string('department')->nullable();

            // 自己紹介
            $table->text('introduction')->nullable();

            // 企業アイコン（必須化するため列を追加）
            $table->string('icon_path')->nullable();
            
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
        Schema::dropIfExists('companies');
    }
}