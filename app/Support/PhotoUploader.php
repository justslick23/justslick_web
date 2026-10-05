<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Throwable;

class PhotoUploader
{
    public static function store(UploadedFile $file): array
    {
        if (! extension_loaded('gd')) {
            throw new RuntimeException('Enable the PHP GD extension.');
        }

        $source = @imagecreatefromstring(
            (string) file_get_contents($file->getRealPath())
        );

        if ($source === false) {
            throw ValidationException::withMessages([
                'image' => 'This photo could not be processed.',
            ]);
        }

        // Apply JPEG orientation when EXIF support is available.
        if ($file->getMimeType() === 'image/jpeg'
            && function_exists('exif_read_data')) {
            $exif = @exif_read_data($file->getRealPath());
            $orientation = (int) ($exif['Orientation'] ?? 1);

            if (in_array($orientation, [2, 5, 7], true)) {
                imageflip($source, IMG_FLIP_HORIZONTAL);
            } elseif ($orientation === 4) {
                imageflip($source, IMG_FLIP_VERTICAL);
            }

            $angle = match ($orientation) {
                3 => 180,
                5, 8 => 90,
                6, 7 => -90,
                default => 0,
            };

            if ($angle !== 0) {
                $rotated = imagerotate($source, $angle, 0);

                if ($rotated === false) {
                    throw ValidationException::withMessages([
                        'image' => 'The photo orientation could not be corrected.',
                    ]);
                }

                $source = $rotated;
            }
        }

        $width = imagesx($source);
        $height = imagesy($source);
        $scale = min(1, 2400 / max($width, $height));

        $newWidth = max(1, (int) round($width * $scale));
        $newHeight = max(1, (int) round($height * $scale));

        $canvas = imagecreatetruecolor($newWidth, $newHeight);

        if ($canvas === false) {
            throw new RuntimeException('Could not prepare the photo.');
        }

        imagefill($canvas, 0, 0, imagecolorallocate($canvas, 255, 255, 255));

        if (! imagecopyresampled(
            $canvas, $source,
            0, 0, 0, 0,
            $newWidth, $newHeight,
            $width, $height
        )) {
            throw new RuntimeException('Could not resize the photo.');
        }

        $directory = public_path('uploads/photos');

        if (! is_dir($directory)
            && ! mkdir($directory, 0755, true)
            && ! is_dir($directory)) {
            throw new RuntimeException('Could not create the photo directory.');
        }

        $path = 'photos/'.Str::random(40).'.jpg';
        $fullPath = public_path('uploads/'.$path);

        try {
            $saved = imagejpeg($canvas, $fullPath, 90);
            clearstatcache(true, $fullPath);

            if (! $saved || ! is_file($fullPath) || filesize($fullPath) === 0) {
                throw new RuntimeException('Could not save the photo.');
            }
        } catch (Throwable $exception) {
            self::delete($path);
            throw $exception;
        }

        return [
            'image_path' => $path,
            'width' => $newWidth,
            'height' => $newHeight,
        ];
    }

    public static function delete(?string $path): void
    {
        if (! $path
            || ! preg_match('#\Aphotos/[A-Za-z0-9]{40}\.jpg\z#', $path)) {
            return;
        }

        $fullPath = public_path('uploads/'.$path);

        if (is_file($fullPath) && ! @unlink($fullPath)) {
            report(new RuntimeException('Could not delete photo: '.$path));
        }
    }
}