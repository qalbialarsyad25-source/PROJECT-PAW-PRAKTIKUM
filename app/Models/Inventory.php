<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'character_id',
        'item_id',
        'quantity',
        'is_equipped',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'is_equipped' => 'boolean',
        ];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
