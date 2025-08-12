<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SeoSetting extends Model
{
    use HasFactory;

    protected $table = 'seo_settings';
    protected $guarded = [];

    protected $fillable = [
        'page',
        'title',
        'description',
        'keywords',
        'og_title',
        'og_description',
        'og_image',
        'twitter_card',
        'twitter_title',
        'twitter_description',
        'twitter_image',
        'structured_data',
        'custom_meta',
    ];

    protected $casts = [
        'structured_data' => 'array',
        'custom_meta' => 'array',
    ];

    // Static Methods
    public static function getForPage($page)
    {
        return static::where('page', $page)->first();
    }

    public static function updateOrCreateForPage($page, array $data)
    {
        return static::updateOrCreate(
            ['page' => $page],
            $data
        );
    }

    // Methods
    public function generateMetaTags()
    {
        $tags = [];

        // Basic meta tags
        $tags[] = '<title>' . e($this->title) . '</title>';
        $tags[] = '<meta name="description" content="' . e($this->description) . '">';

        if ($this->keywords) {
            $tags[] = '<meta name="keywords" content="' . e($this->keywords) . '">';
        }

        // Open Graph tags
        if ($this->og_title) {
            $tags[] = '<meta property="og:title" content="' . e($this->og_title) . '">';
        }
        if ($this->og_description) {
            $tags[] = '<meta property="og:description" content="' . e($this->og_description) . '">';
        }
        if ($this->og_image) {
            $tags[] = '<meta property="og:image" content="' . e($this->og_image) . '">';
        }

        // Twitter Card tags
        if ($this->twitter_card) {
            $tags[] = '<meta name="twitter:card" content="' . e($this->twitter_card) . '">';
        }
        if ($this->twitter_title) {
            $tags[] = '<meta name="twitter:title" content="' . e($this->twitter_title) . '">';
        }
        if ($this->twitter_description) {
            $tags[] = '<meta name="twitter:description" content="' . e($this->twitter_description) . '">';
        }
        if ($this->twitter_image) {
            $tags[] = '<meta name="twitter:image" content="' . e($this->twitter_image) . '">';
        }

        // Custom meta tags
        if ($this->custom_meta) {
            foreach ($this->custom_meta as $name => $content) {
                $tags[] = '<meta name="' . e($name) . '" content="' . e($content) . '">';
            }
        }

        // Structured data
        if ($this->structured_data) {
            $tags[] = '<script type="application/ld+json">' . json_encode($this->structured_data) . '</script>';
        }

        return implode("\n", $tags);
    }
}
