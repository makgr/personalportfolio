<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Http\Request;
use App\Services\MediaService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    protected MediaService $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->middleware(['auth', 'admin']);
        $this->mediaService = $mediaService;
    }

    /**
     * Get profile
     */
    public function show()
    {
        try {
            $profile = Profile::first();
            $user = Auth::user();

            return response()->json([
                'success' => true,
                'data' => [
                    'profile' => $profile,
                    'user' => $user
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update profile
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            // Profile fields
            'full_name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'bio' => 'required|string',
            'short_bio' => 'nullable|string|max:500',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|max:2048',
            'resume' => 'nullable|mimes:pdf|max:5120',
            'social_links' => 'nullable|array',
            'social_links.github' => 'nullable|url',
            'social_links.linkedin' => 'nullable|url',
            'social_links.twitter' => 'nullable|url',
            'social_links.facebook' => 'nullable|url',
            'social_links.instagram' => 'nullable|url',
            'social_links.youtube' => 'nullable|url',
            'meta_tags' => 'nullable|array',

            // User account fields
            'user_name' => 'nullable|string|max:255',
            'user_email' => 'nullable|email|unique:users,email,' . Auth::user()->id,
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:8|confirmed'
        ]);

        try {
            DB::transaction(function () use ($request, $validated) {
                // Get or create profile
                $profile = Profile::firstOrNew();

                // Handle avatar upload
                if ($request->hasFile('avatar')) {
                    if ($profile->avatar_url) {
                        $this->mediaService->delete($profile->avatar_url);
                    }
                    $validated['avatar_url'] = $this->mediaService->upload($request->file('avatar'), 'profile');
                }

                // Handle resume upload
                if ($request->hasFile('resume')) {
                    if ($profile->resume_url) {
                        $this->mediaService->delete($profile->resume_url);
                    }
                    $validated['resume_url'] = $this->mediaService->upload($request->file('resume'), 'profile');
                }

                // Update profile
                $profileData = array_filter($validated, function ($key) {
                    return !str_starts_with($key, 'user_') && !in_array($key, ['current_password', 'new_password', 'new_password_confirmation', 'avatar', 'resume']);
                }, ARRAY_FILTER_USE_KEY);

                $profile->fill($profileData);
                $profile->save();

                // Update user account if provided
                $user = auth()->user();

                if ($request->filled('user_name')) {
                    $user->name = $validated['user_name'];
                }

                if ($request->filled('user_email')) {
                    $user->email = $validated['user_email'];
                }

                // Update password if provided
                if ($request->filled('new_password')) {
                    if (!Hash::check($validated['current_password'], $user->password)) {
                        throw new \Exception('Current password is incorrect');
                    }
                    $user->password = Hash::make($validated['new_password']);
                }

                $user->save();
            });

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update profile: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() === 'Current password is incorrect'
                    ? 'Current password is incorrect'
                    : 'Failed to update profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
