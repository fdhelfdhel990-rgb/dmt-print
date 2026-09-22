<?php

namespace App\Actions;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SetOrderFinalPriceAction
{
    public function execute(Order $order, int $finalTotal, int $shippingCost, ?User $admin = null, ?string $note = null): Order
    {
        return DB::transaction(function () use ($order, $finalTotal, $shippingCost, $admin, $note): Order {
            $order = Order::query()->with('items')->lockForUpdate()->findOrFail($order->id);
            $itemSubtotal = max(0, $finalTotal - $shippingCost);
            $estimatedSubtotal = max(1, $order->estimated_subtotal);

            foreach ($order->items as $item) {
                $finalSubtotal = (int) round($itemSubtotal * ($item->subtotal / $estimatedSubtotal));
                $item->update([
                    'unit_final_price' => (int) ceil($finalSubtotal / max(1, $item->quantity)),
                    'final_subtotal' => $finalSubtotal,
                ]);
            }

            $order->update([
                'shipping_cost' => $shippingCost,
                'estimated_total' => $order->estimated_subtotal + $shippingCost,
                'final_total' => $finalTotal,
                'final_priced_at' => now(),
                'payment_scheme' => null,
                'status' => 'waiting_customer_approval',
                'assigned_admin_id' => $admin?->id,
                'internal_note' => $note,
            ]);

            $order->statusHistories()->create(['status' => 'waiting_customer_approval', 'note' => 'Harga final ditetapkan admin.', 'user_id' => $admin?->id]);
            $order->activityLogs()->create(['event' => 'price.finalized', 'properties' => ['final_total' => $finalTotal, 'shipping_cost' => $shippingCost], 'user_id' => $admin?->id]);

            return $order->refresh();
        });
    }
}
