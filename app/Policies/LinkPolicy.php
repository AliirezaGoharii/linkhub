<?php

namespace App\Policies;

use App\Models\Link;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LinkPolicy
{
    /**
     * Determine whether the user can update the link.
     */
    public function update(User $user, Link $link): Response
    {
        return $link->user_id === $user->id
            ? Response::allow()
            : Response::deny('You do not own this link.');
    }

    /**
     * Determine whether the user can delete the link.
     */
    public function delete(User $user, Link $link): Response
    {
        return $link->user_id === $user->id
            ? Response::allow()
            : Response::deny('You do not own this link.');
    }
}
