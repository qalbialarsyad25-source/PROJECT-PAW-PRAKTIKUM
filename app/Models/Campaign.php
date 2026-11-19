<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'character_id',
        'title',
        'description',
        'difficulty',
        'status',
        'current_location',
        'summary',
        'current_hp',
        'max_hp',
        'level',
        'experience',
        'character_status',
    ];

    protected function casts(): array
    {
        return [
            'current_hp' => 'integer',
            'max_hp' => 'integer',
            'level' => 'integer',
            'experience' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }

    public function storyHistories(): HasMany
    {
        return $this->hasMany(StoryHistory::class)->orderBy('created_at', 'asc');
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function quests(): HasMany
    {
        return $this->hasMany(CampaignQuest::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'Active';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'Completed';
    }

    public function isDefeated(): bool
    {
        return $this->status === 'Defeated';
    }

    /**
     * Calculate current HP percentage for health bar.
     */
    public function hpPercentage(): int
    {
        $max = $this->max_hp ?: ($this->character?->max_hp ?? 20);
        $curr = $this->current_hp ?? $max;
        if ($max <= 0) {
            return 0;
        }
        return (int) round(($curr / $max) * 100);
    }

    /**
     * Calculate XP threshold for next level.
     */
    public function xpThresholdForNextLevel(): int
    {
        $thresholds = [
            1 => 300,
            2 => 900,
            3 => 2700,
            4 => 6500,
            5 => 14000,
            6 => 23000,
            7 => 34000,
            8 => 48000,
            9 => 64000,
            10 => 85000,
        ];

        $lvl = $this->level ?: 1;
        return $thresholds[$lvl] ?? ($lvl * 10000);
    }

    /**
     * Calculate XP percentage towards next level.
     */
    public function xpPercentage(): int
    {
        $target = $this->xpThresholdForNextLevel();
        if ($target <= 0) {
            return 100;
        }
        $exp = $this->experience ?: 0;
        return (int) min(100, round(($exp / $target) * 100));
    }
}
