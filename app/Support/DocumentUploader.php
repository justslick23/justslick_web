<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class DocumentUploader
{
    private const ALLOWED_EXTENSIONS = ['pdf', 'zip', 'jpg', 'png', 'webp'];

    /**
     * Stores the file under public/uploads/{folder}.
     *
     * @return array{path: string, original_name: string, size: int}
     */
    public static function store(UploadedFile $file, string $folder = 'press'): array
    {
        $extension = strtolower((string) $file->guessExtension());

        if ($extension === 'jpeg') {
            $extension = 'jpg';
        }

        abort_unless(in_array($extension, self::ALLOWED_EXTENSIONS, true), 422, 'Unsupported file type.');

        $original = (string) preg_replace('/[^\pL\pN\s._\-()]/u', '', $file->getClientOriginalName());
        $original = trim(Str::limit($original, 120, ''));

        if ($original === '') {
            $original = 'download.'.$extension;
        }

        $size = (int) $file->getSize();
        $directory = public_path('uploads/'.$folder);

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $name = Str::random(40).'.'.$extension;
        $file->move($directory, $name);

        return [
            'path' => $folder.'/'.$name,
            'original_name' => $original,
            'size' => $size,
        ];
    }

    public static function delete(?string $path): void
    {
        // Only ever delete files this class created.
        if (! $path || ! str_starts_with($path, 'press/') || str_contains($path, '..')) {
            return;
        }

        $full = public_path('uploads/'.$path);

        if (is_file($full)) {
            @unlink($full);
        }
    }
}