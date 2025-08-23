<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    protected CategoryService $categoryService;
    protected CategoryRepositoryInterface $categoryRepository;

    public function __construct(
        CategoryService $categoryService,
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->middleware(['auth', 'admin']);
        $this->categoryService = $categoryService;
        $this->categoryRepository = $categoryRepository;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Category::query()->orderBy('order', 'asc');

            return DataTables::of($query)
                ->addColumn('status_badge', function ($category) {
                    $badge = $category->is_active ? 'success' : 'secondary';
                    $text = $category->is_active ? 'Active' : 'Inactive';
                    return '<span class="badge bg-label-' . $badge . '">' . $text . '</span>';
                })
                ->addColumn('type_badge', function ($category) {
                    $colors = ['project' => 'primary', 'blog' => 'info'];
                    $color = $colors[$category->type] ?? 'secondary';
                    return '<span class="badge bg-label-' . $color . '">' . ucfirst($category->type) . '</span>';
                })
                ->addColumn('color_preview', function ($category) {
                    return $category->color ? '<span class="d-inline-block rounded" style="width: 20px; height: 20px; background-color: ' . $category->color . ';"></span>' : '-';
                })
                ->addColumn('icon_preview', function ($category) {
                    return $category->icon ? '<i class="' . $category->icon . '"></i>' : '-';
                })
                ->addColumn('content_count', function ($category) {
                    $projectCount = $category->projects()->count();
                    $blogCount = $category->blogPosts()->count();
                    $total = $projectCount + $blogCount;
                    return $total > 0 ? $total . ' item(s)' : 'No content';
                })
                ->addColumn('action', function ($category) {
                    $canDelete = $this->categoryRepository->canDelete($category->id);
                    $deleteClass = $canDelete ? 'btn-outline-danger' : 'btn-outline-secondary';
                    $deleteDisabled = $canDelete ? '' : 'disabled';
                    $deleteTitle = $canDelete ? 'Delete Category' : 'Cannot delete - has content';

                    $actions = '<div class="btn-group" role="group">';
                    $actions .= '<a href="' . route('categories.edit', $category->id) . '" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bx bx-edit"></i></a>';
                    $actions .= '<button class="btn btn-sm ' . $deleteClass . '" data-ajax="' . route('categories.destroy', $category->id) . '" data-method="DELETE" data-confirm="Are you sure you want to delete this category?" data-loading="Deleting..." data-on-success="refreshCategoriesTable()" title="' . $deleteTitle . '" ' . $deleteDisabled . '><i class="bx bx-trash"></i></button>';
                    $actions .= '</div>';

                    return $actions;
                })
                ->rawColumns(['status_badge', 'type_badge', 'color_preview', 'icon_preview', 'action'])
                ->make(true);
        }

        $stats = [
            'total' => Category::count(),
            'active' => Category::where('is_active', true)->count(),
            'project' => Category::where('type', 'project')->count(),
            'blog' => Category::where('type', 'blog')->count(),
        ];

        return view('admin.categories.index', compact('stats'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'type' => 'required|string|in:project,blog,service',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:255',
            'order' => 'nullable|integer|min:0',
        ]);

        $validated['is_active'] = (bool) $request->input('is_active', false);

        $result = $this->categoryService->createCategory($validated);

        if ($request->ajax()) {
            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'redirect' => route('categories.index')
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 422);
        }

        if ($result['success']) {
            return redirect()->route('categories.index')
                ->with('success', $result['message']);
        }

        return back()->withInput()
            ->withErrors(['error' => $result['message']]);
    }

    public function show($id)
    {
        $category = $this->categoryRepository->findOrFail($id);
        return view('admin.categories.show', compact('category'));
    }

    public function edit($id)
    {
        $category = $this->categoryRepository->findOrFail($id);
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('categories', 'slug')->ignore($id)
            ],
            'type' => 'required|string|in:project,blog,service',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:255',
            'order' => 'nullable|integer|min:0',
        ]);

        $validated['is_active'] = (bool) $request->input('is_active', false);

        $result = $this->categoryService->updateCategory($id, $validated);

        if ($request->ajax()) {
            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'redirect' => route('categories.index')
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 422);
        }

        if ($result['success']) {
            return redirect()->route('categories.index')
                ->with('success', $result['message']);
        }

        return back()->withInput()
            ->withErrors(['error' => $result['message']]);
    }

    public function destroy(Request $request, $id)
    {
        $result = $this->categoryService->deleteCategory($id);

        if ($request->ajax()) {
            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message']
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 422);
        }

        if ($result['success']) {
            return redirect()->route('categories.index')
                ->with('success', $result['message']);
        }

        return back()->withErrors(['error' => $result['message']]);
    }
}
