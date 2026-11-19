<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\Character;
use App\Models\StoryHistory;
use Illuminate\Support\Collection;

class StoryService
{
    /**
     * Retrieve recent story interactions for prompt context.
     *
     * @return Collection<int, StoryHistory>
     */
    public function getRecentHistory(Campaign $campaign, int $limit = 8): Collection
    {
        return StoryHistory::where('campaign_id', $campaign->id)
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get()
            ->reverse()
            ->values();
    }

    /**
     * Record a new story event / turn into the database.
     */
    public function recordStoryEvent(
        Campaign $campaign,
        Character $character,
        string $senderType,
        ?string $playerAction,
        string $narrative,
        ?string $actionResult = 'info',
        ?string $reasoning = null,
        ?array $aiResponseJson = null,
        int $hpChange = 0,
        int $xpChange = 0
    ): StoryHistory {
        return StoryHistory::create([
            'campaign_id' => $campaign->id,
            'character_id' => $character->id,
            'sender_type' => $senderType,
            'player_action' => $playerAction,
            'narrative' => $narrative,
            'action_result' => $actionResult,
            'reasoning' => $reasoning,
            'ai_response_json' => $aiResponseJson,
            'hp_change' => $hpChange,
            'xp_change' => $xpChange,
        ]);
    }

    /**
     * Construct rich context array for the Gemini Prompt using campaign-isolated character state.
     */
    public function buildPromptContext(Campaign $campaign, string $playerAction): array
    {
        $character = $campaign->character;
        $recentHistories = $this->getRecentHistory($campaign, 8);

        // Fetch campaign-scoped inventories (or fallback to base character)
        $inventories = $campaign->inventories()->with('item')->get();
        if ($inventories->isEmpty()) {
            $inventories = $character->inventories()->whereNull('campaign_id')->with('item')->get();
        }

        $inventoryList = $inventories->map(function ($inv) {
            $status = $inv->is_equipped ? ' (Digunakan)' : '';
            return "{$inv->item->name} [{$inv->item->category}, x{$inv->quantity}]{$status}: {$inv->item->description}";
        })->toArray();

        // Format recent dialogue history
        $formattedHistory = [];
        foreach ($recentHistories as $history) {
            if (!empty($history->player_action)) {
                $formattedHistory[] = [
                    'speaker' => 'Pemain (' . $character->name . ')',
                    'action' => $history->player_action,
                ];
            }
            $formattedHistory[] = [
                'speaker' => 'Dungeon Master (DM)',
                'narrative' => $history->narrative,
                'result' => $history->action_result,
            ];
        }

        $currHp = $campaign->current_hp ?? $character->max_hp;
        $maxHp = $campaign->max_hp ?? $character->max_hp;
        $level = $campaign->level ?? $character->level;
        $exp = $campaign->experience ?? $character->experience;
        $status = $campaign->character_status ?? $character->status;

        return [
            'campaign' => [
                'title' => $campaign->title,
                'description' => $campaign->description,
                'difficulty' => $campaign->difficulty,
                'location' => $campaign->current_location ?? 'Gerbang Petualangan',
                'summary' => $campaign->summary ?? 'Petualangan baru saja dimulai.',
            ],
            'character' => [
                'name' => $character->name,
                'race' => $character->race,
                'class' => $character->class,
                'level' => $level,
                'experience' => $exp,
                'current_hp' => $currHp,
                'max_hp' => $maxHp,
                'status' => $status,
                'stats' => [
                    'strength' => $character->strength . " (" . $character->getModifierString('strength') . ")",
                    'dexterity' => $character->dexterity . " (" . $character->getModifierString('dexterity') . ")",
                    'constitution' => $character->constitution . " (" . $character->getModifierString('constitution') . ")",
                    'intelligence' => $character->intelligence . " (" . $character->getModifierString('intelligence') . ")",
                    'wisdom' => $character->wisdom . " (" . $character->getModifierString('wisdom') . ")",
                    'charisma' => $character->charisma . " (" . $character->getModifierString('charisma') . ")",
                ],
                'backstory' => $character->backstory,
                'inventory' => $inventoryList,
            ],
            'recent_history' => $formattedHistory,
            'player_action' => $playerAction,
        ];
    }

    /**
     * Generate standard opening prologue for a new campaign.
     */
    public function generateOpeningPrologue(Campaign $campaign, Character $character): array
    {
        $loreTitles = [
            'Easy' => 'Hutan Kabut Whispering Woods',
            'Medium' => 'Reruntuhan Kuno Benteng Ravenloft',
            'Hard' => 'Katakomba Bawah Tanah Raja Terkutuk',
            'Legendary' => 'Menara Obsidian Abyss & Sarang Naga Merah',
        ];

        $location = $loreTitles[$campaign->difficulty] ?? 'Lembah Misteri';
        $campaign->current_location = $location;
        $campaign->save();

        $narrative = "Angin dingin berhembus menembus dedaunan purba saat kamu, {$character->name} seorang {$character->race} {$character->class}, melangkah memasuki batas {$location}. " .
            "Kabut pekat menyelimuti bebatuan berlumut, dan sayup-sayup terdengar gemuruh guntur dari kejauhan. " .
            "Di hadapanmu, sebuah pintu gerbang batu kuno berdiri setengah terbuka, dengan rune-rune mistis yang berpendar redup. " .
            "Petualanganmu bertajuk '{$campaign->title}' resmi dimulai. Suasana hening mencekam. Apa yang ingin kamu lakukan?";

        return [
            'narrative' => $narrative,
            'action_result' => 'info',
            'reasoning' => 'Awal petualangan: Dungeon Master membacakan prolog suasana awal dan menetapkan medan.',
            'hp_change' => 0,
            'xp_change' => 10,
            'inventory_changes' => [],
            'enemy_or_npc' => null,
            'campaign_status' => 'Active',
        ];
    }
}
