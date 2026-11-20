<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiToken extends Model
{
    use \App\Models\Concerns\DisplaysInSiteTimezone, HasFactory;

    protected $table = 'api_tokens';

    protected $fillable = [
        'name',
        'token',
        'abilities',
        'last_used_at',
        'expires_at',
        'is_active',
        'ip_restriction',
        'rate_limit',
    ];

    protected $casts = [
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected $hidden = [
        'token',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    public function scopeExpired($query)
    {
        return $query->whereNotNull('expires_at')
            ->where('expires_at', '<=', now());
    }

    // Methods
    public function isValid()
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    public function canAccessFromIp($ip)
    {
        if (! $this->ip_restriction) {
            return true;
        }

        $allowedIps = explode(',', $this->ip_restriction);

        return in_array($ip, array_map('trim', $allowedIps));
    }

    public function recordUsage()
    {
        $this->update(['last_used_at' => now()]);
    }

    public function hasAbility($ability)
    {
        if (! $this->abilities) {
            return true; // No restrictions
        }

        $abilities = explode(',', $this->abilities);

        return in_array($ability, array_map('trim', $abilities));
    }

    // Static Methods
    public static function findByToken($token)
    {
        return static::where('token', hash('sha256', $token))->first();
    }

    public static function generateToken()
    {
        return bin2hex(random_bytes(40));
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($token) {
            if (! $token->token) {
                $plainToken = static::generateToken();
                $token->token = hash('sha256', $plainToken);
                $token->setAttribute('plain_token', $plainToken);
            }
        });
    }
}
