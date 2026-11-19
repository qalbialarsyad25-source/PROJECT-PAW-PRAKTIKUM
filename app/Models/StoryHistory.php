<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoryHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'character_id',
        'sender_type',
        'player_action',
        'narrative',
        'action_result',
        'reasoning',
        'ai_response_json',
        'hp_change',
        'xp_change',
    ];

    protected function casts(): array
    {
        return [
            'ai_response_json' => 'array',
            'hp_change' => 'integer',
            'xp_change' => 'integer',
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
}
