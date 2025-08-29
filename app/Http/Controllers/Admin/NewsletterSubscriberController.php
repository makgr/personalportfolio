<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use App\Exports\NewsletterSubscribersExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class NewsletterSubscriberController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Display a listing of newsletter subscribers
     */
    public function index(Request $request)
    {
        try {
            $query = NewsletterSubscriber::query();

            // Search by email
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where('email', 'LIKE', "%{$search}%");
            }

            // Filter by date range
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            // Order by creation date
            $subscribers = $query->orderBy('created_at', 'desc')
                                ->paginate(15)
                                ->withQueryString();

            // Get statistics
            $stats = [
                'total' => NewsletterSubscriber::count(),
                'today' => NewsletterSubscriber::whereDate('created_at', today())->count(),
                'this_week' => NewsletterSubscriber::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                'this_month' => NewsletterSubscriber::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
            ];

            return view('admin.newsletter-subscribers.index', compact('subscribers', 'stats'));

        } catch (\Exception $e) {
            Log::error('Failed to fetch newsletter subscribers: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load newsletter subscribers.');
        }
    }

    /**
     * Display the specified subscriber
     */
    public function show(NewsletterSubscriber $newsletterSubscriber)
    {
        try {
            return view('admin.newsletter-subscribers.show', compact('newsletterSubscriber'));
        } catch (\Exception $e) {
            Log::error('Failed to fetch newsletter subscriber: ' . $e->getMessage());
            return redirect()->route('admin.newsletter-subscribers.index')->with('error', 'Subscriber not found.');
        }
    }

    /**
     * Update subscriber status
     */
    public function update(Request $request, NewsletterSubscriber $newsletterSubscriber)
    {
        try {
            $request->validate([
                'status' => 'required|in:pending,verified,unsubscribed'
            ]);

            $newsletterSubscriber->status = $request->status;
            
            if ($request->status === 'verified' && !$newsletterSubscriber->verified_at) {
                $newsletterSubscriber->verified_at = now();
            } elseif ($request->status === 'unsubscribed' && !$newsletterSubscriber->unsubscribed_at) {
                $newsletterSubscriber->unsubscribed_at = now();
            }

            $newsletterSubscriber->save();

            return response()->json([
                'success' => true,
                'message' => 'Subscriber status updated successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update subscriber: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update subscriber status.'
            ], 500);
        }
    }

    /**
     * Remove the specified subscriber
     */
    public function destroy(NewsletterSubscriber $newsletterSubscriber)
    {
        try {
            $newsletterSubscriber->delete();

            return response()->json([
                'success' => true,
                'message' => 'Subscriber deleted successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to delete subscriber: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete subscriber.'
            ], 500);
        }
    }

    /**
     * Export newsletter subscribers
     */
    public function export(Request $request)
    {
        try {
            $filters = $request->only(['search', 'date_from', 'date_to']);
            
            $filename = 'newsletter-subscribers-' . now()->format('Y-m-d-H-i-s') . '.xlsx';
            
            return Excel::download(new NewsletterSubscribersExport($filters), $filename);

        } catch (\Exception $e) {
            Log::error('Failed to export newsletter subscribers: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to export subscribers.');
        }
    }
}
