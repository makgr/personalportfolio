<?php

namespace App\Services;

use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Support\Str;

class CategoryService
{
    protected CategoryRepositoryInterface $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function createCategory(array $data): array
    {
        try {
            // Generate slug if not provided
            if (empty($data['slug'])) {
                $data['slug'] = Str::slug($data['name']);
            }
            
            // Ensure unique slug
            $originalSlug = $data['slug'];
            $count = 1;
            while ($this->categoryRepository->getBySlug($data['slug'])) {
                $data['slug'] = $originalSlug . '-' . $count;
                $count++;
            }

            // Set defaults
            $data['order'] = $data['order'] ?? $this->getMaxOrder() + 1;
            $data['is_active'] = !empty($data['is_active']);
            $data['color'] = $data['color'] ?? '#6f6af8';

            $category = $this->categoryRepository->create($data);

            return [
                'success' => true,
                'message' => 'Category created successfully!',
                'data' => $category
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to create category: ' . $e->getMessage()
            ];
        }
    }

    public function updateCategory(int $id, array $data): array
    {
        try {
            $category = $this->categoryRepository->find($id);
            if (!$category) {
                return [
                    'success' => false,
                    'message' => 'Category not found'
                ];
            }

            // Generate slug if not provided or if name changed
            if (empty($data['slug']) || ($data['name'] !== $category->name && $data['slug'] === $category->slug)) {
                $data['slug'] = Str::slug($data['name']);
            }
            
            // Ensure unique slug (excluding current record)
            if ($data['slug'] !== $category->slug) {
                $originalSlug = $data['slug'];
                $count = 1;
                $existingCategory = $this->categoryRepository->getBySlug($data['slug']);
                while ($existingCategory && $existingCategory->id !== $category->id) {
                    $data['slug'] = $originalSlug . '-' . $count;
                    $count++;
                    $existingCategory = $this->categoryRepository->getBySlug($data['slug']);
                }
            }

            // Set defaults and booleans
            $data['order'] = $data['order'] ?? 0;
            $data['is_active'] = !empty($data['is_active']);
            $data['color'] = $data['color'] ?? '#6f6af8';

            $this->categoryRepository->update($data, $id);

            return [
                'success' => true,
                'message' => 'Category updated successfully!'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to update category: ' . $e->getMessage()
            ];
        }
    }

    public function deleteCategory(int $id): array
    {
        try {
            $category = $this->categoryRepository->find($id);
            if (!$category) {
                return [
                    'success' => false,
                    'message' => 'Category not found'
                ];
            }

            if (!$this->categoryRepository->canDelete($id)) {
                return [
                    'success' => false,
                    'message' => 'Cannot delete this category. It has associated content (projects or blog posts). Please remove all content first.'
                ];
            }

            $this->categoryRepository->delete($id);

            return [
                'success' => true,
                'message' => 'Category deleted successfully!'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to delete category: ' . $e->getMessage()
            ];
        }
    }

    private function getMaxOrder(): int
    {
        return $this->categoryRepository->getOrderedCategories()->max('order') ?? 0;
    }
}