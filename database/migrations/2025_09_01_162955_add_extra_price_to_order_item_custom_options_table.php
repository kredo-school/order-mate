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
        Schema::table('order_item_custom_options', function (Blueprint $table) {
            $table->integer('extra_price')->default(0)->after('quantity');
        });
    }
    
    public function down(): void
    {
        Schema::table('order_item_custom_options', function (Blueprint $table) {
            $table->dropColumn('extra_price');
        });
    }
    
};
