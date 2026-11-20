<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class NewsletterSubscriber extends Model
{
    use \App\Models\Concerns\DisplaysInSiteTimezone, HasFactory, Notifiable;

    protected $table = 'newsletter_subscribers';

    protected $fillable = [
        'email',
        'name',
        'preferences',
        'status',
        'verification_token',
        'verified_at',
        'unsubscribed_at',
        'ip_address',
        'source',
    ];

    protected $casts = [
        'preferences' => 'array',
        'verified_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    // Scopes
    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeUnsubscribed($query)
    {
        return $query->where('status', 'unsubscribed');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'verified');
    }

    // Methods
    public function verify()
    {
        $this->update([
            'status' => 'verified',
            'verified_at' => now(),
            'verification_token' => null,
        ]);
    }

    public function unsubscribe()
    {
        $this->update([
            'status' => 'unsubscribed',
            'unsubscribed_at' => now(),
        ]);
    }

    public function generateVerificationToken()
    {
        $this->verification_token = Str::random(60);
        $this->save();

        return $this->verification_token;
    }

    public function hasPreference($preference)
    {
        return in_array($preference, $this->preferences ?? []);
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($subscriber) {
            if (! $subscriber->verification_token) {
                $subscriber->verification_token = Str::random(60);
            }
        });
    }
}
