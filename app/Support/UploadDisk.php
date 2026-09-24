<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Throwable;

class UploadDisk
{
    public static function public(): string
    {
        return config('filesystems.uploads.public', 'public_uploads');
    }

    public static function private(): string
    {
        return config('filesystems.uploads.private', 'private_uploads');
    }

    public static function resolve(?string $disk, string $fallback): string
    {
        if (! filled($disk)) {
            return $fallback;
        }

        return match ($disk) {
            'public' => self::public(),
            'local' => self::private(),
            default => $disk,
        };
    }

    public static function isPublic(?string $disk): bool
    {
        return in_array($disk, ['public', self::public()], true);
    }

    public static function isValidPath(mixed $path): bool
    {
        if (! is_string($path)) {
            return false;
        }

        $trimmed = trim($path);

        if ($trimmed === '' || $trimmed === '0' || $trimmed === 'null' || $trimmed === 'undefined') {
            return false;
        }

        return true;
    }

    public static function normalizePath(mixed $path): ?string
    {
        if (! self::isValidPath($path)) {
            return null;
        }

        return trim((string) $path);
    }

    public static function publicUrl(mixed $path, ?string $disk = null, ?string $placeholder = null): ?string
    {
        $normalized = self::normalizePath($path);

        if ($normalized === null) {
            return $placeholder;
        }

        if (str_starts_with($normalized, 'http://') || str_starts_with($normalized, 'https://')) {
            return $normalized;
        }

        $resolvedDisk = self::resolve($disk, self::public());

        if (! self::isPublic($resolvedDisk)) {
            return $placeholder;
        }

        $publicBucket = config("filesystems.disks.{$resolvedDisk}.bucket") ?: config('filesystems.disks.public_uploads.bucket');
        $bucketPattern = filled($publicBucket) ? preg_quote((string) $publicBucket, '#').'/|' : '';
        $cleaned = preg_replace('#^('.$bucketPattern.'public/|storage/)+#i', '', ltrim($normalized, '/'));

        if (! filled($cleaned)) {
            return $placeholder;
        }

        try {
            return Storage::disk($resolvedDisk)->url($cleaned);
        } catch (Throwable) {
            return $placeholder;
        }
    }
}
