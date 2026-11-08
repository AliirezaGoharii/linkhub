<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Services\AnalyticsService;
use Illuminate\Http\Response;

class LinkClickController extends Controller
{
    public function __construct(
        protected AnalyticsService $analytics,
    ) {}

    /**
     * Record a click on a public link.
     *
     * Called fire-and-forget by the public page; returns an empty
     * response so the caller never waits on it.
     */
    public function __invoke(Link $link): Response
    {
        $link->recordClick();

        $this->analytics->recordClick($link->user);

        return response()->noContent();
    }
}
