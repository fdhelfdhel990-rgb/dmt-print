<?php

namespace App\Actions;

use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdjustInventoryAction
{
    public function execute(Product $product, int $quantity, string $type, string $reference, ?User $user = null, ?string $note = null): InventoryMovement
    {
        return DB::transaction(function () use ($product, $quantity, $type, $reference, $user, $note): InventoryMovement {
            $existing = InventoryMovement::query()->where('reference', $reference)->first();

            if ($existing !== null) {
                return $existing;
            }

            $product = Product::query()->lockForUpdate()->findOrFail($product->id);
            $product->increment('stock_on_hand', $quantity);
            $product->refresh();

            return InventoryMovement::create(['product_id' => $product->id, 'type' => $type, 'quantity' => $quantity, 'balance_after' => $product->stock_on_hand, 'reference' => $reference, 'note' => $note, 'user_id' => $user?->id]);
        });
    }
}
