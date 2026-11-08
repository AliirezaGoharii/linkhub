<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UserService
{
    /**
     * The exact payload rendered on a user's public page.
     *
     * This is the single allow-list of user data that may leave the
     * server for anonymous visitors: e-mail, verification status and
     * internal counters are deliberately excluded, and inactive links
     * are filtered out. updated_at is kept (only) so the frontend can
     * cache-bust media URLs after an upload.
     */
    public function publicPagePayload(User $user): array
    {
        return [
            'name' => $user->name,
            'username' => $user->username,
            'bio' => $user->bio,
            'theme_color' => $user->theme_color,
            'profile_photo_path' => $user->profile_photo_path,
            'banner_path' => $user->banner_path,
            'updated_at' => $user->updated_at?->toISOString(),
            'links' => $user->links
                ->where('is_active', true)
                ->values()
                ->map(fn ($link) => [
                    'id' => $link->id,
                    'title' => $link->title,
                    'url' => $link->url,
                ])
                ->all(),
        ];
    }

    /**
     * Update the account profile (name, username, email).
     *
     * Returns whether the email address was changed, so the caller
     * can invalidate the verified status if needed.
     */
    public function updateProfile(User $user, array $data): bool
    {
        $emailChanged = $user->email !== $data['email'];

        $user->forceFill([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
        ])->save();

        return $emailChanged;
    }

    public function updatePageSettings(User $user, array $data): User
    {
        $user->forceFill([
            'theme_color' => $data['theme_color'],
            'bio' => $data['bio'] ?? null,
        ])->save();

        return $user;
    }

    public function storeProfilePhoto(User $user, UploadedFile $photo): string
    {
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        $path = $photo->store('profile-photos', 'public');
        $user->forceFill(['profile_photo_path' => $path])->save();

        return $path;
    }

    public function deleteProfilePhoto(User $user): void
    {
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
            $user->forceFill(['profile_photo_path' => null])->save();
        }
    }

    public function storeBanner(User $user, UploadedFile $banner): string
    {
        if ($user->banner_path) {
            Storage::disk('public')->delete($user->banner_path);
        }

        $path = $banner->store('banners', 'public');
        $user->forceFill(['banner_path' => $path])->save();

        return $path;
    }

    public function deleteBanner(User $user): void
    {
        if ($user->banner_path) {
            Storage::disk('public')->delete($user->banner_path);
            $user->forceFill(['banner_path' => null])->save();
        }
    }

    /**
     * Remove every uploaded media file owned by the user.
     * Called before the account is deleted so no orphan files remain.
     */
    public function deleteAllMedia(User $user): void
    {
        Storage::disk('public')->delete(
            array_filter([$user->profile_photo_path, $user->banner_path])
        );
    }
}
