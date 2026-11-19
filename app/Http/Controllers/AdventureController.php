<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlayerActionRequest;
use App\Models\Campaign;
use App\Services\GameService;
use App\Services\GeminiService;
use App\Services\StoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdventureController extends Controller
{
    public function __construct(
        protected GeminiService $geminiService,
        protected GameService $gameService,
        protected StoryService $storyService
    ) {}

    /**
     * Process player action asynchronously via AJAX/Fetch API.
     */
    public function takeAction(PlayerActionRequest $request, Campaign $campaign): JsonResponse
    {
        $this->authorizeCampaign($campaign);

        if ($campaign->status === 'Completed') {
            return response()->json([
                'success' => false,
                'message' => 'Campaign ini telah selesai. Anda dapat meninjau kronik petualangan atau memulai campaign baru.',
            ], 422);
        }

        $character = $campaign->character;
        $campaignStatus = $campaign->character_status ?? 'Alive';

        if ($campaignStatus === 'Unconscious' && !str_contains(strtolower($request->action), 'istirahat') && !str_contains(strtolower($request->action), 'ramuan')) {
            return response()->json([
                'success' => false,
                'message' => 'Karakter Anda sedang pingsan / terluka parah (0 HP) dalam campaign ini. Anda harus meminum ramuan penyembuh atau beristirahat untuk memulihkan vitalitas!',
            ], 422);
        }

        // 1. Build rich context for Gemini API
        $context = $this->storyService->buildPromptContext($campaign, $request->action);

        // 2. Call Gemini API (with internal procedural D&D fallback)
        $aiOutcome = $this->geminiService->generateTurnOutcome($context);

        // 3. Backend single-source-of-truth: Apply validated status updates to Campaign in DB transaction
        $gameplayUpdates = $this->gameService->applyActionOutcome($campaign, $aiOutcome);

        // 4. Update campaign status if AI determined quest completion
        if (($aiOutcome['campaign_status'] ?? 'Active') === 'Completed') {
            $campaign->status = 'Completed';
            $campaign->save();
        }

        // 5. Record Story History
        $history = $this->storyService->recordStoryEvent(
            campaign: $campaign,
            character: $character,
            senderType: 'dm',
            playerAction: $request->action,
            narrative: $aiOutcome['narrative'],
            actionResult: $aiOutcome['action_result'],
            reasoning: $aiOutcome['reasoning'],
            aiResponseJson: $aiOutcome,
            hpChange: $gameplayUpdates['hp_delta'],
            xpChange: $gameplayUpdates['xp_gain']
        );

        // 6. Reload updated campaign-scoped inventories for frontend UI update
        $updatedInventories = $campaign->inventories()
            ->with('item')
            ->get()
            ->map(function ($inv) {
                return [
                    'id' => $inv->id,
                    'item_id' => $inv->item_id,
                    'name' => $inv->item->name,
                    'description' => $inv->item->description,
                    'category' => $inv->item->category,
                    'effect_type' => $inv->item->effect_type,
                    'effect_value' => $inv->item->effect_value,
                    'icon' => $inv->item->icon,
                    'quantity' => $inv->quantity,
                    'is_equipped' => $inv->is_equipped,
                    'is_usable' => ($inv->item->category === 'potion' || $inv->item->effect_type === 'heal' || in_array($inv->item->category, ['weapon', 'armor'])),
                ];
            });

        return response()->json([
            'success' => true,
            'narrative' => $aiOutcome['narrative'],
            'action_result' => $aiOutcome['action_result'],
            'reasoning' => $aiOutcome['reasoning'],
            'enemy_or_npc' => $aiOutcome['enemy_or_npc'] ?? null,
            'campaign_status' => $campaign->status,
            'gameplay' => [
                'hp_delta' => $gameplayUpdates['hp_delta'],
                'xp_gain' => $gameplayUpdates['xp_gain'],
                'did_level_up' => $gameplayUpdates['did_level_up'],
                'new_level' => $gameplayUpdates['new_level'],
                'inventory_changes' => $gameplayUpdates['inventory_changes'],
            ],
            'character' => [
                'name' => $character->name,
                'class' => $character->class,
                'race' => $character->race,
                'current_hp' => $campaign->current_hp,
                'max_hp' => $campaign->max_hp,
                'hp_percent' => $campaign->hpPercentage(),
                'level' => $campaign->level,
                'experience' => $campaign->experience,
                'xp_target' => $campaign->xpThresholdForNextLevel(),
                'xp_percent' => $campaign->xpPercentage(),
                'status' => $campaign->character_status,
                'stats' => [
                    'strength' => $character->strength,
                    'dexterity' => $character->dexterity,
                    'constitution' => $character->constitution,
                    'intelligence' => $character->intelligence,
                    'wisdom' => $character->wisdom,
                    'charisma' => $character->charisma,
                ],
                'modifiers' => [
                    'strength' => $character->getModifierString('strength'),
                    'dexterity' => $character->getModifierString('dexterity'),
                    'constitution' => $character->getModifierString('constitution'),
                    'intelligence' => $character->getModifierString('intelligence'),
                    'wisdom' => $character->getModifierString('wisdom'),
                    'charisma' => $character->getModifierString('charisma'),
                ],
            ],
            'inventories' => $updatedInventories,
            'timestamp' => $history->created_at->format('H:i'),
        ]);
    }

    /**
     * Use or equip an item from the campaign's inventory.
     */
    public function useItem(Request $request, Campaign $campaign): JsonResponse
    {
        $this->authorizeCampaign($campaign);

        $request->validate([
            'item_id' => ['required', 'integer'],
        ]);

        $result = $this->gameService->useItem($campaign, (int) $request->item_id);

        if (!$result['success']) {
            return response()->json($result, 422);
        }

        // Record system action in story histories
        $this->storyService->recordStoryEvent(
            campaign: $campaign,
            character: $campaign->character,
            senderType: 'system',
            playerAction: null,
            narrative: "[Sistem Inventaris] " . $result['message'],
            actionResult: 'info',
            reasoning: 'Pemain menggunakan item secara mandiri melalui inventaris.',
            aiResponseJson: null,
            hpChange: $result['hp_restored'] ?? 0,
            xpChange: 0
        );

        $campaign = $campaign->fresh();

        $updatedInventories = $campaign->inventories()
            ->with('item')
            ->get()
            ->map(function ($inv) {
                return [
                    'id' => $inv->id,
                    'item_id' => $inv->item_id,
                    'name' => $inv->item->name,
                    'description' => $inv->item->description,
                    'category' => $inv->item->category,
                    'effect_type' => $inv->item->effect_type,
                    'effect_value' => $inv->item->effect_value,
                    'icon' => $inv->item->icon,
                    'quantity' => $inv->quantity,
                    'is_equipped' => $inv->is_equipped,
                    'is_usable' => ($inv->item->category === 'potion' || $inv->item->effect_type === 'heal' || in_array($inv->item->category, ['weapon', 'armor'])),
                ];
            });

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'character' => [
                'current_hp' => $campaign->current_hp,
                'max_hp' => $campaign->max_hp,
                'hp_percent' => $campaign->hpPercentage(),
                'status' => $campaign->character_status,
            ],
            'inventories' => $updatedInventories,
        ]);
    }

    /**
     * Authorize user ownership of campaign.
     */
    protected function authorizeCampaign(Campaign $campaign): void
    {
        if ($campaign->user_id !== auth()->id()) {
            abort(403, 'Akses ditolak: Anda tidak memiliki wewenang atas campaign ini.');
        }
    }
}
