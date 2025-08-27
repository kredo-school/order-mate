<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // chats に store_id を追加
        Schema::table('chats', function (Blueprint $table) {
            $table->foreignId('store_id')->nullable()->constrained()->onDelete('cascade');
        });

        // messages から sender_type を削除
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn('sender_type');
        });
    }

    public function down(): void
    {
        // ロールバック用
        Schema::table('chats', function (Blueprint $table) {
            $table->dropConstrainedForeignId('store_id');
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->string('sender_type')->nullable();
        });
    }
};
