<?php

// database/migrations/xxxx_xx_xx_xxxxxx_alter_store_id_nullable_in_custom_groups.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('custom_groups', function (Blueprint $table) {
            // 既存FKを一旦外す（名前はデフォルトだと custom_groups_store_id_foreign）
            $table->dropForeign(['store_id']);

            // NULL許可へ変更
            $table->unsignedBigInteger('store_id')->nullable()->change();

            // 外部キーを再作成（親 store 削除時に NULL にする）
            $table->foreign('store_id')
                  ->references('id')->on('stores')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('custom_groups', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
            $table->unsignedBigInteger('store_id')->nullable(false)->change();
            $table->foreign('store_id')
                  ->references('id')->on('stores')
                  ->cascadeOnDelete();
        });
    }
};
