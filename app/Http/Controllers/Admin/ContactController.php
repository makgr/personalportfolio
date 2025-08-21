<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Get all contact submissions
     */
    public function index(Request $request)
    {
        try {
            $query = ContactSubmission::query();

            // Search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('subject', 'like', "%{$search}%")
                        ->orWhere('message', 'like', "%{$search}%")
                        ->orWhere('company', 'like', "%{$search}%");
                });
            }

            // Status filter
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Type filter
            if ($request->filled('type')) {
                $query->where('type', $request->type);
            }

            // Date range filter
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $contacts = $query->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $contacts,
                'statistics' => [
                    'new' => ContactSubmission::where('status', 'new')->count(),
                    'read' => ContactSubmission::where('status', 'read')->count(),
                    'replied' => ContactSubmission::where('status', 'replied')->count(),
                    'archived' => ContactSubmission::where('status', 'archived')->count(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch contact submissions: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch contact submissions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get single contact submission
     */
    public function show($id)
    {
        try {
            $contact = ContactSubmission::findOrFail($id);

            // Mark as read if it's new
            if ($contact->status === 'new') {
                $contact->markAsRead();
            }

            return response()->json([
                'success' => true,
                'data' => $contact
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Contact submission not found'
            ], 404);
        }
    }

    /**
     * Mark as read
     */
    public function markAsRead($id)
    {
        try {
            $contact = ContactSubmission::findOrFail($id);
            $contact->markAsRead();

            return response()->json([
                'success' => true,
                'data' => $contact,
                'message' => 'Message marked as read'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark as read',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark as replied
     */
    public function markAsReplied(Request $request, $id)
    {
        $request->validate([
            'admin_notes' => 'nullable|string'
        ]);

        try {
            $contact = ContactSubmission::findOrFail($id);
            $contact->markAsReplied($request->admin_notes);

            return response()->json([
                'success' => true,
                'data' => $contact,
                'message' => 'Message marked as replied'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark as replied',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Archive contact
     */
    public function archive($id)
    {
        try {
            $contact = ContactSubmission::findOrFail($id);
            $contact->archive();

            return response()->json([
                'success' => true,
                'data' => $contact,
                'message' => 'Message archived'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to archive message',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete contact submission
     */
    public function destroy($id)
    {
        try {
            $contact = ContactSubmission::findOrFail($id);
            $contact->delete();

            return response()->json([
                'success' => true,
                'message' => 'Contact submission deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete contact submission: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete contact submission',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,mark_read,mark_replied,archive',
            'ids' => 'required|array',
            'ids.*' => 'exists:contact_submissions,id'
        ]);

        try {
            DB::transaction(function () use ($request) {
                $action = $request->action;
                $ids = $request->ids;

                switch ($action) {
                    case 'delete':
                        ContactSubmission::whereIn('id', $ids)->delete();
                        break;

                    case 'mark_read':
                        ContactSubmission::whereIn('id', $ids)
                            ->where('status', 'new')
                            ->update(['status' => 'read']);
                        break;

                    case 'mark_replied':
                        ContactSubmission::whereIn('id', $ids)
                            ->update([
                                'status' => 'replied',
                                'replied_at' => now()
                            ]);
                        break;

                    case 'archive':
                        ContactSubmission::whereIn('id', $ids)
                            ->update(['status' => 'archived']);
                        break;
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Bulk action completed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to perform bulk action',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
