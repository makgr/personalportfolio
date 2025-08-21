<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certification;
use App\Services\MediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CertificationController extends Controller
{
    protected MediaService $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->middleware(['auth', 'admin']);
        $this->mediaService = $mediaService;
    }

    /**
     * Get all certifications
     */
    public function index(Request $request)
    {
        try {
            $query = Certification::query();

            // Search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('issuing_organization', 'like', "%{$search}%")
                        ->orWhere('credential_id', 'like', "%{$search}%");
                });
            }

            // Status filter (valid/expired)
            if ($request->filled('status')) {
                if ($request->status === 'valid') {
                    $query->valid();
                } elseif ($request->status === 'expired') {
                    $query->expired();
                }
            }

            // Active filter
            if ($request->filled('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'issue_date');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $certifications = $request->has('all') ? $query->get() : $query->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $certifications
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch certifications: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch certifications',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store new certification
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'issuing_organization' => 'required|string|max:255',
            'credential_id' => 'nullable|string|max:255',
            'credential_url' => 'nullable|url',
            'description' => 'nullable|string',
            'issue_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'does_expire' => 'boolean',
            'certificate_image' => 'nullable|image|max:2048',
            'skills' => 'nullable|array',
            'skills.*' => 'string|max:50',
            'is_active' => 'boolean',
        ]);

        try {
            DB::transaction(function () use (&$certification, $request, $validated) {
                // Handle certificate image upload
                if ($request->hasFile('certificate_image')) {
                    $validated['certificate_image'] = $this->mediaService->upload($request->file('certificate_image'), 'certifications');
                }

                // If doesn't expire, clear expiry date
                if (!($validated['does_expire'] ?? false)) {
                    $validated['expiry_date'] = null;
                }

                $validated['order'] = Certification::max('order') + 1;

                $certification = Certification::create($validated);
            });

            return response()->json([
                'success' => true,
                'data' => $certification,
                'message' => 'Certification created successfully'
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create certification: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create certification',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update certification
     */
    public function update(Request $request, $id)
    {
        $certification = Certification::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'issuing_organization' => 'required|string|max:255',
            'credential_id' => 'nullable|string|max:255',
            'credential_url' => 'nullable|url',
            'description' => 'nullable|string',
            'issue_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'does_expire' => 'boolean',
            'certificate_image' => 'nullable|image|max:2048',
            'skills' => 'nullable|array',
            'skills.*' => 'string|max:50',
            'is_active' => 'boolean',
        ]);

        try {
            DB::transaction(function () use ($certification, $request, $validated) {
                // Handle certificate image upload
                if ($request->hasFile('certificate_image')) {
                    // Delete old image
                    if ($certification->certificate_image) {
                        $this->mediaService->delete($certification->certificate_image);
                    }
                    $validated['certificate_image'] = $this->mediaService->upload($request->file('certificate_image'), 'certifications');
                }

                // If doesn't expire, clear expiry date
                if (!($validated['does_expire'] ?? false)) {
                    $validated['expiry_date'] = null;
                }

                $certification->update($validated);
            });

            return response()->json([
                'success' => true,
                'data' => $certification->fresh(),
                'message' => 'Certification updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update certification: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update certification',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete certification
     */
    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $certification = Certification::findOrFail($id);

                // Delete certificate image
                if ($certification->certificate_image) {
                    $this->mediaService->delete($certification->certificate_image);
                }

                $certification->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Certification deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete certification: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete certification',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
