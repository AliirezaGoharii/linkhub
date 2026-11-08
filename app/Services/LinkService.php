<?php

namespace App\Services;

use App\Models\Link;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LinkService
{
    public function createLink(User $user, array $data): Link
    {
        $maxOrder = (int) $user->links()->max('ordering');

        return $user->links()->create([
            'title' => $data['title'],
            'url' => $data['url'],
            'ordering' => $maxOrder + 1,
        ]);
    }

    public function updateLink(Link $link, array $data): Link
    {
        $link->update([
            'title' => $data['title'],
            'url' => $data['url'],
        ]);

        return $link->refresh();
    }

    public function deleteLink(Link $link): void
    {
        $link->delete();
    }

    /**
     * Flip the link's visibility on the public page.
     */
    public function toggleLink(Link $link): void
    {
        $link->forceFill(['is_active' => ! $link->is_active])->save();
    }

    /**
     * Persist the new ordering of a user's links.
     *
     * A single CASE-based UPDATE keeps the reorder to one query
     * instead of one UPDATE per link.
     *
     * @param  array<int, int>  $linkIds  Link ids in their new order.
     */
    public function updateOrder(User $user, array $linkIds): void
    {
        if ($linkIds === []) {
            return;
        }

        $linkIds = array_values($linkIds);

        $cases = collect($linkIds)
            ->map(fn (int $id, int $index) => "when {$id} then {$index}")
            ->implode(' ');

        $ids = implode(',', $linkIds);

        // The request validation already guarantees every id belongs
        // to this user, but scoping the update keeps the guarantee in depth.
        DB::table('links')
            ->where('user_id', $user->id)
            ->whereIn('id', $linkIds)
            ->update(['ordering' => DB::raw("case id {$cases} end")]);
    }
}
