<?php
namespace App\Http\Controllers;
use App\Http\Requests\PageSettingsUpdateRequest;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class PageSettingsController extends Controller
{
    public function __construct(protected UserService $userService) {}

    public function update(PageSettingsUpdateRequest $request): RedirectResponse
    {
        $this->userService->updatePageSettings($request->user(), $request->validated());
        return Redirect::back()->with('success', 'Appearance updated.');
    }
    public function updatePhoto(Request $request): RedirectResponse
    {
        $request->validate(['photo' => ['required', 'image', 'max:1024']]);
        $this->userService->storeProfilePhoto($request->user(), $request->file('photo'));
        return Redirect::back()->with('success', 'Profile photo updated.');
    }
    public function deletePhoto(Request $request): RedirectResponse
    {
        $this->userService->deleteProfilePhoto($request->user());
        return Redirect::back()->with('success', 'Profile photo removed.');
    }
    public function updateBanner(Request $request): RedirectResponse
    {
        $request->validate([
            'banner' => ['required', 'image', 'max:2048'],
        ]);
        $this->userService->storeBanner($request->user(), $request->file('banner'));
        return Redirect::back()->with('success', 'Banner updated.');
    }
    public function deleteBanner(Request $request): RedirectResponse
    {
        $this->userService->deleteBanner($request->user());
        return Redirect::back()->with('success', 'Banner removed.');
    }
}
