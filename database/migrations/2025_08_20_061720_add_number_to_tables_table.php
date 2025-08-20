<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->unsignedInteger('number')->after('uuid');
            $table->unique(['store_id', 'number']);
        });
    }

    public function down(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->dropUnique(['store_id', 'number']);
            $table->dropColumn('number');
        });
    }
};