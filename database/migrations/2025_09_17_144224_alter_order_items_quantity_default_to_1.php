<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // quantity を NOT NULL DEFAULT 1 に変更
        DB::statement("ALTER TABLE `order_items` MODIFY COLUMN `quantity` INT NOT NULL DEFAULT 1");
    }

    public function down(): void
    {
        // 元に戻す（NULL 許容 or DEFAULT 無し にしたければ編集）
        DB::statement("ALTER TABLE `order_items` MODIFY COLUMN `quantity` INT NOT NULL");
    }
};
