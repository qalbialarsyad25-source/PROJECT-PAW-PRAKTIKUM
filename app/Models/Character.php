<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Character extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'race',
        'class',
        'level',
        'experience',
        'max_hp',
        'current_hp',
        'strength',
        'dexterity',
        'constitution',
        'intelligence',
        'wisdom',
        'charisma',
        'backstory',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'level' => 'integer',
            'experience' => 'integer',
            'max_hp' => 'integer',
            'current_hp' => 'integer',
            'strength' => 'integer',
            'dexterity' => 'integer',
            'constitution' => 'integer',
            'intelligence' => 'integer',
            'wisdom' => 'integer',
            'charisma' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'inventories')
            ->withPivot(['id', 'quantity', 'is_equipped'])
            ->withTimestamps();
    }

    public function storyHistories(): HasMany
    {
        return $this->hasMany(StoryHistory::class);
    }

    /**
     * Calculate D&D ability modifier.
     */
    public function getModifier(string $stat): int
    {
        $value = $this->{$stat} ?? 10;
        return (int) floor(($value - 10) / 2);
    }

    /**
     * Return formatted modifier string like "+3" or "-1".
     */
    public function getModifierString(string $stat): string
    {
        $mod = $this->getModifier($stat);
        return ($mod >= 0 ? '+' : '') . $mod;
    }

    /**
     * Current HP percentage for UI health bar.
     */
    public function hpPercentage(): int
    {
        if ($this->max_hp <= 0) {
            return 0;
        }
        return (int) round(($this->current_hp / $this->max_hp) * 100);
    }

    /**
     * XP threshold for the next level.
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

        return $thresholds[$this->level] ?? ($this->level * 10000);
    }

    /**
     * XP percentage towards next level.
     */
    public function xpPercentage(): int
    {
        $target = $this->xpThresholdForNextLevel();
        if ($target <= 0) {
            return 100;
        }
        return (int) min(100, round(($this->experience / $target) * 100));
    }
}
