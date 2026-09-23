<?php

namespace App\Models;

use App\Support\UploadDisk;
use Database\Factories\BannerFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['title', 'description', 'button_label', 'button_url', 'image_path', 'sort_order', 'is_active', 'archived_at'])]
class Banner extends Model
{
    /** @use HasFactory<BannerFactory> */
    use HasFactory;

    public function imageUrl(): ?string
    {
        return UploadDisk::publicUrl($this->image_path, UploadDisk::public());
    }

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'sort_order' => 'integer', 'archived_at' => 'datetime'];
    }
}
