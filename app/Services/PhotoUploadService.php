<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Throwable;

class PhotoUploadService
{
    public function upload(UploadedFile $file, int $userId): string
    {
        try {
            $image = Image::read($file->getRealPath());
            $image->scaleDown(1200);

            $path = sprintf('reports/%d/%s', $userId, $file->hashName());

            Storage::disk('public')->put($path, (string) $image->encode());

            return Storage::url($path);
        } catch (Throwable $e) {
            if ($e instanceof \RuntimeException) {
                throw $e;
            }

            throw new \RuntimeException('Failed to upload photo.', 0, $e);
        }
    }

    public function delete(string $path): void
    {
        try {
            $storagePath = $path;

            if (str_starts_with($storagePath, 'http://') || str_starts_with($storagePath, 'https://')) {
                $parsed = parse_url($storagePath);
                $storagePath = $parsed['path'] ?? '';
            }

            $storagePath = ltrim($storagePath, '/');

            if (str_starts_with($storagePath, 'storage/')) {
                $storagePath = substr($storagePath, strlen('storage/'));
            }

            if ($storagePath === '') {
                return;
            }

            Storage::disk('public')->delete($storagePath);
        } catch (Throwable $e) {
            if ($e instanceof \RuntimeException) {
                throw $e;
            }

            throw new \RuntimeException('Failed to delete photo.', 0, $e);
        }
    }
}
