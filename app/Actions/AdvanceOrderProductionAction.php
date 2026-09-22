<?php

namespace App\Actions;

use App\Models\InventoryMovement;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdvanceOrderProductionAction
{
    public function start(Order $order, ?User $admin = null): Order
    {
        return DB::transaction(function () use ($order, $admin): Order {
            $order = Order::query()->with('items')->lockForUpdate()->findOrFail($order->id);

            foreach ($order->items as $item) {
                if ($item->product_id === null || $item->stock_deducted_at !== null) {
                    continue;
                }

                $reference = 'order-item:'.$item->id.':production';
                if (InventoryMovement::query()->where('reference', $reference)->exists()) {
                    $item->update(['stock_deducted_at' => now()]);

                    continue;
                }

                $product = Product::query()->lockForUpdate()->findOrFail($item->product_id);
                $quantity = -1 * $item->quantity;
                $product->increment('stock_on_hand', $quantity);
                $product->refresh();
                InventoryMovement::create(['product_id' => $product->id, 'order_item_id' => $item->id, 'type' => 'production_usage', 'quantity' => $quantity, 'balance_after' => $product->stock_on_hand, 'reference' => $reference, 'note' => 'Pemakaian stok untuk '.$order->order_number, 'user_id' => $admin?->id]);
                $item->update(['stock_deducted_at' => now()]);
            }

            $order->update(['status' => 'production', 'production_started_at' => $order->production_started_at ?? now()]);
            $order->statusHistories()->create(['status' => 'production', 'note' => 'Pesanan masuk produksi.', 'user_id' => $admin?->id]);
            $order->activityLogs()->create(['event' => 'production.started', 'user_id' => $admin?->id]);

            return $order->refresh();
        });
    }

    public function markReady(Order $order, ?User $admin = null): Order
    {
        return $this->setStatus($order, 'ready', 'ready_at', 'Pesanan siap diserahkan.', $admin);
    }

    public function complete(Order $order, ?User $admin = null): Order
    {
        return $this->setStatus($order, 'completed', 'completed_at', 'Pesanan selesai.', $admin);
    }

    private function setStatus(Order $order, string $status, string $timestampColumn, string $note, ?User $admin): Order
    {
        return DB::transaction(function () use ($order, $status, $timestampColumn, $note, $admin): Order {
            $order = Order::query()->lockForUpdate()->findOrFail($order->id);
            $order->update(['status' => $status, $timestampColumn => $order->{$timestampColumn} ?? now()]);
            $order->statusHistories()->create(['status' => $status, 'note' => $note, 'user_id' => $admin?->id]);
            $order->activityLogs()->create(['event' => 'order.'.$status, 'user_id' => $admin?->id]);

            return $order->refresh();
        });
    }
}
