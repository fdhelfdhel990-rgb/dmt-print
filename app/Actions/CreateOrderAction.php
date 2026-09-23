<?php

namespace App\Actions;

use App\Models\Customer;
use App\Models\Order;
use App\Services\CartService;
use App\Services\OrderNumberGenerator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class CreateOrderAction
{
    public function __construct(private CartService $cart, private OrderNumberGenerator $numbers) {}

    public function execute(array $data, array $files = []): Order
    {
        $stored = [];
        try {
            return DB::transaction(function () use ($data, $files, &$stored): Order {
                $customer = Customer::create(['name' => $data['name'], 'phone' => $this->normalizePhone($data['phone']), 'email' => $data['email'] ?? null]);
                $order = Order::create([
                    'order_number' => $this->numbers->generate(), 'customer_id' => $customer->id, 'public_token' => Str::random(64),
                    'status' => 'pending_review', 'estimated_subtotal' => $this->cart->subtotal(), 'shipping_cost' => 0,
                    'estimated_total' => $this->cart->subtotal(), 'amount_paid' => 0, 'fulfillment_method' => $data['fulfillment_method'],
                    'shipping_address' => $data['shipping_address'] ?? null, 'shipping_region' => $data['shipping_region'] ?? null,
                    'postal_code' => $data['postal_code'] ?? null, 'address_note' => $data['address_note'] ?? null,
                    'latitude' => $data['latitude'] ?? null, 'longitude' => $data['longitude'] ?? null,
                    'customer_note' => $data['customer_note'] ?? null, 'customer_approved_at' => now(),
                ]);

                foreach ($this->cart->items() as $cartItem) {
                    $product = $cartItem['product'];
                    $price = $cartItem['price'];
                    $item = $order->items()->create([
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'sku' => $product->sku,
                        'product_image_disk' => filled($product->image_path) ? 'public' : null,
                        'product_image_path' => $product->image_path,
                        'unit' => $product->unit,
                        'quantity' => $cartItem['quantity'],
                        'base_price' => $price['base_price'],
                        'options_total' => $price['options_total'],
                        'unit_estimate' => $price['unit_estimate'],
                        'subtotal' => $price['subtotal'],
                    ]);
                    foreach ($price['values'] as $value) {
                        $item->options()->create(['option_name' => $value['option_name'], 'option_value' => $value['option_value'], 'price_adjustment' => $value['price_adjustment']]);
                    }
                    $file = $files[$cartItem['key']] ?? null;
                    if ($file instanceof UploadedFile) {
                        $path = $file->store('order-designs/'.$order->public_token, 'local');
                        $stored[] = $path;
                        $item->files()->create(['disk' => 'local', 'path' => $path, 'original_name' => $file->getClientOriginalName(), 'mime_type' => $file->getMimeType() ?: 'application/octet-stream', 'size' => $file->getSize()]);
                    }
                }
                $order->statusHistories()->create(['status' => 'pending_review', 'note' => 'Pesanan dibuat oleh pelanggan.']);

                return $order->load('customer', 'items.options', 'items.files', 'statusHistories');
            });
        } catch (Throwable $exception) {
            foreach ($stored as $path) {
                Storage::disk('local')->delete($path);
            }
            throw $exception;
        }
    }

    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?: '';

        return str_starts_with($digits, '0') ? '62'.substr($digits, 1) : (str_starts_with($digits, '62') ? $digits : '62'.$digits);
    }
}
