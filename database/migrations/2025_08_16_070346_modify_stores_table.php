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
        // 既存テーブルを変更
        Schema::table('stores', function (Blueprint $table) {
            // 既存カラムを nullable に変更
            $table->text('address')->nullable()->change();
            $table->string('phone')->nullable()->unique()->change();
            $table->string('store_photo')->nullable()->change();
            $table->text('open_hours')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            // 元に戻す場合（nullable 解除）
            $table->text('address')->nullable(false)->change();
            $table->string('phone')->nullable(false)->change();
            $table->string('store_photo')->nullable(false)->change();
            $table->text('open_hours')->nullable(false)->change();
        });
    }
};
