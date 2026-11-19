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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('character_id')->constrained('characters')->cascadeOnDelete();
            $table->string('title', 150);
            $table->text('description')->nullable();
            $table->enum('difficulty', ['Easy', 'Medium', 'Hard', 'Legendary'])->default('Medium');
            $table->enum('status', ['Active', 'Completed', 'Defeated'])->default('Active');
            $table->string('current_location', 150)->nullable()->default('Gerbang Awal Petualangan');
            $table->text('summary')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['character_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
