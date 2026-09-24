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

    public static function cleanPath(mixed $path, ?string $disk = null): ?string
    {
        if (! self::isValidPath($path)) {
            return null;
        }

        $cleaned = ltrim(trim((string) $path), '/');
        $resolvedDisk = self::resolve($disk, self::public());

        $knownBuckets = array_filter(array_unique([
            (string) config("filesystems.disks.{$resolvedDisk}.bucket"),
            (string) config('filesystems.disks.public_uploads.bucket'),
            (string) env('PUBLIC_AWS_BUCKET'),
            (string) env('AWS_BUCKET'),
            'dmt-print-public',
        ]), fn ($b) => trim($b) !== '');

        $patterns = ['public', 'storage'];
        foreach ($knownBuckets as $bucket) {
            $patterns[] = preg_quote(trim($bucket, '/'), '#');
        }

        $regex = '#^('.implode('|', $patterns).')/+#i';

        while (preg_match($regex, $cleaned)) {
            $cleaned = preg_replace($regex, '', $cleaned);
            $cleaned = ltrim($cleaned, '/');
        }

        return filled($cleaned) ? $cleaned : null;
    }

    public static function normalizePath(mixed $path, ?string $disk = null): ?string
    {
        return self::cleanPath($path, $disk);
    }

    public static function publicUrl(mixed $path, ?string $disk = null, ?string $placeholder = null): ?string
    {
        if (! self::isValidPath($path)) {
            return $placeholder;
        }

        $raw = trim((string) $path);

        if (str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://')) {
            $cleanedUrl = preg_replace('#^(https?://[^/]+)/(?:dmt-print-public/)+(.*)$#i', '$1/$2', $raw);

            return $cleanedUrl ?: $placeholder;
        }

        $resolvedDisk = self::resolve($disk, self::public());

        if (! self::isPublic($resolvedDisk)) {
            return $placeholder;
        }

        $cleaned = self::cleanPath($raw, $resolvedDisk);

        if (! filled($cleaned)) {
            return $placeholder;
        }

        $diskConfig = config("filesystems.disks.{$resolvedDisk}", []);
        $driver = $diskConfig['driver'] ?? 'local';
        $configuredUrl = $diskConfig['url'] ?? env('PUBLIC_AWS_URL') ?? env('AWS_URL');

        if (filled($configuredUrl)) {
            $base = rtrim((string) $configuredUrl, '/');
            $knownBuckets = array_filter(array_unique([
                (string) ($diskConfig['bucket'] ?? ''),
                (string) config('filesystems.disks.public_uploads.bucket'),
                (string) env('PUBLIC_AWS_BUCKET'),
                (string) env('AWS_BUCKET'),
                'dmt-print-public',
            ]), fn ($b) => trim($b) !== '');

            foreach ($knownBuckets as $bucket) {
                $bSuffix = '/'.trim($bucket, '/');
                if (str_ends_with($base, $bSuffix)) {
                    $base = substr($base, 0, -strlen($bSuffix));
                }
            }

            return $base.'/'.$cleaned;
        }

        if ($driver === 'local') {
            $appUrl = rtrim(config('app.url', env('APP_URL', 'http://localhost')), '/');

            return $appUrl.'/storage/'.$cleaned;
        }

        try {
            $url = Storage::disk($resolvedDisk)->url($cleaned);

            return preg_replace('#^(https?://[^/]+)/(?:dmt-print-public/)+(.*)$#i', '$1/$2', $url);
        } catch (Throwable) {
            return $placeholder;
        }
    }
}
