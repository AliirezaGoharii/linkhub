<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'username' => 'testuser',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('testuser', $user->username);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'username' => $user->username,
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_username_must_be_unique_when_updating_profile(): void
    {
        $user = User::factory()->create();
        User::factory()->create(['username' => 'someoneelse']);

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => $user->name,
                'username' => 'someoneelse',
                'email' => $user->email,
            ]);

        $response->assertSessionHasErrors('username');

        $this->assertSame($user->username, $user->refresh()->username);
    }

    public function test_reserved_username_is_rejected_when_updating_profile(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => $user->name,
                'username' => 'admin',
                'email' => $user->email,
            ]);

        $response->assertSessionHasErrors('username');

        $this->assertNotSame('admin', $user->refresh()->username);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_deleting_the_account_removes_uploaded_media(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $user->forceFill([
            'profile_photo_path' => UploadedFile::fake()->image('photo.png')->store('profile-photos', 'public'),
            'banner_path' => UploadedFile::fake()->image('banner.png')->store('banners', 'public'),
        ])->save();

        $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $this->assertNull($user->fresh());
        Storage::disk('public')->assertMissing($user->profile_photo_path);
        Storage::disk('public')->assertMissing($user->banner_path);
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrors('password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }
}
