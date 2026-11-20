<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use \App\Models\Concerns\DisplaysInSiteTimezone, HasFactory;

    protected $table = 'services';

    protected $fillable = [
        'title',
        'slug',
        'short_description',
        'description',
        'icon',
        'image',
        'features',
        'technologies',
        'starting_price',
        'price_unit',
        'order',
        'is_active',
    ];

    protected $casts = [
        'features' => 'array',
        'technologies' => 'array',
        'starting_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function media()
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    // Accessors
    public function getFormattedPriceAttribute()
    {
        if (! $this->starting_price) {
            return 'Contact for pricing';
        }

        $price = number_format($this->starting_price, 2);

        return $this->price_unit
            ? '$'.$price.' / '.$this->price_unit
            : '$'.$price;
    }
}
