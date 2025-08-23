<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    /**
     * Get public settings (excluding sensitive data like email configuration)
     */
    public function index(Request $request)
    {
        try {
            $settings = Setting::all()->pluck('value', 'key');

            // Filter out sensitive email settings for public API
            $publicSettings = $settings->except([
                'mail_driver',
                'mail_host',
                'mail_port',
                'mail_username',
                'mail_password',
                'mail_encryption',
                'mail_from_address',
                'mail_from_name',
            ]);

            // Group settings by category for better organization
            $groupedSettings = [
                'general' => [
                    'site_title' => $publicSettings->get('site_title'),
                    'site_description' => $publicSettings->get('site_description'),
                    'site_keywords' => $publicSettings->get('site_keywords'),
                    'site_author' => $publicSettings->get('site_author'),
                    'contact_email' => $publicSettings->get('contact_email'),
                    'contact_phone' => $publicSettings->get('contact_phone'),
                    'contact_address' => $publicSettings->get('contact_address'),
                    'timezone' => $publicSettings->get('timezone', 'UTC'),
                    'date_format' => $publicSettings->get('date_format', 'Y-m-d'),
                    'time_format' => $publicSettings->get('time_format', 'H:i:s'),
                ],
                'social' => [
                    'facebook_url' => $publicSettings->get('facebook_url'),
                    'twitter_url' => $publicSettings->get('twitter_url'),
                    'linkedin_url' => $publicSettings->get('linkedin_url'),
                    'github_url' => $publicSettings->get('github_url'),
                    'instagram_url' => $publicSettings->get('instagram_url'),
                    'youtube_url' => $publicSettings->get('youtube_url'),
                ],
                'seo' => [
                    'meta_title' => $publicSettings->get('meta_title'),
                    'meta_description' => $publicSettings->get('meta_description'),
                    'og_title' => $publicSettings->get('og_title'),
                    'og_description' => $publicSettings->get('og_description'),
                    'og_image' => $publicSettings->get('og_image'),
                    'twitter_card' => $publicSettings->get('twitter_card'),
                    'google_analytics' => $publicSettings->get('google_analytics'),
                    'google_tag_manager' => $publicSettings->get('google_tag_manager'),
                ],
                'features' => [
                    'maintenance_mode' => (bool) $publicSettings->get('maintenance_mode', false),
                    'registration_enabled' => (bool) $publicSettings->get('registration_enabled', false),
                    'comments_enabled' => (bool) $publicSettings->get('comments_enabled', true),
                    'newsletter_enabled' => (bool) $publicSettings->get('newsletter_enabled', true),
                ]
            ];

            // Remove null values
            $groupedSettings = array_map(function ($group) {
                return array_filter($group, function ($value) {
                    return !is_null($value);
                });
            }, $groupedSettings);

            return response()->json([
                'success' => true,
                'data' => $groupedSettings,
                'meta' => [
                    'total_settings' => count($publicSettings),
                    'last_updated' => Setting::max('updated_at'),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch settings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch settings.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get specific setting by key
     */
    public function show(Request $request, $key)
    {
        try {
            // Prevent access to sensitive settings
            $restrictedKeys = [
                'mail_driver', 'mail_host', 'mail_port', 'mail_username', 
                'mail_password', 'mail_encryption', 'mail_from_address', 'mail_from_name'
            ];

            if (in_array($key, $restrictedKeys)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access to this setting is restricted.'
                ], 403);
            }

            $setting = Setting::where('key', $key)->first();

            if (!$setting) {
                return response()->json([
                    'success' => false,
                    'message' => 'Setting not found.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'key' => $setting->key,
                    'value' => $setting->value,
                    'updated_at' => $setting->updated_at,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch setting: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch setting.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}