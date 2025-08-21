<?php

namespace App\Traits;

trait HasSeo
{
    public function getSeoTitle()
    {
        if (isset($this->meta_tags['title']) && !empty($this->meta_tags['title'])) {
            return $this->meta_tags['title'];
        }

        return $this->title ?? $this->name ?? 'Untitled';
    }

    public function getSeoDescription()
    {
        if (isset($this->meta_tags['description']) && !empty($this->meta_tags['description'])) {
            return $this->meta_tags['description'];
        }

        if (isset($this->excerpt) && !empty($this->excerpt)) {
            return $this->excerpt;
        }

        if (isset($this->short_description) && !empty($this->short_description)) {
            return $this->short_description;
        }

        if (isset($this->description) && !empty($this->description)) {
            return \Str::limit(strip_tags($this->description), 160);
        }

        return 'Professional portfolio and blog.';
    }

    public function getSeoKeywords()
    {
        if (isset($this->meta_tags['keywords']) && !empty($this->meta_tags['keywords'])) {
            return $this->meta_tags['keywords'];
        }

        $keywords = [];

        // Add category name if available
        if (isset($this->category) && $this->category) {
            $keywords[] = $this->category->name;
        }

        // Add tags if available
        if (method_exists($this, 'tags') && $this->tags()->exists()) {
            $keywords = array_merge($keywords, $this->tags->pluck('name')->toArray());
        } elseif (method_exists($this, 'tagModels') && $this->tagModels()->exists()) {
            $keywords = array_merge($keywords, $this->tagModels->pluck('name')->toArray());
        }

        return implode(', ', $keywords);
    }

    public function getSeoImage()
    {
        if (isset($this->featured_image) && !empty($this->featured_image)) {
            return asset('storage/' . $this->featured_image);
        }

        if (isset($this->image) && !empty($this->image)) {
            return asset('storage/' . $this->image);
        }

        if (isset($this->avatar_url) && !empty($this->avatar_url)) {
            return asset('storage/' . $this->avatar_url);
        }

        return asset('images/default-og.jpg');
    }

    public function getSeoUrl()
    {
        if (isset($this->slug)) {
            $routeName = strtolower(class_basename($this)) . '.show';
            try {
                return route($routeName, $this->slug);
            } catch (\Exception $e) {
                return url()->current();
            }
        }

        return url()->current();
    }
}