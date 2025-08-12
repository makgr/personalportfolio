<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Certification extends Model
{
    use HasFactory;

    protected $table = 'certifications';
    protected $guarded = [];

    protected $fillable = [
        'name',
        'issuing_organization',
        'credential_id',
        'credential_url',
        'description',
        'issue_date',
        'expiry_date',
        'does_expire',
        'certificate_image',
        'skills',
        'order',
        'is_active',
    ];

    protected $casts = [
        'skills' => 'array',
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'does_expire' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeValid($query)
    {
        return $query->where(function ($q) {
            $q->where('does_expire', false)
                ->orWhere('expiry_date', '>', now());
        });
    }

    public function scopeExpired($query)
    {
        return $query->where('does_expire', true)
            ->where('expiry_date', '<=', now());
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc')
            ->orderBy('issue_date', 'desc');
    }

    // Accessors
    public function getIsValidAttribute()
    {
        if (!$this->does_expire) {
            return true;
        }

        return $this->expiry_date && $this->expiry_date->isFuture();
    }

    public function getStatusAttribute()
    {
        if (!$this->does_expire) {
            return 'valid';
        }

        return $this->expiry_date && $this->expiry_date->isFuture() ? 'valid' : 'expired';
    }
}
