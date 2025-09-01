<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            // 既存のユニーク制約を削除
            $table->dropUnique('categories_name_unique');

            // name + user_id の複合ユニークを設定
            $table->unique(['name', 'user_id']);
        });
    }

    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            // 複合ユニークを削除
            $table->dropUnique('categories_name_user_id_unique');

            // name のユニーク制約を復活
            $table->unique('name');
        });
    }
};