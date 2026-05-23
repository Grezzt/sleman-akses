<?php

namespace App\Services;

use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Configuration\Configuration;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use Throwable;

class PhotoUploadService
{
    private bool $configured = false;

    public function upload(UploadedFile $file, int $userId): string
    {
        try {
            $this->configure();
            $image = Image::read($file->getRealPath());
            $image->scaleDown(1200);

            $tempPath = $this->writeTempImage($image);
            $upload = null;

            try {
                $publicId = $this->buildPublicId($userId);
                $upload = (new UploadApi())->upload($tempPath, [
                    'public_id' => $publicId,
                    'resource_type' => 'image',
                    'overwrite' => true,
                    'unique_filename' => false,
                ]);
            } finally {
                if (is_string($tempPath) && $tempPath !== '') {
                    @unlink($tempPath);
                }
            }

            $url = $upload['secure_url'] ?? $upload['url'] ?? null;

            if (!$url) {
                throw new \RuntimeException('Failed to upload photo.');
            }

            return $url;
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
            $this->configure();
            $publicId = $this->extractPublicId($path);

            if ($publicId === '') {
                return;
            }

            (new UploadApi())->destroy($publicId, [
                'resource_type' => 'image',
            ]);
        } catch (Throwable $e) {
            if ($e instanceof \RuntimeException) {
                throw $e;
            }

            throw new \RuntimeException('Failed to delete photo.', 0, $e);
        }
    }

    private function configure(): void
    {
        if ($this->configured) {
            return;
        }

        $config = config('services.cloudinary');

        if (
            !$config
            || empty($config['cloud_name'])
            || empty($config['api_key'])
            || empty($config['api_secret'])
        ) {
            throw new \RuntimeException('Cloudinary is not configured.');
        }

        Configuration::instance([
            'cloud' => [
                'cloud_name' => $config['cloud_name'],
                'api_key' => $config['api_key'],
                'api_secret' => $config['api_secret'],
            ],
            'url' => ['secure' => true],
        ]);

        $this->configured = true;
    }

    private function buildPublicId(int $userId): string
    {
        $folder = $this->cloudinaryFolder();

        return sprintf('%s/%d/%s', $folder, $userId, Str::uuid());
    }

    private function cloudinaryFolder(): string
    {
        $folder = trim((string) config('services.cloudinary.folder', 'reports'));

        if ($folder === '') {
            return 'reports';
        }

        return trim($folder, '/');
    }

    private function writeTempImage($image): string
    {
        $binary = (string) $image->encode();
        $tempPath = tempnam(sys_get_temp_dir(), 'report_');

        if ($tempPath === false) {
            throw new \RuntimeException('Failed to create temp file.');
        }

        file_put_contents($tempPath, $binary);

        return $tempPath;
    }

    private function extractPublicId(string $path): string
    {
        $path = trim($path);

        if ($path === '') {
            return '';
        }

        $publicPart = $path;

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            $parsedPath = parse_url($path, PHP_URL_PATH);

            if (!is_string($parsedPath) || $parsedPath === '') {
                return '';
            }

            $publicPart = $this->extractUploadPath($parsedPath);
        }

        $segments = array_values(array_filter(explode('/', ltrim($publicPart, '/')), 'strlen'));

        if ($segments === []) {
            return '';
        }

        $versionIndex = null;

        foreach ($segments as $index => $segment) {
            if (preg_match('/^v\d+$/', $segment)) {
                $versionIndex = $index;
                break;
            }
        }

        if ($versionIndex !== null) {
            $segments = array_slice($segments, $versionIndex + 1);
        }

        if ($segments === []) {
            return '';
        }

        $publicId = implode('/', $segments);

        return (string) preg_replace('/\.[^\.\/]+$/', '', $publicId);
    }

    private function extractUploadPath(string $path): string
    {
        $parts = explode('/upload/', $path, 2);

        if (count($parts) === 2) {
            return $parts[1];
        }

        return ltrim($path, '/');
    }
}
