<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\ProfileRepositoryInterface;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    protected ProfileRepositoryInterface $profileRepository;

    public function __construct(ProfileRepositoryInterface $profileRepository)
    {
        $this->profileRepository = $profileRepository;
    }

    /**
     * Get profile information
     */
    public function index(Request $request)
    {
        try {
            $profile = $this->profileRepository->getActiveProfile();

            if (!$profile) {
                return response()->json([
                    'success' => false,
                    'message' => 'Profile not found.'
                ], 404);
            }

            // Get relevant settings for additional profile info
            $settings = Setting::whereIn('key', [
                'site_title',
                'site_description',
                'contact_email',
                'contact_phone',
                'contact_address',
                'facebook_url',
                'twitter_url',
                'linkedin_url',
                'github_url',
                'instagram_url'
            ])->pluck('value', 'key');

            $data = [
                'id' => $profile->id,
                'name' => $profile->full_name,
                'email' => $profile->email,
                'title' => $profile->title,
                'bio' => $profile->bio,
                'description' => $profile->description,
                'avatar' => $profile->avatar ? asset($profile->avatar) : null,
                'cover_image' => $profile->cover_image ? asset($profile->cover_image) : null,
                'phone' => $profile->phone,
                'location' => $profile->location,
                'website' => $profile->website,
                'resume_url' => $profile->resume_url ? asset($profile->resume_url) : null,
                'years_experience' => $profile->years_experience,
                'projects_completed' => $profile->projects_completed,
                'happy_clients' => $profile->happy_clients,
                'social_links' => $profile->social_links ?: [],
                'skills_summary' => $profile->skills_summary ?: [],
                'is_available' => $profile->is_available,
                'hourly_rate' => $profile->hourly_rate,
                'currency' => $profile->currency,
                'timezone' => $profile->timezone,
                'languages' => $profile->languages ?: [],

                // Additional info from settings
                'site_info' => [
                    'title' => $settings->get('site_title'),
                    'description' => $settings->get('site_description'),
                    'contact' => [
                        'email' => $settings->get('contact_email'),
                        'phone' => $settings->get('contact_phone'),
                        'address' => $settings->get('contact_address'),
                    ],
                    'social' => [
                        'facebook' => $settings->get('facebook_url'),
                        'twitter' => $settings->get('twitter_url'),
                        'linkedin' => $settings->get('linkedin_url'),
                        'github' => $settings->get('github_url'),
                        'instagram' => $settings->get('instagram_url'),
                    ]
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch profile: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch profile.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
