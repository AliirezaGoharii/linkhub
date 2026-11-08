<?php

namespace Tests\Feature;

use App\Models\Link;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_page_is_rendered(): void
    {
        $user = User::factory()->create([
            'name' => 'Ada Lovelace',
            'username' => 'ada',
            'bio' => 'First programmer, link enthusiast.',
        ]);
        Link::factory()->for($user)->create(['title' => 'My Blog']);

        $response = $this->get('/ada');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Public/Show')
            ->where('user.username', 'ada')
            ->where('user.bio', 'First programmer, link enthusiast.')
            ->has('user.links', 1));
    }

    public function test_unknown_username_returns_404(): void
    {
        $this->get('/ghost-user')->assertNotFound();
    }

    public function test_page_view_is_counted_for_guests(): void
    {
        $user = User::factory()->create();

        $this->get("/{$user->username}");
        $this->get("/{$user->username}");

        $this->assertSame(2, (int) $user->fresh()->profile_views);
    }

    public function test_page_view_is_not_counted_for_the_owner(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get("/{$user->username}");

        $this->assertSame(0, (int) $user->fresh()->profile_views);
    }

    public function test_link_click_is_counted(): void
    {
        $user = User::factory()->create();
        $link = Link::factory()->for($user)->create(['click_count' => 5]);

        $this->post("/links/{$link->id}/click")->assertNoContent();

        $this->assertSame(6, (int) $link->fresh()->click_count);
    }

    public function test_public_page_does_not_expose_private_user_data(): void
    {
        $user = User::factory()->create([
            'username' => 'ada',
            'email' => 'secret-address@example.com',
        ]);
        Link::factory()->for($user)->create(['title' => 'My Blog']);

        $response = $this->get('/ada');

        $response->assertOk();

        // The e-mail address (and every other private field) must not
        // ship to anonymous visitors through the Inertia payload.
        $response->assertInertia(fn ($page) => $page
            ->component('Public/Show')
            ->where('user.username', 'ada')
            ->missing('user.email')
            ->missing('user.email_verified_at')
            ->missing('user.profile_views'));

        $this->assertStringNotContainsString('secret-address@example.com', $response->getContent());
    }

    public function test_public_links_only_contain_safe_fields(): void
    {
        $user = User::factory()->create();
        Link::factory()->for($user)->create(['title' => 'My Blog', 'click_count' => 42]);

        $response = $this->get("/{$user->username}");

        $response->assertInertia(fn ($page) => $page
            ->where('user.links.0.title', 'My Blog')
            ->missing('user.links.0.click_count')
            ->missing('user.links.0.user_id')
            ->missing('user.links.0.ordering'));
    }

    public function test_inactive_links_are_hidden_from_the_public_page(): void
    {
        $user = User::factory()->create();
        Link::factory()->for($user)->create(['title' => 'Visible']);
        Link::factory()->for($user)->create(['title' => 'Hidden', 'is_active' => false]);

        $response = $this->get("/{$user->username}");

        $response->assertInertia(fn ($page) => $page
            ->has('user.links', 1)
            ->where('user.links.0.title', 'Visible'));
    }

    public function test_public_page_renders_social_meta_tags(): void
    {
        $user = User::factory()->create([
            'name' => 'Ada Lovelace',
            'username' => 'ada',
            'bio' => 'First programmer.',
        ]);

        $response = $this->get('/ada');

        $response->assertOk();
        $response->assertSee('property="og:title"', false);
        $response->assertSee('property="og:description"', false);
        $response->assertSee('rel="canonical"', false);
        $response->assertSee('name="twitter:card"', false);
        $response->assertSee('application/ld+json', false);

        $this->assertStringContainsString('Ada Lovelace (@ada)', $response->getContent());
        $this->assertStringContainsString('First programmer.', $response->getContent());
        $this->assertStringContainsString('http://localhost/ada', $response->getContent());
    }
}
