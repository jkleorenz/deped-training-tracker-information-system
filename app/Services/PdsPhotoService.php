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
            $disk->makeDirectory($dir);
        }
        $filename = $pdsId . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs($dir, $filename, 'public');
        return $path;
    }

    /**
     * Delete stored photo by path (relative to storage/app/public).
     */
    public function delete(?string $photoPath): void
    {
        if ($photoPath === null || $photoPath === '') {
            return;
        }
        Storage::disk('public')->delete($photoPath);
    }

    /**
     * Absolute filesystem path for the photo (for PDF image source).
     */
    public function absolutePath(?string $photoPath): ?string
    {
        if ($photoPath === null || $photoPath === '') {
            return null;
        }
        $path = storage_path('app/public/' . $photoPath);
        return file_exists($path) ? $path : null;
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
