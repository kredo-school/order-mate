<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // categories
        Schema::table('categories', function (Blueprint $table) {
            // 1. 外部キー削除
            $table->dropForeign(['store_id']);
            // 2. カラム名変更
            $table->renameColumn('store_id', 'user_id');
        });

        Schema::table('categories', function (Blueprint $table) {
            // 3. 新しい外部キーを user_id に追加
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // menus
        Schema::table('menus', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
            $table->renameColumn('store_id', 'user_id');
        });

        Schema::table('menus', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // tables
        Schema::table('tables', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
            $table->renameColumn('store_id', 'user_id');
        });

        Schema::table('tables', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // chats
        Schema::table('chats', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
            $table->renameColumn('store_id', 'user_id');
        });

        Schema::table('chats', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // orders
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
            $table->renameColumn('store_id', 'user_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // custom_groups
        Schema::table('custom_groups', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
            $table->renameColumn('store_id', 'user_id');
        });

        Schema::table('custom_groups', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        // categories
        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->renameColumn('user_id', 'store_id');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
        });

        // menus
        Schema::table('menus', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->renameColumn('user_id', 'store_id');
        });

        Schema::table('menus', function (Blueprint $table) {
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
        });

        // tables
        Schema::table('tables', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->renameColumn('user_id', 'store_id');
        });

        Schema::table('tables', function (Blueprint $table) {
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
        });

        // chats
        Schema::table('chats', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->renameColumn('user_id', 'store_id');
        });

        Schema::table('chats', function (Blueprint $table) {
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
        });

        // orders
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->renameColumn('user_id', 'store_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
        });

        // custom_groups
        Schema::table('custom_groups', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->renameColumn('user_id', 'store_id');
        });

        Schema::table('custom_groups', function (Blueprint $table) {
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
        });
    }
};