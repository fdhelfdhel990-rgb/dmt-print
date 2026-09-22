<?php

namespace App\Models;

use Database\Factories\OrderItemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['order_id', 'product_id', 'product_name', 'sku', 'unit', 'quantity', 'base_price', 'options_total', 'unit_estimate', 'unit_final_price', 'subtotal', 'final_subtotal', 'stock_deducted_at'])]
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

    protected function casts(): array
    {
        return ['quantity' => 'integer', 'base_price' => 'integer', 'options_total' => 'integer', 'unit_estimate' => 'integer', 'unit_final_price' => 'integer', 'subtotal' => 'integer', 'final_subtotal' => 'integer', 'stock_deducted_at' => 'datetime'];
    }
}
