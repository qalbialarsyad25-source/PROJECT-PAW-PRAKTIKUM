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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('slug', 140)->unique();
            $table->text('description');
            $table->enum('category', ['weapon', 'armor', 'potion', 'equipment', 'quest'])->default('equipment');
            $table->string('effect_type', 50)->nullable(); // heal, buff_str, buff_dex, buff_hp, key_item
            $table->integer('effect_value')->nullable(); // e.g. 15 for healing 15 HP
            $table->string('icon', 50)->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
