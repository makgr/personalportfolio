<?php

namespace App\Services;

use App\Models\Media;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

class MediaService
{
    public function upload(UploadedFile $file, string $folder = 'uploads'): string
    {
        \Log::info('MediaService upload started', [
            'original_name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'folder' => $folder
        ]);

        // Generate unique filename
        $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
        $uploadPath = 'uploads/' . $folder;
        $fullPath = public_path($uploadPath);
        
        // Create directory if it doesn't exist
        if (!File::exists($fullPath)) {
            File::makeDirectory($fullPath, 0755, true);
            \Log::info('MediaService created directory', ['path' => $fullPath]);
        }

        \Log::info('MediaService upload paths', [
            'filename' => $filename,
            'upload_path' => $uploadPath,
            'full_path' => $fullPath
        ]);

        // Check if image before moving
        $isImage = $this->isImage($file);
        
        // Move file to public/uploads/folder
        $destinationPath = $fullPath . '/' . $filename;
        $file->move($fullPath, $filename);
        
        $relativePath = $uploadPath . '/' . $filename;

        \Log::info('MediaService file stored', [
            'relative_path' => $relativePath,
            'exists' => File::exists($destinationPath)
        ]);

        // Optimize image if it's an image file
        if ($isImage) {
            $this->optimizeImage($destinationPath);
        }

        return $relativePath;
    }

    public function uploadWithThumbnail(UploadedFile $file, string $folder = 'uploads'): array
    {
        // Check if image before moving
        $isImage = $this->isImage($file);
        
        $mainPath = $this->upload($file, $folder);
        $thumbnailPath = null;

        // Create thumbnail if it's an image
        if ($isImage) {
            $thumbnailPath = $this->createThumbnail($mainPath);
        }

        return [
            'main' => $mainPath,
            'thumbnail' => $thumbnailPath
        ];
    }

    public function attachToModel($model, UploadedFile $file, string $collection = 'default'): Media
    {
        // Store file properties before moving the file
        $originalName = $file->getClientOriginalName();
        $mimeType = $file->getMimeType();
        $fileSize = $file->getSize();
        
        $path = $this->upload($file, strtolower(class_basename($model)));

        return Media::create([
            'filename' => basename($path),
            'original_name' => $originalName,
            'mime_type' => $mimeType,
            'path' => $path,
            'disk' => 'public',
            'url' => asset($path),
            'size' => $fileSize,
            'collection' => $collection,
            'mediable_type' => get_class($model),
            'mediable_id' => $model->id,
        ]);
    }

    public function delete(string $path): bool
    {
        if (empty($path)) {
            \Log::warning('MediaService delete called with empty path');
            return false;
        }

        \Log::info('MediaService deleting file', ['path' => $path]);
        
        $fullPath = public_path($path);

        if (!File::exists($fullPath)) {
            \Log::warning('MediaService file does not exist for deletion', ['path' => $fullPath]);
            return false;
        }

        return File::delete($fullPath);
    }

    protected function isImage(UploadedFile $file): bool
    {
        return str_starts_with($file->getMimeType(), 'image/');
    }

    protected function optimizeImage(string $fullPath): void
    {
        try {
            if (file_exists($fullPath)) {
                $manager = new ImageManager(new Driver());
                $image = $manager->read($fullPath);

                // Resize if too large
                if ($image->width() > 1920) {
                    $image->scaleDown(width: 1920);
                }

                // Save with quality optimization
                $image->toJpeg(85)->save($fullPath);
                \Log::info('MediaService image optimized', ['path' => $fullPath]);
            } else {
                \Log::warning('MediaService image file not found for optimization', ['path' => $fullPath]);
            }
        } catch (\Exception $e) {
            \Log::error('MediaService image optimization failed', [
                'path' => $fullPath,
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function createThumbnail(string $originalPath): string
    {
        try {
            $fullPath = public_path($originalPath);
            $thumbnailPath = str_replace('.', '_thumb.', $originalPath);
            $thumbnailFullPath = public_path($thumbnailPath);

            if (file_exists($fullPath)) {
                $manager = new ImageManager(new Driver());
                $image = $manager->read($fullPath);
                $image->cover(300, 300);
                $image->toJpeg(80)->save($thumbnailFullPath);

                return $thumbnailPath;
            }
        } catch (\Exception $e) {
            \Log::error('MediaService thumbnail creation failed', [
                'path' => $originalPath,
                'error' => $e->getMessage()
            ]);
        }

        return $originalPath;
    }
}
