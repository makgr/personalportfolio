<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContactSubmission extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'contact_submissions';
    protected $guarded = [];

    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'subject',
        'message',
        'type',
        'budget_range',
        'services_interested',
        'status',
        'admin_notes',
        'ip_address',
        'user_agent',
        'metadata',
        'replied_at',
    ];

    protected $casts = [
        'services_interested' => 'array',
        'metadata' => 'array',
        'replied_at' => 'datetime',
    ];

    // Scopes
    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    public function scopeRead($query)
    {
        return $query->where('status', 'read');
    }

    public function scopeReplied($query)
    {
        return $query->where('status', 'replied');
    }

    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Methods
    public function markAsRead()
    {
        if ($this->status === 'new') {
            $this->update(['status' => 'read']);
        }
    }

    public function markAsReplied($notes = null)
    {
        $this->update([
            'status' => 'replied',
            'replied_at' => now(),
            'admin_notes' => $notes ?: $this->admin_notes,
        ]);
    }

    public function archive()
    {
        $this->update(['status' => 'archived']);
    }
}
