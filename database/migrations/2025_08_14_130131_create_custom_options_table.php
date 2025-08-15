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
        Schema::create('custom_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_group_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->decimal('extra_price', 10, 2)->default(0.0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_options');
    }
};
