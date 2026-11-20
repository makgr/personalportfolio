<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use \App\Models\Concerns\DisplaysInSiteTimezone, HasFactory;

    protected $table = 'media';

    protected $fillable = [
        'filename',
        'original_name',
        'mime_type',
        'path',
        'disk',
        'url',
        'size',
        'metadata',
        'alt_text',
        'caption',
        'mediable_type',
        'mediable_id',
        'collection',
        'order',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    // Relationships
    public function mediable()
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeByCollection($query, $collection)
    {
        return $query->where('collection', $collection);
    }

    public function scopeImages($query)
    {
        return $query->where('mime_type', 'like', 'image/%');
    }

    public function scopeVideos($query)
    {
        return $query->where('mime_type', 'like', 'video/%');
    }

    public function scopeDocuments($query)
    {
        return $query->whereIn('mime_type', [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    // Accessors
    public function getFullUrlAttribute()
    {
        if ($this->url) {
            return $this->url;
        }

        return Storage::disk($this->disk)->url($this->path);
    }

    public function getFormattedSizeAttribute()
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2).' '.$units[$i];
    }

    public function getIsImageAttribute()
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    public function getIsVideoAttribute()
    {
        return str_starts_with($this->mime_type, 'video/');
    }

    // Methods
    public function deleteFile()
    {
        return Storage::disk($this->disk)->delete($this->path);
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($media) {
            $media->deleteFile();
        });
    }
}
