<?php

namespace App\Models;

use App\Support\UploadDisk;
use Database\Factories\OrderItemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

#[Fillable(['order_id', 'product_id', 'product_name', 'sku', 'product_image_disk', 'product_image_path', 'unit', 'quantity', 'base_price', 'options_total', 'unit_estimate', 'unit_final_price', 'subtotal', 'final_subtotal', 'stock_deducted_at'])]
class OrderItem extends Model
{
    /** @use HasFactory<OrderItemFactory> */
    use HasFactory;

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(OrderItemOption::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(OrderFile::class);
    }

    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function imageDisk(): string
    {
        return UploadDisk::resolve($this->product_image_disk, UploadDisk::public());
    }

    public function imagePath(): ?string
    {
        return $this->product_image_path ?: $this->product?->image_path;
    }

    public function imageUrl(): string
    {
        $disk = $this->imageDisk();
        $path = $this->imagePath();

        if (! filled($path) || ! Storage::disk($disk)->exists($path)) {
            return asset('images/placeholders/product.svg');
        }

        return Storage::disk($disk)->url($path);
    }

    protected function casts(): array
    {
        return ['quantity' => 'integer', 'base_price' => 'integer', 'options_total' => 'integer', 'unit_estimate' => 'integer', 'unit_final_price' => 'integer', 'subtotal' => 'integer', 'final_subtotal' => 'integer', 'stock_deducted_at' => 'datetime'];
    }
}
