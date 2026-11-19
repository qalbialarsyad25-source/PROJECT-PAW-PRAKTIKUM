<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\Character;
use App\Models\Inventory;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdventureActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_player_can_perform_action_via_ajax_endpoint(): void
    {
        $user = User::factory()->create();
        $character = Character::create([
            'user_id' => $user->id,
            'name' => 'Ksatria Valerius',
            'race' => 'Human',
            'class' => 'Warrior',
            'level' => 1,
            'experience' => 0,
            'max_hp' => 16,
            'current_hp' => 16,
            'strength' => 16,
            'dexterity' => 12,
            'constitution' => 14,
            'intelligence' => 10,
            'wisdom' => 10,
            'charisma' => 10,
            'status' => 'Alive',
        ]);

        $campaign = Campaign::create([
            'user_id' => $user->id,
            'character_id' => $character->id,
            'title' => 'Pertempuran di Gerbang Kastil',
            'difficulty' => 'Medium',
            'status' => 'Active',
            'current_location' => 'Gerbang Kastil',
        ]);

        $response = $this->actingAs($user)->postJson(route('campaigns.action', $campaign), [
            'action' => 'Aku menghunus pedang panjangku dan menyerang goblin di depanku dengan sekuat tenaga',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'narrative',
            'action_result',
            'reasoning',
            'character' => [
                'name',
                'current_hp',
                'max_hp',
                'level',
                'experience',
            ],
            'inventories',
        ]);

        $this->assertTrue($response->json('success'));
        $this->assertNotEmpty($response->json('narrative'));

        // Assert story history was recorded in database
        $this->assertDatabaseHas('story_histories', [
            'campaign_id' => $campaign->id,
            'character_id' => $character->id,
            'player_action' => 'Aku menghunus pedang panjangku dan menyerang goblin di depanku dengan sekuat tenaga',
        ]);
    }

    public function test_player_can_use_potion_from_inventory(): void
    {
        $user = User::factory()->create();
        $character = Character::create([
            'user_id' => $user->id,
            'name' => 'Petualang Terluka',
            'race' => 'Human',
            'class' => 'Warrior',
            'level' => 1,
            'experience' => 0,
            'max_hp' => 20,
            'current_hp' => 5, // Damaged!
            'strength' => 10,
            'dexterity' => 10,
            'constitution' => 10,
            'intelligence' => 10,
            'wisdom' => 10,
            'charisma' => 10,
            'status' => 'Alive',
        ]);

        $campaign = Campaign::create([
            'user_id' => $user->id,
            'character_id' => $character->id,
            'title' => 'Penyembuhan di Perkemahan',
            'difficulty' => 'Medium',
            'status' => 'Active',
        ]);

        $potion = Item::create([
            'name' => 'Health Potion Darurat',
            'slug' => 'health-potion-darurat',
            'description' => 'Memulihkan 15 HP.',
            'category' => 'potion',
            'effect_type' => 'heal',
            'effect_value' => 15,
        ]);

        Inventory::create([
            'character_id' => $character->id,
            'item_id' => $potion->id,
            'quantity' => 2,
        ]);

        $response = $this->actingAs($user)->postJson(route('campaigns.use-item', $campaign), [
            'item_id' => $potion->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        // Assert character HP increased from 5 to 20 (capped at max_hp)
        $character->refresh();
        $this->assertEquals(20, $character->current_hp);

        // Assert inventory decremented from 2 to 1
        $inv = Inventory::where('character_id', $character->id)->where('item_id', $potion->id)->first();
        $this->assertEquals(1, $inv->quantity);

        // Assert system story history was recorded
        $this->assertDatabaseHas('story_histories', [
            'campaign_id' => $campaign->id,
            'sender_type' => 'system',
        ]);
    }

    public function test_unconscious_character_cannot_perform_combat_actions(): void
    {
        $user = User::factory()->create();
        $character = Character::create([
            'user_id' => $user->id,
            'name' => 'Ksatria Pingsan',
            'race' => 'Human',
            'class' => 'Warrior',
            'max_hp' => 15,
            'current_hp' => 0,
            'status' => 'Unconscious',
        ]);

        $campaign = Campaign::create([
            'user_id' => $user->id,
            'character_id' => $character->id,
            'title' => 'Jurang Kematian',
            'status' => 'Active',
        ]);

        $response = $this->actingAs($user)->postJson(route('campaigns.action', $campaign), [
            'action' => 'Aku berlari dan menyerang musuh',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
    }
}
