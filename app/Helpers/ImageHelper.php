<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * ImageHelper - Handle image compression and storage
 * 
 * This helper uses PHP GD library to compress images below 100KB
 * while maintaining acceptable quality. It supports:
 * - JPEG, PNG, GIF, WebP formats
 * - Automatic resizing (max width 800px by default)
 * - Iterative quality reduction until target size is reached
 * - Transparency preservation for PNG/GIF
 * 
 * Usage:
 * $path = ImageHelper::compressAndSave($file, 'karyawan/foto', 100, 800, 85);
 */
class ImageHelper
{
    /**
     * Compress and save image to storage
     * 
     * @param UploadedFile $file
     * @param string $path Storage path (e.g., 'karyawan/foto')
     * @param int $maxSizeKB Maximum file size in KB (default: 100)
     * @param int $maxWidth Maximum width in pixels (default: 800)
     * @param int $quality Initial quality (default: 85)
     * @return string Stored file path
     */
    public static function compressAndSave(
        UploadedFile $file,
        string $path,
        int $maxSizeKB = 100,
        int $maxWidth = 800,
        int $quality = 85
    ): string {
        // Generate unique filename
        $extension = strtolower($file->getClientOriginalExtension());
        $filename = time() . '_' . uniqid() . '.' . $extension;
        $fullPath = $path . '/' . $filename;
        
        // Get image info
        $imageInfo = getimagesize($file->getRealPath());
        $originalWidth = $imageInfo[0];
        $originalHeight = $imageInfo[1];
        $mimeType = $imageInfo['mime'];
        
        // Create image resource based on mime type
        switch ($mimeType) {
            case 'image/jpeg':
            case 'image/jpg':
                $sourceImage = imagecreatefromjpeg($file->getRealPath());
                break;
            case 'image/png':
                $sourceImage = imagecreatefrompng($file->getRealPath());
                break;
            case 'image/gif':
                $sourceImage = imagecreatefromgif($file->getRealPath());
                break;
            case 'image/webp':
                $sourceImage = imagecreatefromwebp($file->getRealPath());
                break;
            default:
                throw new \Exception('Unsupported image type: ' . $mimeType);
        }
        
        // Calculate new dimensions (maintain aspect ratio)
        if ($originalWidth > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = (int) ($originalHeight * ($maxWidth / $originalWidth));
        } else {
            $newWidth = $originalWidth;
            $newHeight = $originalHeight;
        }
        
        // Create new image with new dimensions
        $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
        
        // Preserve transparency for PNG and GIF
        if ($mimeType === 'image/png' || $mimeType === 'image/gif') {
            imagealphablending($resizedImage, false);
            imagesavealpha($resizedImage, true);
            $transparent = imagecolorallocatealpha($resizedImage, 255, 255, 255, 127);
            imagefilledrectangle($resizedImage, 0, 0, $newWidth, $newHeight, $transparent);
        }
        
        // Resize image
        imagecopyresampled(
            $resizedImage,
            $sourceImage,
            0, 0, 0, 0,
            $newWidth,
            $newHeight,
            $originalWidth,
            $originalHeight
        );
        
        // Compress image iteratively until size is below maxSizeKB
        $tempPath = sys_get_temp_dir() . '/' . $filename;
        $currentQuality = $quality;
        
        do {
            // Save with current quality
            switch ($mimeType) {
                case 'image/jpeg':
                case 'image/jpg':
                    imagejpeg($resizedImage, $tempPath, $currentQuality);
                    break;
                case 'image/png':
                    // PNG quality is 0-9 (0 = no compression, 9 = max compression)
                    $pngQuality = (int) (9 - ($currentQuality / 100 * 9));
                    imagepng($resizedImage, $tempPath, $pngQuality);
                    break;
                case 'image/gif':
                    imagegif($resizedImage, $tempPath);
                    break;
                case 'image/webp':
                    imagewebp($resizedImage, $tempPath, $currentQuality);
                    break;
            }
            
            // Check file size
            $fileSize = filesize($tempPath);
            $fileSizeKB = $fileSize / 1024;
            
            // If size is acceptable, break
            if ($fileSizeKB <= $maxSizeKB) {
                break;
            }
            
            // Reduce quality for next iteration
            $currentQuality -= 5;
            
            // Prevent infinite loop - stop at quality 20
            if ($currentQuality < 20) {
                break;
            }
        } while ($fileSizeKB > $maxSizeKB);
        
        // Store to storage disk
        $fileContents = file_get_contents($tempPath);
        Storage::disk('public')->put($fullPath, $fileContents);
        
        // Clean up
        imagedestroy($sourceImage);
        imagedestroy($resizedImage);
        @unlink($tempPath);
        
        return $fullPath;
    }
    
    /**
     * Delete image from storage
     * 
     * @param string|null $path
     * @return bool
     */
    public static function delete(?string $path): bool
    {
        if (!$path) {
            return false;
        }
        
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }
        
        return false;
    }
    
    /**
     * Get image URL
     * 
     * @param string|null $path
     * @return string|null
     */
    public static function url(?string $path): ?string
    {
        if (!$path) {
            return null;
        }
        
        return asset('storage/' . $path);
    }
}
