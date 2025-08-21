<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait Sluggable
{
    protected static function bootSluggable()
    {
        static::creating(function ($model) {
            if (empty($model->slug) && !empty($model->title)) {
                $model->slug = static::generateUniqueSlug($model->title, get_class($model));
            } elseif (empty($model->slug) && !empty($model->name)) {
                $model->slug = static::generateUniqueSlug($model->name, get_class($model));
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('title') || $model->isDirty('name')) {
                $titleField = isset($model->title) ? 'title' : 'name';
                if (!empty($model->{$titleField})) {
                    $model->slug = static::generateUniqueSlug(
                        $model->{$titleField}, 
                        get_class($model),
                        $model->id
                    );
                }
            }
        });
    }

    protected static function generateUniqueSlug($title, $modelClass, $ignoreId = null)
    {
        $originalSlug = Str::slug($title);
        $slug = $originalSlug;
        $counter = 1;

        while (static::slugExists($slug, $modelClass, $ignoreId)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    protected static function slugExists($slug, $modelClass, $ignoreId = null)
    {
        $query = $modelClass::where('slug', $slug);
        
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query->exists();
    }
}