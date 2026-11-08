<?php

namespace Tests\Feature;

use App\Models\DailyStat;
use App\Models\Link;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_page_view_is_aggregated_per_day(): void
    {
        $user = User::factory()->create();

        $this->get("/{$user->username}");
        $this->get("/{$user->username}");

        $stat = DailyStat::query()
            ->where('user_id', $user->id)
            ->whereDate('date', CarbonImmutable::today())
            ->first();

        $this->assertNotNull($stat);
        $this->assertSame(2, (int) $stat->views);
        $this->assertSame(0, (int) $stat->clicks);
    }

    public function test_owner_page_view_is_not_aggregated(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get("/{$user->username}");

        $this->assertDatabaseCount('daily_stats', 0);
    }

    public function test_link_click_is_aggregated_per_day(): void
    {
        $user = User::factory()->create();
        $link = Link::factory()->for($user)->create();

        $this->post("/links/{$link->id}/click")->assertNoContent();

        $stat = DailyStat::query()->where('user_id', $user->id)->first();

        $this->assertNotNull($stat);
        $this->assertSame(1, (int) $stat->clicks);
        $this->assertSame(0, (int) $stat->views);
    }

    public function test_dashboard_receives_a_14_day_series_with_zero_fill(): void
    {
        $user = User::factory()->create();

        // Traffic three days ago and today; everything else stays zero.
        DailyStat::factory()->for($user)->create([
            'date' => CarbonImmutable::today()->subDays(3)->toDateString(),
            'views' => 7,
            'clicks' => 4,
        ]);
        DailyStat::factory()->for($user)->create([
            'date' => CarbonImmutable::today()->toDateString(),
            'views' => 2,
            'clicks' => 1,
        ]);
        // A row outside the 14-day window must not leak into the series.
        DailyStat::factory()->for($user)->create([
            'date' => CarbonImmutable::today()->subDays(20)->toDateString(),
            'views' => 99,
            'clicks' => 99,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk();

        $series = $response->inertiaPage()['props']['analytics'];

        $this->assertCount(14, $series);
        $this->assertSame(CarbonImmutable::today()->subDays(13)->toDateString(), $series[0]['date']);
        $this->assertSame(0, $series[0]['views']);
        $this->assertSame(7, $series[10]['views']);
        $this->assertSame(4, $series[10]['clicks']);
        $this->assertSame(2, $series[13]['views']);
        $this->assertSame(1, $series[13]['clicks']);
        $this->assertSame(CarbonImmutable::today()->toDateString(), $series[13]['date']);
    }

    public function test_daily_series_stays_one_row_per_day(): void
    {
        $user = User::factory()->create();
        $link = Link::factory()->for($user)->create();

        $this->get("/{$user->username}");
        $this->get("/{$user->username}");
        $this->post("/links/{$link->id}/click");
        $this->get("/{$user->username}");

        $this->assertDatabaseCount('daily_stats', 1);

        $stat = DailyStat::query()->first();

        $this->assertSame(3, (int) $stat->views);
        $this->assertSame(1, (int) $stat->clicks);
    }
}
