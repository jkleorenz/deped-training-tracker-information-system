<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Process and store PDS passport-sized photo: 4.5 cm × 3.5 cm.
 * Center-crops to exact aspect ratio, no distortion.
 */
class PdsPhotoService
{
    /** Required size in cm (width × height). */
    public const WIDTH_CM = 4.5;
    public const HEIGHT_CM = 3.5;

    /** Aspect ratio width/height = 9/7. */
    private const TARGET_WIDTH_PX = 540;
    private const TARGET_HEIGHT_PX = 420;

    /**
     * Check if the GD extension is loaded (required for image processing).
     */
    public static function gdAvailable(): bool
    {
        return extension_loaded('gd');
    }

    /**
     * Process uploaded image: center-crop to 4.5×3.5 aspect, resize to fixed pixels, save as JPEG.
     * Returns storage path (e.g. "pds-photos/123.jpg") or null on failure.
     */
    public function processAndStore(UploadedFile $file, int $pdsId): ?string
    {
        $disk = Storage::disk('public');
        $dir = 'pds-photos';
        if (! $disk->exists($dir)) {
            $disk->makeDirectory($dir, 0755, true, true);
        }
        $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $filename = $pdsId . '.' . $extension;
        $path = $file->storeAs($dir, $filename, 'public');
        $normalized = $this->normalizePath($path);
        if ($normalized) {
            $disk->setVisibility($normalized, 'public');
        }
        return $normalized;
    }

    /**
     * Delete stored photo by path (relative to storage/app/public).
     */
    public function delete(?string $photoPath): void
    {
        $normalized = $this->normalizePath($photoPath);
        if ($normalized === null) {
            return;
        }
        Storage::disk('public')->delete($normalized);
    }

    /**
     * Absolute filesystem path for the photo (for PDF image source).
     */
    public function absolutePath(?string $photoPath): ?string
    {
        $normalized = $this->normalizePath($photoPath);
        if ($normalized === null) {
            return null;
        }
        $disk = Storage::disk('public');
        try {
            $path = method_exists($disk, 'path')
                ? $disk->path($normalized)
                : storage_path('app/public/' . $normalized);
        } catch (\Throwable $e) {
            $path = storage_path('app/public/' . $normalized);
        }
        return is_readable($path) ? $path : null;
    }

    private function normalizePath(?string $path): ?string
    {
        if ($path === null) {
            return null;
        }
        $path = str_replace('\\', '/', $path);
        $path = ltrim($path, '/');
        return $path === '' ? null : $path;
    }

    private function loadImage(string $path, string $mime): ?\GdImage
    {
        $image = null;
        switch (strtolower($mime)) {
            case 'image/jpeg':
            case 'image/jpg':
                $image = @\imagecreatefromjpeg($path);
                break;
            case 'image/png':
                $image = @\imagecreatefrompng($path);
                if ($image) {
                    \imagealphablending($image, true);
                    \imagesavealpha($image, true);
                }
                break;
            case 'image/gif':
                $image = @\imagecreatefromgif($path);
                break;
            default:
                return null;
        }
        return $image instanceof \GdImage ? $image : null;
    }
}
