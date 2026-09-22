<?php

namespace App\Http\Controllers;

use App\Actions\CreateOrderAction;
use App\Http\Requests\CheckoutRequest;
use App\Models\Order;
use App\Services\CartService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class CheckoutController extends Controller
{
    public function create(CartService $cart): View|RedirectResponse
    {
        if ($cart->isEmpty()) {
            return redirect()->route('cart')->withErrors(['cart' => 'Keranjang masih kosong.']);
        }

        return view('customer.checkout-order', ['items' => $cart->items(), 'subtotal' => $cart->subtotal()]);
    }

    public function store(CheckoutRequest $request, CartService $cart, CreateOrderAction $action): RedirectResponse
    {
        if ($cart->isEmpty()) {
            return redirect()->route('cart')->withErrors(['cart' => 'Keranjang masih kosong.']);
        }
        $order = $action->execute($request->safe()->except('design_files'), $request->file('design_files', []));
        $cart->clear();

        return redirect()->route('orders.success', ['order' => $order->public_token]);
    }

    public function success(Order $order): View
    {
        return view('customer.order-success', compact('order'));
    }
}
