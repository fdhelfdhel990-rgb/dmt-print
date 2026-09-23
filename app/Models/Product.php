<?php

namespace App\Models;

use App\Support\UploadDisk;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

#[Fillable(['category_id', 'name', 'slug', 'sku', 'short_description', 'description', 'image_path', 'base_price', 'unit', 'minimum_order', 'stock_on_hand', 'stock_minimum', 'production_estimate', 'tone', 'tag', 'is_active', 'is_featured', 'sort_order'])]
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(ProductOption::class)->orderBy('sort_order');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function imageUrl(): ?string
    {
        $disk = UploadDisk::public();

        if (! filled($this->image_path) || ! Storage::disk($disk)->exists($this->image_path)) {
            return asset('images/placeholders/product.svg');
        }

        return Storage::disk($disk)->url($this->image_path);
    }

    protected function casts(): array
    {
        return [
            'base_price' => 'integer',
            'minimum_order' => 'integer',
            'stock_on_hand' => 'integer',
            'stock_minimum' => 'integer',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
