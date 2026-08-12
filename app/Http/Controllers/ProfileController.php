<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validated();

        if ($user->isMember()) {
            unset($validated['nisn']);
        }

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if ($request->boolean('remove_profile_image') && $user->profile_image) {
            $this->deleteProfileImage($user->profile_image);
            $user->profile_image = null;
        }

        if ($request->hasFile('profile_image')) {
            $image = $request->file('profile_image');
            $destinationPath = 'images/profiles/';
            $filename = 'profile_' . $user->id . '_' . date('YmdHis') . '.' . $image->guessExtension();

            $this->deleteProfileImage($user->profile_image);

            $image->move(public_path($destinationPath), $filename);
            $user->profile_image = $destinationPath . $filename;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    private function deleteProfileImage(?string $image): void
    {
        if (!$image || !str_starts_with($image, 'images/profiles/')) {
            return;
        }

        $path = public_path($image);
        if (file_exists($path)) {
            @unlink($path);
        }
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
