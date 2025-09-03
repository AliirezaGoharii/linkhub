<?php
namespace App\Http\Controllers;

use App\Http\Requests\LinkOrderUpdateRequest;
use App\Http\Requests\LinkStoreRequest;
use App\Http\Requests\LinkUpdateRequest;
use App\Models\Link;
use App\Services\LinkService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;

class LinkController extends Controller
{
    public function __construct(protected LinkService $linkService) {}

    public function store(LinkStoreRequest $request): RedirectResponse
    {
        $this->linkService->createLink($request->user(), $request->validated());
        return Redirect::route('dashboard')->with('success', 'Link created.');
    }

    public function update(LinkUpdateRequest $request, Link $link): RedirectResponse
    {
        $this->authorize('update', $link);
        $this->linkService->updateLink($link, $request->validated());
        return Redirect::back()->with('success', 'Link updated.');
    }

    public function destroy(Link $link): RedirectResponse
    {
        $this->authorize('delete', $link);
        $this->linkService->deleteLink($link);
        return Redirect::back()->with('success', 'Link deleted.');
    }

    public function updateOrder(LinkOrderUpdateRequest $request): RedirectResponse
    {
        $this->linkService->updateOrder($request->user(), $request->validated('links'));
        return Redirect::back()->with('success', 'Link order updated.');
    }
}
