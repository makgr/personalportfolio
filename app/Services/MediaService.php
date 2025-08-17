<?php

namespace App\Services;

use App\Models\Media;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MediaService
{
    protected $disk = 'public';

    public function upload(UploadedFile $file, string $folder = 'uploads'): string
    {
        // Generate unique filename
        $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
        $path = $folder . '/' . date('Y/m');

        // Store file
        $filePath = $file->storeAs($path, $filename, $this->disk);

        // Optimize if image
        if ($this->isImage($file)) {
            $this->optimizeImage($filePath);
        }

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
        return Storage::disk($this->disk)->delete($path);
    }

    protected function isImage(UploadedFile $file): bool
    {
        return str_starts_with($file->getMimeType(), 'image/');
    }

    protected function optimizeImage(string $path): void
    {
        $fullPath = Storage::disk($this->disk)->path($path);

        if (file_exists($fullPath)) {
            $image = Image::make($fullPath);

            // Resize if too large
            if ($image->width() > 1920) {
                $image->resize(1920, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }

            // Optimize quality
            $image->save($fullPath, 85);
        }
    }

    protected function createThumbnail(string $originalPath): string
    {
        $fullPath = Storage::disk($this->disk)->path($originalPath);
        $thumbnailPath = str_replace('.', '_thumb.', $originalPath);
        $thumbnailFullPath = Storage::disk($this->disk)->path($thumbnailPath);

        if (file_exists($fullPath)) {
            $image = Image::make($fullPath);
            $image->fit(300, 300);
            $image->save($thumbnailFullPath, 80);

            return $thumbnailPath;
        }

        return $originalPath;
    }
}
