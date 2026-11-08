<?php

namespace App\Support;

use Illuminate\Validation\Rules\NotIn;

final class ReservedUsernames
{
    /**
     * Usernames that would collide with application routes or
     * carry a special meaning users could abuse for phishing.
     *
     * Route collisions would make the user's public page unreachable
     * (e.g. /login, /register, /dashboard), so they are rejected early.
     */
    public const USERNAMES = [
        // Application routes
        'login', 'logout', 'register', 'dashboard', 'profile',
        'password', 'password-confirmation', 'confirm-password',
        'forgot-password', 'reset-password', 'verify-email',
        'email', 'links', 'page-settings', 'up',
        // Common system / impersonation targets
        'admin', 'administrator', 'root', 'system', 'support',
        'help', 'api', 'staff', 'moderator', 'moderation',
        'official', 'linkhub', 'security', 'billing',
        // Well-known asset paths served from /
        'storage', 'static', 'assets', 'public', 'vendor',
        // Popular tooling that may be mounted later
        'telescope', 'horizon', 'nova', 'sanctum', 'pulse',
    ];

    /**
     * Build a "not in" validation rule for reserved usernames.
     */
    public static function rule(): NotIn
    {
        return new NotIn(self::USERNAMES);
    }
}
