<?php

namespace App\Support;

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
}
