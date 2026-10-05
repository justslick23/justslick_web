<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ImageUploader
{
    /** Returns a path relative to public/uploads, e.g. "covers/abc123.jpg". */
    public static function storeCover(UploadedFile $file, int $maxSide = 1200): string
    {
        $source = @imagecreatefromstring((string) file_get_contents($file->getRealPath()));

        if ($source === false) {
            throw ValidationException::withMessages([
                'cover' => 'The image could not be processed. Try a different file.',
            ]);
        }

        $width = imagesx($source);
        $height = imagesy($source);
        $scale = min(1, $maxSide / max($width, $height));
        $newWidth = max(1, (int) round($width * $scale));
        $newHeight = max(1, (int) round($height * $scale));

        $canvas = imagecreatetruecolor($newWidth, $newHeight);
        imagefill($canvas, 0, 0, imagecolorallocate($canvas, 255, 255, 255)); // flatten transparency
        imagecopyresampled($canvas, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        $directory = public_path('uploads/covers');

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $name = Str::random(40).'.jpg';
        imagejpeg($canvas, $directory.'/'.$name, 85);

        return 'covers/'.$name;
    }

    public static function delete(?string $path): void
    {
        // Only ever delete files we created ourselves.
        if (! $path || ! str_starts_with($path, 'covers/') || str_contains($path, '..')) {
            return;
        }

        $full = public_path('uploads/'.$path);

        if (is_file($full)) {
            @unlink($full);
        }
    }
}