<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        $this->assertDatabaseHas('users', [
            'username' => 'testuser',
        ]);
    }

    public function test_usernames_must_be_unique(): void
    {
        User::factory()->create(['username' => 'taken']);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'username' => 'taken',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    public function test_usernames_must_be_alpha_dash(): void
    {
        $invalidUsernames = ['hello world', 'hi!', '../etc', 'Ünicode'];

        foreach ($invalidUsernames as $username) {
            $response = $this->post('/register', [
                'name' => 'Test User',
                'username' => $username,
                'email' => 'test@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

            $response->assertSessionHasErrors('username');
            $this->assertGuest();
        }
    }

    public function test_reserved_usernames_are_rejected(): void
    {
        foreach (['login', 'register', 'dashboard', 'admin', 'api', 'storage'] as $username) {
            $response = $this->post('/register', [
                'name' => 'Test User',
                'username' => $username,
                'email' => 'test@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

            $response->assertSessionHasErrors('username');
            $this->assertGuest();
        }
    }
}
