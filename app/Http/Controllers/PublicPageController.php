<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AnalyticsService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PublicPageController extends Controller
{
    public function __construct(
        protected UserService $userService,
        protected AnalyticsService $analytics,
    ) {}

    /**
     * Render a user's public link-in-bio page and count a profile view
     * whenever the page is not viewed by its owner.
     *
     * Only the fields allow-listed by UserService::publicPagePayload()
     * ever reach the visitor — the owner's e-mail address is never
     * part of the public page payload.
     */
    public function __invoke(Request $request, User $user): Response
    {
        // Count a view for everyone except the owner.
        if ($request->user() === null || $request->user()->isNot($user)) {
            $user->timestamps = false;
            $user->increment('profile_views');
            $user->timestamps = true;

            $this->analytics->recordView($user);
        }

        return Inertia::render('Public/Show', [
            'user' => $this->userService->publicPagePayload($user),
        ]);
    }
}
