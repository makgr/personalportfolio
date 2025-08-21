<?php

namespace App\Services;

use App\Models\Media;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MediaService
{
    protected $disk = 'public';

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
        $path = $folder . '/' . date('Y/m');

        \Log::info('MediaService upload paths', [
            'filename' => $filename,
            'path' => $path,
            'disk' => $this->disk
        ]);

        // Store file
        $filePath = $file->storeAs($path, $filename, $this->disk);

        \Log::info('MediaService file stored', [
            'file_path' => $filePath,
            'exists' => \Storage::disk($this->disk)->exists($filePath)
        ]);

        // Temporarily disable image optimization for debugging
        // if ($this->isImage($file)) {
        //     $this->optimizeImage($filePath);
        // }

        return $filePath;
    }

    public function uploadWithThumbnail(UploadedFile $file, string $folder = 'uploads'): array
    {
        $mainPath = $this->upload($file, $folder);
        $thumbnailPath = null;

        if ($this->isImage($file)) {
            $thumbnailPath = $this->createThumbnail($mainPath);
        }

        return [
            'main' => $mainPath,
            'thumbnail' => $thumbnailPath
        ];
    }

    public function attachToModel($model, UploadedFile $file, string $collection = 'default'): Media
    {
        $path = $this->upload($file, strtolower(class_basename($model)));

        return Media::create([
            'filename' => basename($path),
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'path' => $path,
            'disk' => $this->disk,
            'url' => Storage::disk($this->disk)->url($path),
            'size' => $file->getSize(),
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

        if (!Storage::disk($this->disk)->exists($path)) {
            \Log::warning('MediaService file does not exist for deletion', ['path' => $path]);
            return false;
        }

        return Storage::disk($this->disk)->delete($path);
    }

    protected function isImage(UploadedFile $file): bool
    {
        return str_starts_with($file->getMimeType(), 'image/');
    }

    protected function optimizeImage(string $path): void
    {
        try {
            $fullPath = Storage::disk($this->disk)->path($path);

            if (file_exists($fullPath)) {
                $manager = new ImageManager(new Driver());
                $image = $manager->read($fullPath);

                // Resize if too large
                if ($image->width() > 1920) {
                    $image->scaleDown(width: 1920);
                }

                // Save with quality optimization
                $image->toJpeg(85)->save($fullPath);
                \Log::info('MediaService image optimized', ['path' => $path]);
            } else {
                \Log::warning('MediaService image file not found for optimization', ['path' => $fullPath]);
            }
        } catch (\Exception $e) {
            \Log::error('MediaService image optimization failed', [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function createThumbnail(string $originalPath): string
    {
        try {
            $fullPath = Storage::disk($this->disk)->path($originalPath);
            $thumbnailPath = str_replace('.', '_thumb.', $originalPath);
            $thumbnailFullPath = Storage::disk($this->disk)->path($thumbnailPath);

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
