<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCartItemRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class CartController extends Controller
{
    public function index(CartService $cart): View
    {
        return view('customer.cart-dynamic', ['items' => $cart->items(), 'subtotal' => $cart->subtotal()]);
    }

    public function store(AddCartItemRequest $request, Product $product, CartService $cart): RedirectResponse
    {
        abort_unless($product->is_active, 404);
        $cart->add($product, $request->validated('options', []), $request->integer('quantity'));

        return redirect()->route('cart')->with('success', 'Produk ditambahkan ke keranjang.');
    }

    public function update(UpdateCartItemRequest $request, string $key, CartService $cart): RedirectResponse
    {
        $cart->update($key, $request->integer('quantity'));

        return back()->with('success', 'Jumlah diperbarui.');
    }

    public function destroy(string $key, CartService $cart): RedirectResponse
    {
        $cart->remove($key);

        return back()->with('success', 'Item dihapus.');
    }

    public function clear(CartService $cart): RedirectResponse
    {
        $cart->clear();

        return back()->with('success', 'Keranjang dikosongkan.');
    }
}
