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
     * Get all settings
     */
    public function index(Request $request)
    {
        try {
            $group = $request->get('group', 'general');

            if ($group === 'all') {
                $settings = Setting::all()->groupBy('group');
            } else {
                $settings = Setting::where('group', $group)->get();
            }

            return response()->json([
                'success' => true,
                'data' => $settings,
                'groups' => Setting::distinct('group')->pluck('group')
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch settings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch settings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'nullable',
            'settings.*.type' => 'nullable|string|in:string,boolean,integer,float,json,array',
            'settings.*.group' => 'nullable|string',
            'settings.*.description' => 'nullable|string',
            'settings.*.is_public' => 'nullable|boolean'
        ]);

        try {
            DB::transaction(function () use ($validated) {
                foreach ($validated['settings'] as $setting) {
                    Setting::updateOrCreate(
                        ['key' => $setting['key']],
                        [
                            'value' => Setting::prepareValue($setting['value'], $setting['type'] ?? 'string'),
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

            return response()->json([
                'success' => true,
                'message' => 'Settings updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update settings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update settings',
                'error' => $e->getMessage()
            ], 500);
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
