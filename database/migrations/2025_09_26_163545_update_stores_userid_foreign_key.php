<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            // 既存の外部キーを削除
            $table->dropForeign(['user_id']);

            // 外部キーを再作成して onDelete('cascade') を追加
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            // cascadeを削除して元の状態に戻す
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')
                ->references('id')->on('users');
        });
    }
};
