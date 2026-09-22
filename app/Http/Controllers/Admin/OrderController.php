<?php

namespace App\Http\Controllers\Admin;

use App\Actions\AdvanceOrderProductionAction;
use App\Actions\SetOrderFinalPriceAction;
use App\Actions\VerifyPaymentAction;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderFile;
use App\Models\Payment;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::query()->with('customer', 'items')->when($request->string('q')->isNotEmpty(), fn ($q) => $q->where(fn ($q) => $q->where('order_number', 'like', '%'.$request->string('q').'%')->orWhereHas('customer', fn ($q) => $q->where('name', 'like', '%'.$request->string('q').'%')->orWhere('phone', 'like', '%'.$request->string('q').'%'))))->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))->when($request->filled('fulfillment_method'), fn ($q) => $q->where('fulfillment_method', $request->string('fulfillment_method')))->latest()->paginate(15)->withQueryString();

        return view('admin.orders-dynamic', compact('orders'));
    }

    public function show(Order $order): View
    {
        return view('admin.order-detail-dynamic', ['order' => $order->load('customer', 'items.options', 'items.files', 'payments', 'statusHistories.user')]);
    }

    public function download(OrderFile $file): StreamedResponse
    {
        return Storage::disk($file->disk)->download($file->path, $file->original_name);
    }

    public function price(Request $request, Order $order, SetOrderFinalPriceAction $action): RedirectResponse
    {
        $validated = $request->validate([
            'final_total' => ['required', 'integer', 'min:1'],
            'shipping_cost' => ['required', 'integer', 'min:0'],
            'internal_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $action->execute($order, (int) $validated['final_total'], (int) $validated['shipping_cost'], $request->user(), $validated['internal_note'] ?? null);

        return back()->with('status', 'Harga final disimpan.');
    }

    public function verifyPayment(Request $request, Payment $payment, VerifyPaymentAction $action): RedirectResponse
    {
        $validated = $request->validate(['admin_note' => ['nullable', 'string', 'max:1000']]);
        $action->verify($payment, $request->user(), $validated['admin_note'] ?? null);

        return back()->with('status', 'Pembayaran diverifikasi.');
    }

    public function rejectPayment(Request $request, Payment $payment, VerifyPaymentAction $action): RedirectResponse
    {
        $validated = $request->validate(['admin_note' => ['nullable', 'string', 'max:1000']]);
        $action->reject($payment, $request->user(), $validated['admin_note'] ?? null);

        return back()->with('status', 'Pembayaran ditolak.');
    }

    public function cancelPaymentVerification(Request $request, Payment $payment, VerifyPaymentAction $action): RedirectResponse
    {
        $validated = $request->validate(['admin_note' => ['nullable', 'string', 'max:1000']]);
        $action->cancelVerification($payment, $request->user(), $validated['admin_note'] ?? null);

        return back()->with('status', 'Verifikasi pembayaran dibatalkan.');
    }

    public function production(Request $request, Order $order, AdvanceOrderProductionAction $action): RedirectResponse
    {
        $validated = $request->validate(['step' => ['required', 'in:start,ready,complete']]);

        match ($validated['step']) {
            'start' => $action->start($order, $request->user()),
            'ready' => $action->markReady($order, $request->user()),
            'complete' => $action->complete($order, $request->user()),
        };

        return back()->with('status', 'Status produksi diperbarui.');
    }
}
