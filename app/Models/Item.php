<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'category',
        'effect_type',
        'effect_value',
        'icon',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'effect_value' => 'integer',
            'is_default' => 'boolean',
        ];
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function characters(): BelongsToMany
    {
        return $this->belongsToMany(Character::class, 'inventories')
            ->withPivot(['id', 'quantity', 'is_equipped'])
            ->withTimestamps();
    }
}
