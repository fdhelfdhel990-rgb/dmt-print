<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Session\Store;
use Illuminate\Support\Collection;
use Throwable;

class CartService
{
    public function __construct(private ProductPriceCalculator $calculator, private Store $session) {}

    public function add(Product $product, array $options, int $quantity): string
    {
        $price = $this->calculator->calculate($product, $options, $quantity);
        $key = hash('sha256', $product->id.'|'.collect($options)->sortKeys()->toJson());
        $cart = $this->raw();
        $cart[$key] = ['product_id' => $product->id, 'options' => $options, 'quantity' => ($cart[$key]['quantity'] ?? 0) + $quantity];
        $this->calculator->calculate($product, $options, $cart[$key]['quantity']);
        $this->session->put('cart', $cart);

        return $key;
    }

    public function update(string $key, int $quantity): void
    {
        $cart = $this->raw();
        abort_unless(isset($cart[$key]), 404);
        $product = Product::findOrFail($cart[$key]['product_id']);
        $this->calculator->calculate($product, $cart[$key]['options'], $quantity);
        $cart[$key]['quantity'] = $quantity;
        $this->session->put('cart', $cart);
    }

    public function remove(string $key): void
    {
        $cart = $this->raw();
        unset($cart[$key]);
        $this->session->put('cart', $cart);
    }

    public function clear(): void
    {
        $this->session->forget('cart');
    }

    public function raw(): array
    {
        return $this->session->get('cart', []);
    }

    public function items(): Collection
    {
        return collect($this->raw())->map(function (array $item, string $key) {
            try {
                $product = Product::with('category')->where('is_active', true)->findOrFail($item['product_id']);

                return ['key' => $key, 'product' => $product, 'quantity' => $item['quantity'], 'price' => $this->calculator->calculate($product, $item['options'], $item['quantity'])];
            } catch (Throwable) {
                $this->remove($key);

                return null;
            }
        })->filter()->values();
    }

    public function subtotal(): int
    {
        return $this->items()->sum(fn (array $item) => $item['price']['subtotal']);
    }

    public function isEmpty(): bool
    {
        return $this->raw() === [];
    }
}
