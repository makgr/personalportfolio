<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class ContactController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Display a listing of contact submissions
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = ContactSubmission::query();
            
            return DataTables::of($query)
                ->addColumn('sender_info', function ($contact) {
                    $html = '<div>';
                    $html .= '<div class="fw-bold">' . e($contact->name) . '</div>';
                    $html .= '<div class="text-muted small">' . e($contact->email) . '</div>';
                    if ($contact->company) {
                        $html .= '<div class="text-muted small"><i class="bx bx-buildings me-1"></i>' . e($contact->company) . '</div>';
                    }
                    $html .= '</div>';
                    return $html;
                })
                ->addColumn('type_badge', function ($contact) {
                    $colors = [
                        'general' => 'secondary',
                        'project' => 'primary',
                        'job' => 'success',
                        'collaboration' => 'info',
                        'other' => 'warning'
                    ];
                    $color = $colors[$contact->type] ?? 'secondary';
                    return '<span class="badge bg-' . $color . '">' . ucfirst($contact->type) . '</span>';
                })
                ->addColumn('status_badge', function ($contact) {
                    $colors = [
                        'new' => 'danger',
                        'read' => 'info', 
                        'replied' => 'success',
                        'archived' => 'secondary'
                    ];
                    $icons = [
                        'new' => 'bx-envelope',
                        'read' => 'bx-envelope-open',
                        'replied' => 'bx-check-circle',
                        'archived' => 'bx-archive'
                    ];
                    $color = $colors[$contact->status] ?? 'secondary';
                    $icon = $icons[$contact->status] ?? 'bx-envelope';
                    return '<span class="badge bg-' . $color . '"><i class="bx ' . $icon . ' me-1"></i>' . ucfirst($contact->status) . '</span>';
                })
                ->addColumn('priority_indicator', function ($contact) {
                    // High priority for new messages from project/job inquiries
                    if ($contact->status === 'new' && in_array($contact->type, ['project', 'job'])) {
                        return '<i class="bx bx-error text-danger" title="High Priority"></i>';
                    }
                    return '';
                })
                ->addColumn('budget_display', function ($contact) {
                    return $contact->budget_range ? '<span class="badge bg-success">' . e($contact->budget_range) . '</span>' : '';
                })
                ->addColumn('actions', function ($contact) {
                    return view('admin.contacts.partials.actions', compact('contact'));
                })
                ->editColumn('subject', function ($contact) {
                    return Str::limit($contact->subject, 40);
                })
                ->editColumn('message', function ($contact) {
                    return Str::limit($contact->message, 50);
                })
                ->editColumn('created_at', function ($contact) {
                    return $contact->created_at->format('M d, Y H:i');
                })
                ->rawColumns(['sender_info', 'type_badge', 'status_badge', 'priority_indicator', 'budget_display', 'actions'])
                ->make(true);
        }

        // Statistics for dashboard cards
        $statistics = [
            'new' => ContactSubmission::where('status', 'new')->count(),
            'read' => ContactSubmission::where('status', 'read')->count(),
            'replied' => ContactSubmission::where('status', 'replied')->count(),
            'archived' => ContactSubmission::where('status', 'archived')->count(),
            'total' => ContactSubmission::count(),
            'today' => ContactSubmission::whereDate('created_at', today())->count(),
            'this_week' => ContactSubmission::where('created_at', '>=', now()->startOfWeek())->count(),
            'this_month' => ContactSubmission::where('created_at', '>=', now()->startOfMonth())->count(),
        ];
        
        return view('admin.contacts.index', compact('statistics'));
    }

    /**
     * Display the specified contact submission
     */
    public function show(Request $request, $id)
    {
        try {
            $contact = ContactSubmission::findOrFail($id);

            // Mark as read if it's new
            if ($contact->status === 'new') {
                $contact->markAsRead();
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => $contact
                ]);
            }

            return view('admin.contacts.show', compact('contact'));
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Contact submission not found'
                ], 404);
            }

            return back()->with('error', 'Contact submission not found');
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
    public function destroy(Request $request, $contact)
    {
        try {
            $contactId = (int) $contact;
            $contactRecord = ContactSubmission::findOrFail($contactId);
            $contactRecord->delete();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Contact submission deleted successfully'
                ]);
            }

            return redirect()
                ->route('contacts.index')
                ->with('success', 'Contact submission deleted successfully');
        } catch (\Exception $e) {
            Log::error('Failed to delete contact submission: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete contact submission',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()
                ->with('error', 'Failed to delete contact submission: ' . $e->getMessage());
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
