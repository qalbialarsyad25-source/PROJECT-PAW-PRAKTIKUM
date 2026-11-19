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
        Schema::table('campaigns', function (Blueprint $table) {
            $table->unsignedInteger('current_hp')->nullable()->after('status');
            $table->unsignedInteger('max_hp')->nullable()->after('current_hp');
            $table->unsignedInteger('level')->default(1)->after('max_hp');
            $table->unsignedInteger('experience')->default(0)->after('level');
            $table->string('character_status', 30)->default('Alive')->after('experience');
        });

        Schema::table('inventories', function (Blueprint $table) {
            $table->foreignId('campaign_id')->nullable()->after('id')->constrained('campaigns')->cascadeOnDelete();
            
            // Drop old unique index if exists
            $table->dropUnique(['character_id', 'item_id']);
            // Add new unique index scoped to campaign
            $table->unique(['campaign_id', 'character_id', 'item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->dropUnique(['campaign_id', 'character_id', 'item_id']);
            $table->dropForeign(['campaign_id']);
            $table->dropColumn('campaign_id');
            $table->unique(['character_id', 'item_id']);
        });

        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn(['current_hp', 'max_hp', 'level', 'experience', 'character_status']);
        });
    }
};
