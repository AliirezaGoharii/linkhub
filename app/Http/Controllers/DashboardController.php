<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\User;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        protected AnalyticsService $analytics,
    ) {}

    /**
     * Display the link management dashboard with lightweight analytics.
     */
    public function __invoke(Request $request): Response
    {
        /** @var User $user */
        $user = $request->user();

        $links = $user->links()->get();

        $topLink = $links
            ->filter(fn (Link $link) => $link->click_count > 0)
            ->sortByDesc('click_count')
            ->first();

        return Inertia::render('Dashboard', [
            'links' => fn () => $links,
            'analytics' => fn () => $this->analytics->dailySeries($user, 14),
            'stats' => [
                'profile_views' => $user->profile_views,
                'total_clicks' => $links->sum('click_count'),
                'link_count' => $links->count(),
                'top_link' => $topLink ? [
                    'id' => $topLink->id,
                    'title' => $topLink->title,
                    'click_count' => $topLink->click_count,
                ] : null,
            ],
        ]);
    }
}
