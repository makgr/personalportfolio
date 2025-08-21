<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\SeoSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Show settings page
     */
    public function index(Request $request)
    {
        try {
            $activeTab = $request->get('tab', 'general');
            
            // Get settings grouped by category
            $settingsGroups = Setting::all()->groupBy('group');
            
            // Get SEO settings
            $seoSettings = SeoSetting::all()->keyBy('page');
            
            // Define available setting groups
            $groups = [
                'general' => 'General Settings',
                'site' => 'Site Configuration', 
                'email' => 'Email Settings',
                'social' => 'Social Media',
                'analytics' => 'Analytics',
                'appearance' => 'Appearance'
            ];

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $settingsGroups,
                    'groups' => Setting::distinct('group')->pluck('group')
                ]);
            }

            return view('admin.settings.index', compact('settingsGroups', 'seoSettings', 'groups', 'activeTab'));
            
        } catch (\Exception $e) {
            Log::error('Failed to fetch settings: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch settings',
                    'error' => $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to load settings: ' . $e->getMessage());
        }
    }

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        // Handle different input formats (API vs Web form)
        if ($request->has('settings')) {
            // API format
            $validated = $request->validate([
                'settings' => 'required|array',
                'settings.*.key' => 'required|string',
                'settings.*.value' => 'nullable',
                'settings.*.type' => 'nullable|string|in:string,boolean,integer,float,json,array',
                'settings.*.group' => 'nullable|string',
                'settings.*.description' => 'nullable|string',
                'settings.*.is_public' => 'nullable|boolean'
            ]);
            $settingsData = $validated['settings'];
        } else {
            // Web form format - convert to API format
            $validated = $request->validate([
                'site_name' => 'nullable|string|max:255',
                'site_tagline' => 'nullable|string|max:255',
                'site_description' => 'nullable|string',
                'contact_email' => 'nullable|email',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string',
                'github_url' => 'nullable|url',
                'linkedin_url' => 'nullable|url',
                'twitter_url' => 'nullable|url',
                'facebook_url' => 'nullable|url',
                'instagram_url' => 'nullable|url',
                'google_analytics' => 'nullable|string',
                'maintenance_mode' => 'nullable|boolean',
                'items_per_page' => 'nullable|integer|min:1|max:100'
            ]);

            // Convert form data to settings format
            $settingsData = [];
            $fieldGroups = [
                'site_name' => ['group' => 'general', 'type' => 'string'],
                'site_tagline' => ['group' => 'general', 'type' => 'string'],
                'site_description' => ['group' => 'general', 'type' => 'string'],
                'contact_email' => ['group' => 'general', 'type' => 'string'],
                'phone' => ['group' => 'general', 'type' => 'string'],
                'address' => ['group' => 'general', 'type' => 'string'],
                'github_url' => ['group' => 'social', 'type' => 'string'],
                'linkedin_url' => ['group' => 'social', 'type' => 'string'],
                'twitter_url' => ['group' => 'social', 'type' => 'string'],
                'facebook_url' => ['group' => 'social', 'type' => 'string'],
                'instagram_url' => ['group' => 'social', 'type' => 'string'],
                'google_analytics' => ['group' => 'analytics', 'type' => 'string'],
                'maintenance_mode' => ['group' => 'site', 'type' => 'boolean'],
                'items_per_page' => ['group' => 'site', 'type' => 'integer']
            ];

            foreach ($validated as $key => $value) {
                if ($value !== null && isset($fieldGroups[$key])) {
                    $settingsData[] = [
                        'key' => $key,
                        'value' => $value,
                        'type' => $fieldGroups[$key]['type'],
                        'group' => $fieldGroups[$key]['group'],
                        'is_public' => in_array($key, ['site_name', 'site_tagline', 'site_description'])
                    ];
                }
            }
        }

        try {
            DB::transaction(function () use ($settingsData) {
                foreach ($settingsData as $setting) {
                    Setting::updateOrCreate(
                        ['key' => $setting['key']],
                        [
                            'value' => $this->prepareSettingValue($setting['value'], $setting['type'] ?? 'string'),
                            'type' => $setting['type'] ?? 'string',
                            'group' => $setting['group'] ?? 'general',
                            'description' => $setting['description'] ?? null,
                            'is_public' => $setting['is_public'] ?? false
                        ]
                    );
                }

                // Clear settings cache
                Cache::tags(['settings'])->flush();
            });

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Settings updated successfully'
                ]);
            }

            return redirect()->route('settings.index')->with('success', 'Settings updated successfully');

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
            
        } catch (\Exception $e) {
            Log::error('Failed to update settings: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update settings',
                    'error' => $e->getMessage()
                ], 500);
            }

            return redirect()->back()->withInput()->with('error', 'Failed to update settings: ' . $e->getMessage());
        }
    }

    /**
     * Prepare setting value based on type
     */
    private function prepareSettingValue($value, $type)
    {
        switch ($type) {
            case 'boolean':
                return (bool) $value;
            case 'integer':
                return (int) $value;
            case 'float':
                return (float) $value;
            case 'json':
            case 'array':
                return is_string($value) ? json_decode($value, true) : $value;
            default:
                return (string) $value;
        }
    }

    /**
     * Get SEO settings
     */
    public function seo(Request $request)
    {
        try {
            $page = $request->get('page', 'all');

            if ($page === 'all') {
                $seoSettings = SeoSetting::all();
            } else {
                $seoSettings = SeoSetting::where('page', $page)->first();
            }

            return response()->json([
                'success' => true,
                'data' => $seoSettings
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch SEO settings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update SEO settings
     */
    public function updateSeo(Request $request)
    {
        $validated = $request->validate([
            'page' => 'required|string',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:500',
            'keywords' => 'nullable|string',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string|max:500',
            'og_image' => 'nullable|string',
            'twitter_card' => 'nullable|string|in:summary,summary_large_image',
            'twitter_title' => 'nullable|string|max:255',
            'twitter_description' => 'nullable|string|max:500',
            'twitter_image' => 'nullable|string',
            'structured_data' => 'nullable|array',
            'custom_meta' => 'nullable|array'
        ]);

        try {
            $seoSetting = SeoSetting::updateOrCreate(
                ['page' => $validated['page']],
                $validated
            );

            return response()->json([
                'success' => true,
                'data' => $seoSetting,
                'message' => 'SEO settings updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update SEO settings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update SEO settings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear cache
     */
    public function clearCache()
    {
        try {
            Cache::flush();

            return response()->json([
                'success' => true,
                'message' => 'Cache cleared successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
