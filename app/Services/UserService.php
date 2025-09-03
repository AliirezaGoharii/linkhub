<?php
namespace App\Services;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UserService
{
    public function updatePageSettings(User $user, array $data): User
    {
        $user->forceFill(['theme_color' => $data['theme_color']])->save();
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
}
