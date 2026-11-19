<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCampaignRequest;
use App\Models\Campaign;
use App\Models\Character;
use App\Services\GameService;
use App\Services\StoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CampaignController extends Controller
{
    public function __construct(
        protected StoryService $storyService,
        protected GameService $gameService
    ) {}

    /**
     * Display a listing of the user's campaigns.
     */
    public function index(): View
    {
        $user = Auth::user();

        $activeCampaigns = $user->campaigns()
            ->with(['character'])
            ->where('status', 'Active')
            ->latest()
            ->get();

        $completedCampaigns = $user->campaigns()
            ->with(['character'])
            ->where('status', '!=', 'Active')
            ->latest()
            ->get();

        return view('campaigns.index', compact('activeCampaigns', 'completedCampaigns'));
    }

    /**
     * Show the form for creating a new campaign.
     */
    public function create(): View
    {
        $characters = Auth::user()->characters()->get();

        return view('campaigns.create', compact('characters'));
    }

    /**
     * Store a newly created campaign and initialize its prologue and campaign-isolated status.
     */
    public function store(StoreCampaignRequest $request): RedirectResponse
    {
        $character = Character::findOrFail($request->character_id);

        $campaign = Auth::user()->campaigns()->create([
            'character_id' => $request->character_id,
            'title' => $request->title,
            'description' => $request->description,
            'difficulty' => $request->difficulty,
            'status' => 'Active',
            'current_location' => 'Pintu Gerbang Petualangan',
            'current_hp' => $character->max_hp,
            'max_hp' => $character->max_hp,
            'level' => 1,
            'experience' => 0,
            'character_status' => 'Alive',
        ]);

        // Grant campaign-isolated starter equipment
        $this->gameService->grantStarterItems($character, $campaign);

        // Generate opening prologue
        $prologue = $this->storyService->generateOpeningPrologue($campaign, $character);

        // Record prologue into story histories
        $this->storyService->recordStoryEvent(
            campaign: $campaign,
            character: $character,
            senderType: 'dm',
            playerAction: null,
            narrative: $prologue['narrative'],
            actionResult: $prologue['action_result'],
            reasoning: $prologue['reasoning'],
            aiResponseJson: $prologue,
            hpChange: 0,
            xpChange: $prologue['xp_change']
        );

        // Initial experience bonus for starting campaign
        $campaign->experience += $prologue['xp_change'];
        $campaign->save();

        return redirect()->route('campaigns.adventure', $campaign)
            ->with('success', "Campaign '{$campaign->title}' dimulai! Sang Dungeon Master menantimu.");
    }

    /**
     * Display campaign chronicle / detail.
     */
    public function show(Campaign $campaign): View
    {
        $this->authorizeCampaign($campaign);

        $campaign->load(['character', 'storyHistories']);

        return view('campaigns.show', compact('campaign'));
    }

    /**
     * Interactive adventure screen (AI Dungeon Master interface).
     */
    public function adventure(Campaign $campaign): View
    {
        $this->authorizeCampaign($campaign);

        $campaign->load([
            'character',
            'inventories.item',
            'storyHistories' => function ($q) {
                $q->orderBy('created_at', 'asc');
            },
        ]);

        $inventories = $campaign->inventories;
        if ($inventories->isEmpty()) {
            // Seed starter items into this campaign if none exists
            $this->gameService->grantStarterItems($campaign->character, $campaign);
            $campaign->refresh();
            $inventories = $campaign->inventories()->with('item')->get();
        }

        return view('campaigns.adventure', [
            'campaign' => $campaign,
            'character' => $campaign->character,
            'histories' => $campaign->storyHistories,
            'inventories' => $inventories,
        ]);
    }

    /**
     * Remove the specified campaign from storage.
     */
    public function destroy(Campaign $campaign): RedirectResponse
    {
        $this->authorizeCampaign($campaign);

        $title = $campaign->title;
        $campaign->delete();

        return redirect()->route('campaigns.index')
            ->with('success', "Campaign '{$title}' berhasil dihapus.");
    }

    /**
     * Authorize user ownership of campaign.
     */
    protected function authorizeCampaign(Campaign $campaign): void
    {
        if ($campaign->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak: Anda tidak memiliki wewenang atas campaign ini.');
        }
    }
}
