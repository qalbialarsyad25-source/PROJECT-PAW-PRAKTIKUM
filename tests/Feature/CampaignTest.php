<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\Character;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampaignTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_campaigns_index(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('campaigns.index'));

        $response->assertStatus(200);
        $response->assertSee('Daftar Campaign Petualangan');
    }

    public function test_user_can_create_a_campaign_with_automatic_prologue(): void
    {
        $user = User::factory()->create();
        $character = Character::create([
            'user_id' => $user->id,
            'name' => 'Elven Mage',
            'race' => 'Elf',
            'class' => 'Mage',
            'level' => 1,
            'max_hp' => 10,
            'current_hp' => 10,
            'strength' => 8,
            'dexterity' => 14,
            'constitution' => 10,
            'intelligence' => 16,
            'wisdom' => 13,
            'charisma' => 10,
            'status' => 'Alive',
        ]);

        $response = $this->actingAs($user)->post(route('campaigns.store'), [
            'character_id' => $character->id,
            'title' => 'Misi Penyelamatan Menara Kuno',
            'description' => 'Mencari artefak kuno di dalam menara terbengkalai.',
            'difficulty' => 'Medium',
        ]);

        $this->assertDatabaseHas('campaigns', [
            'user_id' => $user->id,
            'character_id' => $character->id,
            'title' => 'Misi Penyelamatan Menara Kuno',
            'difficulty' => 'Medium',
            'status' => 'Active',
        ]);

        $campaign = Campaign::where('title', 'Misi Penyelamatan Menara Kuno')->first();
        $this->assertNotNull($campaign);

        // Check opening prologue was recorded
        $this->assertDatabaseHas('story_histories', [
            'campaign_id' => $campaign->id,
            'character_id' => $character->id,
            'sender_type' => 'dm',
        ]);

        $response->assertRedirect(route('campaigns.adventure', $campaign));
    }

    public function test_user_can_access_adventure_screen(): void
    {
        $user = User::factory()->create();
        $character = Character::create([
            'user_id' => $user->id,
            'name' => 'Dwarf Fighter',
            'race' => 'Dwarf',
            'class' => 'Warrior',
            'level' => 1,
            'max_hp' => 14,
            'current_hp' => 14,
            'strength' => 16,
            'dexterity' => 12,
            'constitution' => 14,
            'intelligence' => 8,
            'wisdom' => 10,
            'charisma' => 10,
            'status' => 'Alive',
        ]);

        $campaign = Campaign::create([
            'user_id' => $user->id,
            'character_id' => $character->id,
            'title' => 'Ekspedisi Gua Hitam',
            'difficulty' => 'Hard',
            'status' => 'Active',
            'current_location' => 'Pintu Masuk Gua Hitam',
        ]);

        $response = $this->actingAs($user)->get(route('campaigns.adventure', $campaign));

        $response->assertStatus(200);
        $response->assertSee('Dwarf Fighter');
        $response->assertSee('Ekspedisi Gua Hitam');
        $response->assertSee('Apa yang ingin kamu lakukan?');
    }

    public function test_user_cannot_access_another_users_campaign(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $charA = Character::create([
            'user_id' => $userA->id,
            'name' => 'Ksatria A',
            'race' => 'Human',
            'class' => 'Warrior',
            'max_hp' => 15,
            'current_hp' => 15,
        ]);

        $campaignA = Campaign::create([
            'user_id' => $userA->id,
            'character_id' => $charA->id,
            'title' => 'Campaign Rahasia User A',
            'difficulty' => 'Medium',
            'status' => 'Active',
        ]);

        // User B tries to view or adventure in User A's campaign
        $response = $this->actingAs($userB)->get(route('campaigns.show', $campaignA));
        $response->assertStatus(403);

        $response = $this->actingAs($userB)->get(route('campaigns.adventure', $campaignA));
        $response->assertStatus(403);
    }
}
