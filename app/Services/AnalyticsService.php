<?php

namespace App\Services;

use App\Models\DailyStat;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    /**
     * Record one page view on the user's public page for today.
     */
    public function recordView(User $user): void
    {
        $this->bump($user, 'views');
    }

    /**
     * Record one link click on the user's public page for today.
     */
    public function recordClick(User $user): void
    {
        $this->bump($user, 'clicks');
    }

    /**
     * A day-by-day views/clicks series for the dashboard chart.
     *
     * Missing days are filled with zeros so the frontend always
     * receives a continuous, fixed-length series.
     *
     * @return array<int, array{date: string, views: int, clicks: int}>
     */
    public function dailySeries(User $user, int $days = 14): array
    {
        $days = max(1, min(90, $days));

        $start = CarbonImmutable::today()->subDays($days - 1);

        $rows = $user->dailyStats()
            ->whereBetween('date', [$start->toDateString(), CarbonImmutable::today()->toDateString()])
            ->get()
            ->keyBy(fn (DailyStat $stat) => $stat->date->toDateString());

        $series = [];

        for ($i = 0; $i < $days; $i++) {
            $day = $start->addDays($i)->toDateString();

            $series[] = [
                'date' => $day,
                'views' => (int) ($rows[$day]->views ?? 0),
                'clicks' => (int) ($rows[$day]->clicks ?? 0),
            ];
        }

        return $series;
    }

    /**
     * Atomically increment today's counter, creating the row if it
     * is the first event of the day. The unique (user_id, date) key
     * makes the upsert race-safe.
     */
    private function bump(User $user, string $column): void
    {
        DB::table('daily_stats')->upsert(
            [
                'user_id' => $user->id,
                'date' => CarbonImmutable::today()->toDateString(),
                $column => DB::raw(1),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            ['user_id', 'date'],
            [
                $column => DB::raw("daily_stats.{$column} + 1"),
                'updated_at' => now(),
            ],
        );
    }
}
