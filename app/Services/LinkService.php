<?php
namespace App\Services;

use App\Models\Link;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LinkService
{
    public function createLink(User $user, array $data): Link
    {
        $maxOrder = $user->links()->max('ordering');
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
        return $link;
    }

    public function deleteLink(Link $link): void
    {
        $link->delete();
    }

    public function updateOrder(User $user, array $linkIds): void
    {
        DB::transaction(function () use ($user, $linkIds) {
            foreach ($linkIds as $index => $linkId) {
                $user->links()->where('id', $linkId)->update(['ordering' => $index]);
            }
        });
    }
}
