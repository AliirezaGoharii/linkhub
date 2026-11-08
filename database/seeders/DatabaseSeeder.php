<?php

namespace Database\Seeders;

use App\Models\DailyStat;
use App\Models\Link;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with a ready-to-use demo account.
     *
     * Login:  demo@linkhub.test  /  password
     */
    public function run(): void
    {
        $demo = User::factory()->create([
            'name' => 'Demo User',
            'username' => 'demo',
            'email' => 'demo@linkhub.test',
            'bio' => 'This is a demo account. Log in and make it yours!',
            'theme_color' => '#6366F1',
            'profile_views' => 42,
        ]);

        $links = [
            ['title' => 'My Website', 'url' => 'https://example.com', 'click_count' => 128],
            ['title' => 'GitHub', 'url' => 'https://github.com', 'click_count' => 96],
            ['title' => 'YouTube', 'url' => 'https://youtube.com', 'click_count' => 54],
            ['title' => 'X (Twitter)', 'url' => 'https://x.com', 'click_count' => 30],
            ['title' => 'Contact Me', 'url' => 'mailto:demo@linkhub.test', 'click_count' => 12],
        ];

        foreach ($links as $index => $link) {
            $demo->links()->create($link + ['ordering' => $index]);
        }

        // Two weeks of plausible analytics history so the dashboard
        // chart and stat cards have something to show in the demo.
        foreach (range(13, 0) as $daysAgo) {
            $views = random_int(8, 60);
            DailyStat::factory()->for($demo)->create([
                'date' => now()->subDays($daysAgo)->toDateString(),
                'views' => $views,
                'clicks' => (int) round($views * random_int(20, 45) / 100),
            ]);
        }

        if (app()->environment('local')) {
            User::factory()->count(5)
                ->has(Link::factory()->count(3))
                ->create();
        }
    }
}
