<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCharacterRequest;
use App\Http\Requests\UpdateCharacterRequest;
use App\Models\Character;
use App\Services\GameService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CharacterController extends Controller
{
    public function __construct(
        protected GameService $gameService
    ) {}

    /**
     * Display a listing of user's characters.
     */
    public function index(): View
    {
        $characters = Auth::user()
            ->characters()
            ->withCount(['campaigns', 'inventories'])
            ->latest()
            ->paginate(9);

        return view('characters.index', compact('characters'));
    }

    /**
     * Show the form for creating a new character.
     */
    public function create(): View
    {
        $classes = GameService::CLASSES;
        $races = GameService::RACES;

        return view('characters.create', compact('classes', 'races'));
    }

    /**
     * Store a newly created character in storage.
     */
    public function store(StoreCharacterRequest $request): RedirectResponse
    {
        $calculated = $this->gameService->calculateInitialStats($request->class, $request->race);

        $character = Auth::user()->characters()->create([
            'name' => $request->name,
            'race' => $request->race,
            'class' => $request->class,
            'level' => 1,
            'experience' => 0,
            'max_hp' => $calculated['max_hp'],
            'current_hp' => $calculated['current_hp'],
            'strength' => $calculated['stats']['strength'],
            'dexterity' => $calculated['stats']['dexterity'],
            'constitution' => $calculated['stats']['constitution'],
            'intelligence' => $calculated['stats']['intelligence'],
            'wisdom' => $calculated['stats']['wisdom'],
            'charisma' => $calculated['stats']['charisma'],
            'backstory' => $request->backstory,
            'status' => 'Alive',
        ]);

        $this->gameService->grantStarterItems($character);

        return redirect()->route('characters.show', $character)
            ->with('success', "Karakter {$character->name} berhasil diciptakan beserta perlengkapan perdananya!");
    }

    /**
     * Display the specified character.
     */
    public function show(Character $character): View
    {
        $this->authorizeCharacter($character);

        $character->load([
            'inventories.item',
            'campaigns' => function ($q) {
                $q->latest();
            },
        ]);

        return view('characters.show', compact('character'));
    }

    /**
     * Show the form for editing the character.
     */
    public function edit(Character $character): View
    {
        $this->authorizeCharacter($character);

        return view('characters.edit', compact('character'));
    }

    /**
     * Update the specified character.
     */
    public function update(UpdateCharacterRequest $request, Character $character): RedirectResponse
    {
        $this->authorizeCharacter($character);

        $character->update([
            'name' => $request->name,
            'backstory' => $request->backstory,
        ]);

        return redirect()->route('characters.show', $character)
            ->with('success', "Informasi karakter {$character->name} berhasil diperbarui.");
    }

    /**
     * Remove the specified character from storage.
     */
    public function destroy(Character $character): RedirectResponse
    {
        $this->authorizeCharacter($character);

        $name = $character->name;
        $character->delete();

        return redirect()->route('characters.index')
            ->with('success', "Karakter {$name} telah dihapus dari catatan petualang.");
    }

    /**
     * Ensure the authenticated user owns this character.
     */
    protected function authorizeCharacter(Character $character): void
    {
        if ($character->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak: Anda tidak memiliki wewenang atas karakter ini.');
        }
    }
}
