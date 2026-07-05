<?php

namespace App\Jobs;

use App\Models\ActivityPhoto;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * TASK-156: Generate thumbnail image for a photo.
 * Uses Imagick if available, fallback to GD.
 */
class GeneratePhotoThumbnail implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $photoId,
    ) {}

    public function handle(): void
    {
        try {
            $photo = ActivityPhoto::find($this->photoId);
            if (!$photo || !$photo->file_path) {
                Log::warning('GeneratePhotoThumbnail: photo not found', ['photo_id' => $this->photoId]);
                return;
            }

            $sourcePath = Storage::disk('local')->path($photo->file_path);
            if (!file_exists($sourcePath)) {
                Log::warning('GeneratePhotoThumbnail: source file missing', ['path' => $sourcePath]);
                return;
            }

            $info = getimagesize($sourcePath);
            if ($info === false) return;

            [$width, $height] = $info;
            $maxDim = 300;
            $ratio = min($maxDim / $width, $maxDim / $height, 1);
            $newW = (int) round($width * $ratio);
            $newH = (int) round($height * $ratio);

            $thumbDir = 'thumbnails';
            $thumbPath = $thumbDir . '/' . basename($photo->file_path);
            $destPath = Storage::disk('local')->path($thumbPath);

            if (!is_dir(dirname($destPath))) {
                mkdir(dirname($destPath), 0755, true);
            }

            if (extension_loaded('imagick')) {
                $img = new \Imagick($sourcePath);
                $img->thumbnailImage($newW, $newH, true);
                $img->writeImage($destPath);
                $img->clear();
            } else {
                $srcImg = match ($info[2]) {
                    IMAGETYPE_JPEG => imagecreatefromjpeg($sourcePath),
                    IMAGETYPE_PNG => imagecreatefrompng($sourcePath),
                    IMAGETYPE_WEBP => imagecreatefromwebp($sourcePath),
                    default => null,
                };
                if ($srcImg === null) return;

                $thumb = imagecreatetruecolor($newW, $newH);
                imagecopyresampled($thumb, $srcImg, 0, 0, 0, 0, $newW, $newH, $width, $height);
                imagejpeg($thumb, $destPath, 85);
                imagedestroy($srcImg);
                imagedestroy($thumb);
            }

            $photo->update(['thumbnail_path' => $thumbPath]);
            Log::info('Thumbnail generated', ['photo_id' => $this->photoId]);
        } catch (\Throwable $e) {
            Log::error('GeneratePhotoThumbnail failed', ['photo_id' => $this->photoId, 'error' => $e->getMessage()]);
        }
    }
}
