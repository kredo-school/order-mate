<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn('allergy'); // 古いカラム削除
            $table->json('allergens')->nullable()->after('tag'); // 新しいカラム追加
        });
    }

    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn('allergens');
            $table->string('allergy')->nullable()->after('tag');
        });
    }
};

