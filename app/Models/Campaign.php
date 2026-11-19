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
    ];

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
}
