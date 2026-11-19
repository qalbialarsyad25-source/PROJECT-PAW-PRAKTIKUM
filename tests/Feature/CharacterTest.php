<?php

namespace Tests\Feature;

use App\Models\Character;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CharacterTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_characters_index(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('characters.index'));

        $response->assertStatus(200);
        $response->assertSee('Daftar Karakter Petualang');
    }

    public function test_user_can_create_a_character_with_starter_inventory(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('characters.store'), [
            'name' => 'Ksatria Thorin',
            'race' => 'Dwarf',
            'class' => 'Warrior',
            'backstory' => 'Seorang ksatria gunung yang tangguh.',
        ]);

        $this->assertDatabaseHas('characters', [
            'user_id' => $user->id,
            'name' => 'Ksatria Thorin',
            'race' => 'Dwarf',
            'class' => 'Warrior',
            'level' => 1,
            'status' => 'Alive',
        ]);

        $character = Character::where('name', 'Ksatria Thorin')->first();
        $this->assertNotNull($character);
        $this->assertGreaterThan(0, $character->max_hp);
        $this->assertEquals($character->max_hp, $character->current_hp);

        // Assert starter inventory items were granted
        $this->assertGreaterThan(0, $character->inventories()->count());

        $response->assertRedirect(route('characters.show', $character));
    }

    public function test_user_can_view_character_detail(): void
    {
        $user = User::factory()->create();
        $character = Character::create([
            'user_id' => $user->id,
            'name' => 'Elf Ranger',
            'race' => 'Elf',
            'class' => 'Archer',
            'level' => 1,
            'experience' => 0,
            'max_hp' => 12,
            'current_hp' => 12,
            'strength' => 10,
            'dexterity' => 18,
            'constitution' => 12,
            'intelligence' => 10,
            'wisdom' => 14,
            'charisma' => 9,
            'status' => 'Alive',
        ]);

        $response = $this->actingAs($user)->get(route('characters.show', $character));

        $response->assertStatus(200);
        $response->assertSee('Elf Ranger');
        $response->assertSee('+4'); // DEX modifier for 18
    }

    public function test_user_can_update_character(): void
    {
        $user = User::factory()->create();
        $character = Character::create([
            'user_id' => $user->id,
            'name' => 'Nama Lama Karakter',
            'race' => 'Human',
            'class' => 'Mage',
            'max_hp' => 10,
            'current_hp' => 10,
            'strength' => 8,
            'dexterity' => 12,
            'constitution' => 11,
            'intelligence' => 16,
            'wisdom' => 13,
            'charisma' => 10,
        ]);

        $response = $this->actingAs($user)->put(route('characters.update', $character), [
            'name' => 'Nama Baru Karakter',
            'backstory' => 'Kisah petualang baru.',
        ]);

        $response->assertRedirect(route('characters.show', $character));
        $this->assertDatabaseHas('characters', [
            'id' => $character->id,
            'name' => 'Nama Baru Karakter',
            'backstory' => 'Kisah petualang baru.',
        ]);
    }

    public function test_user_can_delete_character(): void
    {
        $user = User::factory()->create();
        $character = Character::create([
            'user_id' => $user->id,
            'name' => 'Karakter Terhapus',
            'race' => 'Human',
            'class' => 'Warrior',
            'max_hp' => 15,
            'current_hp' => 15,
        ]);

        $response = $this->actingAs($user)->delete(route('characters.destroy', $character));

        $response->assertRedirect(route('characters.index'));
        $this->assertDatabaseMissing('characters', [
            'id' => $character->id,
        ]);
    }

    public function test_user_cannot_access_or_edit_another_users_character(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $characterA = Character::create([
            'user_id' => $userA->id,
            'name' => 'Pahlawan Rahasia User A',
            'race' => 'Human',
            'class' => 'Warrior',
            'max_hp' => 15,
            'current_hp' => 15,
        ]);

        // User B tries to view User A's character
        $response = $this->actingAs($userB)->get(route('characters.show', $characterA));
        $response->assertStatus(403);

        // User B tries to edit User A's character
        $response = $this->actingAs($userB)->put(route('characters.update', $characterA), [
            'name' => 'Nama Dibajak',
        ]);
        $response->assertStatus(403);

        // User B tries to delete User A's character
        $response = $this->actingAs($userB)->delete(route('characters.destroy', $characterA));
        $response->assertStatus(403);
    }
}
