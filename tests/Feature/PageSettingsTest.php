<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_bio_and_theme_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->put('/page-settings', [
                'theme_color' => '#8B5CF6',
                'bio' => 'Full-stack dev and coffee enthusiast.',
            ]);

        $response->assertSessionHasNoErrors();

        $user->refresh();

        $this->assertSame('#8B5CF6', $user->theme_color);
        $this->assertSame('Full-stack dev and coffee enthusiast.', $user->bio);
    }

    public function test_bio_can_be_cleared(): void
    {
        $user = User::factory()->create(['bio' => 'Old bio']);

        $response = $this
            ->actingAs($user)
            ->put('/page-settings', [
                'theme_color' => $user->theme_color,
                'bio' => null,
            ]);

        $response->assertSessionHasNoErrors();

        $this->assertNull($user->refresh()->bio);
    }

    public function test_bio_length_is_limited(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->put('/page-settings', [
                'theme_color' => '#8B5CF6',
                'bio' => str_repeat('a', 281),
            ]);

        $response->assertSessionHasErrors('bio');
    }

    public function test_theme_color_must_be_six_digit_hex(): void
    {
        $user = User::factory()->create();

        foreach (['#fff', 'blue', '#12345', '#1234567'] as $color) {
            $response = $this
                ->actingAs($user)
                ->put('/page-settings', [
                    'theme_color' => $color,
                    'bio' => null,
                ]);

            $response->assertSessionHasErrors('theme_color');
        }
    }
}
