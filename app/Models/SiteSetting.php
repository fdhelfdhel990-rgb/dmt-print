<?php

namespace App\Models;

use Database\Factories\SiteSettingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['key', 'value'])]
class SiteSetting extends Model
{
    /** @use HasFactory<SiteSettingFactory> */
    use HasFactory;

    public static function value(string $key, array $default = []): array
    {
        return self::query()->where('key', $key)->first()?->value ?? $default;
    }

    protected function casts(): array
    {
        return ['value' => 'array'];
    }
}
