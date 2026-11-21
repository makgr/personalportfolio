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
                'full_name' => $profile->full_name,
                'title' => $profile->title,
                'subtitle' => $profile->subtitle,
                'bio' => $profile->bio,
                'short_bio' => $profile->short_bio,
                'email' => $profile->email,
                'phone' => $profile->phone,
                'location' => $profile->location,
                'avatar_url' => $profile->avatar_url ? asset($profile->avatar_url) : null,
                'hero_image_url' => $profile->hero_image_url ? asset($profile->hero_image_url) : null,
                'hero_cutout_url' => $profile->hero_cutout_url ? asset($profile->hero_cutout_url) : null,
                'resume_url' => $profile->resume_url ? asset($profile->resume_url) : null,
                
                // Profile Statistics
                'years_of_experience' => $profile->years_of_experience ?? 0,
                'total_projects' => $profile->total_projects ?? 0,
                'happy_clients' => $profile->happy_clients ?? 0,
                
                'social_links' => $profile->social_links ?: [],
                'meta_tags' => $profile->meta_tags ?: [],
                'is_active' => $profile->is_active,

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
