<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PublicPageController extends Controller {
    /**
     * Handle the incoming request.
     */
    public function __invoke(User $user): Response {
        $user->refresh();

        $user->load('links');

        return Inertia::render('Public/Show', [
            'user' => $user,
        ]);
    }
}
