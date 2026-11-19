<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_page_can_be_rendered(): void
    {
        $response = $this->get(route('register'));

        $response->assertStatus(200);
        $response->assertSee('Pendaftaran Petualang');
    }

    public function test_new_user_can_register(): void
    {
        $response = $this->post(route('register.submit'), [
            'name' => 'Ksatria Baru',
            'email' => 'ksatria@dnd.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'ksatria@dnd.test',
            'name' => 'Ksatria Baru',
        ]);
    }

    public function test_user_cannot_register_with_duplicate_email(): void
    {
        User::factory()->create([
            'email' => 'ksatria@dnd.test',
        ]);

        $response = $this->post(route('register.submit'), [
            'name' => 'Ksatria Kembar',
            'email' => 'ksatria@dnd.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_login_page_can_be_rendered(): void
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $response->assertSee('Masuk Petualang');
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'petualang@dnd.test',
            'password' => Hash::make('secretpass123'),
        ]);

        $response = $this->post(route('login.submit'), [
            'email' => 'petualang@dnd.test',
            'password' => 'secretpass123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'petualang@dnd.test',
            'password' => Hash::make('secretpass123'),
        ]);

        $response = $this->post(route('login.submit'), [
            'email' => 'petualang@dnd.test',
            'password' => 'wrongpass',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_authenticated_user_can_update_profile(): void
    {
        $user = User::factory()->create([
            'name' => 'Nama Lama',
            'email' => 'lama@dnd.test',
        ]);

        $response = $this->actingAs($user)->put(route('profile.update'), [
            'name' => 'Nama Baru Petualang',
            'email' => 'baru@dnd.test',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Nama Baru Petualang',
            'email' => 'baru@dnd.test',
        ]);
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
