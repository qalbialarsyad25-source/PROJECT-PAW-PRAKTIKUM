<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\Character;
use App\Models\User;
use App\Services\GameService;
use App\Services\StoryService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ItemSeeder::class,
        ]);

        $gameService = app(GameService::class);
        $storyService = app(StoryService::class);

        // Create Demo Adventurer User
        $user = User::updateOrCreate(
            ['email' => 'player@dnd.test'],
            [
                'name' => 'Dungeon Master Adventurer',
                'password' => Hash::make('password123'),
            ]
        );

        // Create Demo Characters if user has none
        if ($user->characters()->count() === 0) {
            // Character 1: Dwarf Warrior
            $warriorStats = $gameService->calculateInitialStats('Warrior', 'Dwarf');
            $warrior = $user->characters()->create([
                'name' => 'Valerius Ironbrow',
                'race' => 'Dwarf',
                'class' => 'Warrior',
                'level' => 1,
                'experience' => 50,
                'max_hp' => $warriorStats['max_hp'],
                'current_hp' => $warriorStats['current_hp'],
                'strength' => $warriorStats['stats']['strength'],
                'dexterity' => $warriorStats['stats']['dexterity'],
                'constitution' => $warriorStats['stats']['constitution'],
                'intelligence' => $warriorStats['stats']['intelligence'],
                'wisdom' => $warriorStats['stats']['wisdom'],
                'charisma' => $warriorStats['stats']['charisma'],
                'backstory' => 'Seorang ksatria klan pegunungan yang bersumpah mengembalikan kehormatan leluhurnya yang hilang.',
                'status' => 'Alive',
            ]);
            $gameService->grantStarterItems($warrior);

            // Character 2: Elf Mage
            $mageStats = $gameService->calculateInitialStats('Mage', 'Elf');
            $mage = $user->characters()->create([
                'name' => 'Aeloria Moonwhisper',
                'race' => 'Elf',
                'class' => 'Mage',
                'level' => 2,
                'experience' => 450,
                'max_hp' => $mageStats['max_hp'] + 4,
                'current_hp' => $mageStats['max_hp'] + 4,
                'strength' => $mageStats['stats']['strength'],
                'dexterity' => $mageStats['stats']['dexterity'],
                'constitution' => $mageStats['stats']['constitution'],
                'intelligence' => $mageStats['stats']['intelligence'] + 1,
                'wisdom' => $mageStats['stats']['wisdom'],
                'charisma' => $mageStats['stats']['charisma'],
                'backstory' => 'Cendekiawan akademi sihir perak yang meneliti anomali portal dimensi kuno.',
                'status' => 'Alive',
            ]);
            $gameService->grantStarterItems($mage);

            // Character 3: Halfling Rogue
            $rogueStats = $gameService->calculateInitialStats('Rogue', 'Halfling');
            $rogue = $user->characters()->create([
                'name' => 'Shadowthorn Pip',
                'race' => 'Halfling',
                'class' => 'Rogue',
                'level' => 1,
                'experience' => 0,
                'max_hp' => $rogueStats['max_hp'],
                'current_hp' => $rogueStats['current_hp'],
                'strength' => $rogueStats['stats']['strength'],
                'dexterity' => $rogueStats['stats']['dexterity'],
                'constitution' => $rogueStats['stats']['constitution'],
                'intelligence' => $rogueStats['stats']['intelligence'],
                'wisdom' => $rogueStats['stats']['wisdom'],
                'charisma' => $rogueStats['stats']['charisma'],
                'backstory' => 'Pencuri ulung dengan hati dermawan yang terbiasa keluar masuk dungeon bawah tanah kota.',
                'status' => 'Alive',
            ]);
            $gameService->grantStarterItems($rogue);

            // Create initial active Campaign for Valerius
            $campaign = $user->campaigns()->create([
                'character_id' => $warrior->id,
                'title' => 'Misteri Benteng Kuno Ravenloft',
                'description' => 'Membongkar teka-teki kabut abadi dan makhluk bayangan yang meneror desa terdekat.',
                'difficulty' => 'Medium',
                'status' => 'Active',
                'current_location' => 'Reruntuhan Kuno Benteng Ravenloft',
                'current_hp' => $warrior->max_hp,
                'max_hp' => $warrior->max_hp,
                'level' => 1,
                'experience' => 50,
                'character_status' => 'Alive',
            ]);

            $gameService->grantStarterItems($warrior, $campaign);

            // Create prologue for this campaign
            $prologue = $storyService->generateOpeningPrologue($campaign, $warrior);
            $storyService->recordStoryEvent(
                campaign: $campaign,
                character: $warrior,
                senderType: 'dm',
                playerAction: null,
                narrative: $prologue['narrative'],
                actionResult: $prologue['action_result'],
                reasoning: $prologue['reasoning'],
                aiResponseJson: $prologue,
                hpChange: 0,
                xpChange: $prologue['xp_change']
            );
        }
    }
}
