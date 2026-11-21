<?php

// Author: Mohammad Ali Khan

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use function Illuminate\Support\defer;

/**
 * Tells the Next.js frontend to drop cached API data after an admin change, so
 * edits show up at once instead of after the frontend's cache time (5 min).
 *
 * Tags are collected for the whole request and sent in one POST after the
 * response, so saving in the admin is never slowed down or broken by it.
 */
class FrontendRevalidator
{
    /** Model class => frontend cache tags (see src/lib/api in the frontend). */
    public const MODEL_TAGS = [
        \App\Models\Profile::class => ['profile'],
        \App\Models\Project::class => ['projects'],
        \App\Models\BlogPost::class => ['blog-posts'],
        \App\Models\Tag::class => ['blog-posts'],
        \App\Models\Category::class => ['categories', 'projects', 'blog-posts'],
        \App\Models\Service::class => ['services'],
        \App\Models\Skill::class => ['skills'],
        \App\Models\Experience::class => ['experiences'],
        \App\Models\Education::class => ['education'],
        \App\Models\Training::class => ['training'],
        \App\Models\Certification::class => ['training'],
        \App\Models\Team::class => ['teams'],
        \App\Models\Testimonial::class => ['testimonials'],
        // the profile API also returns contact and social values from settings
        \App\Models\Setting::class => ['settings', 'profile'],
        \App\Models\SeoSetting::class => ['settings', 'profile'],
    ];

    /** @var array<string, true> tags waiting to be sent this request */
    private array $pending = [];

    public function queue(array $tags): void
    {
        if (! $this->enabled()) {
            return;
        }

        $first = $this->pending === [];
        foreach ($tags as $tag) {
            $this->pending[$tag] = true;
        }

        if ($first) {
            defer(fn () => $this->flush());
        }
    }

    public function flush(): void
    {
        $tags = array_keys($this->pending);
        $this->pending = [];
        if ($tags === [] || ! $this->enabled()) {
            return;
        }

        try {
            $response = Http::timeout(5)
                ->withHeaders(['x-revalidate-secret' => config('services.frontend.revalidate_secret')])
                ->post(rtrim(config('services.frontend.url'), '/') . '/api/revalidate', ['tags' => $tags]);

            if ($response->failed()) {
                Log::warning('Frontend revalidate failed', ['status' => $response->status(), 'tags' => $tags]);
            }
        } catch (\Throwable $e) {
            // The frontend still refreshes on its own after its cache time.
            Log::warning('Frontend revalidate unreachable', ['error' => $e->getMessage(), 'tags' => $tags]);
        }
    }

    private function enabled(): bool
    {
        return filled(config('services.frontend.url')) && filled(config('services.frontend.revalidate_secret'));
    }
}
