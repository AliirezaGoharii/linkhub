<?php

namespace Tests\Feature;

use App\Models\Link;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_links_require_authentication(): void
    {
        $this->post('/links', ['title' => 'X', 'url' => 'https://example.com'])->assertRedirect('/login');
        $this->put('/links/order', ['links' => []])->assertRedirect('/login');
    }

    public function test_user_can_create_a_link(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post('/links', [
                'title' => 'My Website',
                'url' => 'https://example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertDatabaseHas('links', [
            'user_id' => $user->id,
            'title' => 'My Website',
            'url' => 'https://example.com',
        ]);

        $this->assertSame(1, $user->links()->first()->ordering);
    }

    public function test_link_validation_rejects_bad_urls(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post('/links', [
                'title' => '',
                'url' => 'not-a-url',
            ]);

        $response->assertSessionHasErrors(['title', 'url']);
    }

    public function test_user_can_update_their_link(): void
    {
        $user = User::factory()->create();
        $link = Link::factory()->for($user)->create();

        $response = $this
            ->actingAs($user)
            ->put("/links/{$link->id}", [
                'title' => 'Updated',
                'url' => 'https://updated.com',
            ]);

        $response->assertSessionHasNoErrors();

        $this->assertSame('Updated', $link->fresh()->title);
        $this->assertSame('https://updated.com', $link->fresh()->url);
    }

    public function test_user_cannot_update_someone_elses_link(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $link = Link::factory()->for($owner)->create();

        $response = $this
            ->actingAs($intruder)
            ->put("/links/{$link->id}", [
                'title' => 'Hacked',
                'url' => 'https://evil.com',
            ]);

        $response->assertForbidden();

        $this->assertSame($link->title, $link->fresh()->title);
    }

    public function test_user_can_delete_their_link(): void
    {
        $user = User::factory()->create();
        $link = Link::factory()->for($user)->create();

        $this
            ->actingAs($user)
            ->delete("/links/{$link->id}")
            ->assertSessionHasNoErrors();

        $this->assertNull($link->fresh());
    }

    public function test_user_cannot_delete_someone_elses_link(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $link = Link::factory()->for($owner)->create();

        $this
            ->actingAs($intruder)
            ->delete("/links/{$link->id}")
            ->assertForbidden();

        $this->assertNotNull($link->fresh());
    }

    public function test_user_can_reorder_their_links(): void
    {
        $user = User::factory()->create();
        $first = Link::factory()->for($user)->create(['ordering' => 0]);
        $second = Link::factory()->for($user)->create(['ordering' => 1]);
        $third = Link::factory()->for($user)->create(['ordering' => 2]);

        $response = $this
            ->actingAs($user)
            ->put('/links/order', [
                'links' => [$third->id, $first->id, $second->id],
            ]);

        $response->assertSessionHasNoErrors();

        $this->assertSame(0, $third->fresh()->ordering);
        $this->assertSame(1, $first->fresh()->ordering);
        $this->assertSame(2, $second->fresh()->ordering);
    }

    public function test_reorder_rejects_foreign_link_ids(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $ownLink = Link::factory()->for($user)->create(['ordering' => 5]);
        $foreignLink = Link::factory()->for($other)->create(['ordering' => 7]);

        $response = $this
            ->actingAs($user)
            ->put('/links/order', [
                'links' => [$ownLink->id, $foreignLink->id],
            ]);

        $response->assertSessionHasErrors(['links.1']);

        // Validation failed, so neither link's ordering may have changed.
        $this->assertSame(5, $ownLink->fresh()->ordering);
        $this->assertSame(7, $foreignLink->fresh()->ordering);
    }

    public function test_dashboard_shows_link_stats(): void
    {
        $user = User::factory()->create();
        Link::factory()->for($user)->count(3)->create(['click_count' => 10]);

        $response = $this
            ->actingAs($user)
            ->get('/dashboard');

        $response->assertOk();

        $stats = $response->inertiaPage()['props']['stats'];
        $this->assertSame(3, $stats['link_count']);
        $this->assertSame(30, $stats['total_clicks']);
        $this->assertNotNull($stats['top_link']);
    }

    public function test_link_can_be_toggled_hidden_and_shown(): void
    {
        $user = User::factory()->create();
        $link = Link::factory()->for($user)->create();

        $this
            ->actingAs($user)
            ->patch("/links/{$link->id}/toggle")
            ->assertSessionHasNoErrors();

        $this->assertFalse((bool) $link->fresh()->is_active);

        $this
            ->actingAs($user)
            ->patch("/links/{$link->id}/toggle");

        $this->assertTrue((bool) $link->fresh()->is_active);
    }

    public function test_link_toggle_requires_ownership(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $link = Link::factory()->for($owner)->create();

        $this
            ->actingAs($intruder)
            ->patch("/links/{$link->id}/toggle")
            ->assertForbidden();

        $this->assertTrue((bool) $link->fresh()->is_active);
    }

    public function test_new_links_are_visible_by_default(): void
    {
        $user = User::factory()->create();

        $this
            ->actingAs($user)
            ->post('/links', ['title' => 'Fresh', 'url' => 'https://example.com']);

        $this->assertTrue((bool) $user->links()->first()->is_active);
    }
}
