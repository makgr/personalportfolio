<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Analytics extends Model
{
    use \App\Models\Concerns\DisplaysInSiteTimezone, HasFactory;

    protected $table = 'analytics';

    protected $fillable = [
        'session_id',
        'visitor_id',
        'page_type',
        'page_id',
        'page_url',
        'referrer',
        'ip_address',
        'user_agent',
        'browser',
        'device_type',
        'os',
        'country',
        'city',
        'time_on_page',
        'events',
        'visited_at',
    ];

    protected $casts = [
        'events' => 'array',
        'visited_at' => 'datetime',
    ];

    // Scopes
    public function scopeByPageType($query, $type)
    {
        return $query->where('page_type', $type);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('visited_at', [$startDate, $endDate]);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('visited_at', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('visited_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('visited_at', now()->month)
            ->whereYear('visited_at', now()->year);
    }

    public function scopeByDevice($query, $device)
    {
        return $query->where('device_type', $device);
    }

    public function scopeUniqueVisitors($query)
    {
        return $query->select('visitor_id')->distinct();
    }

    // Static Methods for Analytics
    public static function recordPageView($data)
    {
        return static::create(array_merge($data, [
            'visited_at' => now(),
        ]));
    }

    public static function getPageViews($pageType = null, $days = 30)
    {
        $query = static::where('visited_at', '>=', now()->subDays($days));

        if ($pageType) {
            $query->where('page_type', $pageType);
        }

        return $query->count();
    }

    public static function getUniqueVisitors($days = 30)
    {
        return static::where('visited_at', '>=', now()->subDays($days))
            ->distinct('visitor_id')
            ->count('visitor_id');
    }

    public static function getTopPages($limit = 10, $days = 30)
    {
        return static::where('visited_at', '>=', now()->subDays($days))
            ->select('page_url', DB::raw('COUNT(*) as views'))
            ->groupBy('page_url')
            ->orderBy('views', 'desc')
            ->limit($limit)
            ->get();
    }

    public static function getTopReferrers($limit = 10, $days = 30)
    {
        return static::where('visited_at', '>=', now()->subDays($days))
            ->whereNotNull('referrer')
            ->where('referrer', '!=', '')
            ->select('referrer', DB::raw('COUNT(*) as count'))
            ->groupBy('referrer')
            ->orderBy('count', 'desc')
            ->limit($limit)
            ->get();
    }

    public static function getDeviceStats($days = 30)
    {
        return static::where('visited_at', '>=', now()->subDays($days))
            ->select('device_type', DB::raw('COUNT(*) as count'))
            ->groupBy('device_type')
            ->get()
            ->pluck('count', 'device_type');
    }
}
