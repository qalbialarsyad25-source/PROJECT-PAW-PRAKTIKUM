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
        Schema::create('story_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('campaigns')->cascadeOnDelete();
            $table->foreignId('character_id')->constrained('characters')->cascadeOnDelete();
            $table->enum('sender_type', ['player', 'dm', 'system'])->default('dm');
            $table->text('player_action')->nullable();
            $table->longText('narrative');
            $table->string('action_result', 40)->nullable(); // success, failure, critical_success, critical_failure, partial_success, info
            $table->text('reasoning')->nullable();
            $table->json('ai_response_json')->nullable();
            $table->integer('hp_change')->default(0);
            $table->integer('xp_change')->default(0);
            $table->timestamps();

            $table->index(['campaign_id', 'created_at']);
            $table->index(['character_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('story_histories');
    }
};
