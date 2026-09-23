<?php

namespace App\Http\Controllers;

use App\Actions\RecordPaymentAction;
use App\Models\Order;
use App\Models\PaymentMethod;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OrderTrackingController extends Controller
{
    public function create(): View
    {
        return view('customer.track');
    }

    public function show(Request $request): View|RedirectResponse
    {
        if (! $request->filled('order_number') && ! $request->filled('phone')) {
            return view('customer.track');
        }

        $validated = $request->validate([
            'order_number' => ['required', 'string'],
            'phone' => ['required', 'string'],
        ]);

        $order = Order::query()
            ->with('customer', 'items', 'payments', 'statusHistories')
            ->where('order_number', $validated['order_number'])
            ->whereHas('customer', fn ($query) => $query->where('phone', $this->normalizePhone($validated['phone'])))
            ->first();

        if ($order === null) {
            return back()->withInput()->withErrors(['order_number' => 'Pesanan tidak ditemukan untuk kode dan nomor WhatsApp tersebut.']);
        }

        return view('customer.order-status', [
            'order' => $order,
            'paymentMethods' => PaymentMethod::query()->where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }

    public function approve(Request $request, Order $order): RedirectResponse
    {
        $request->validate(['payment_scheme' => ['required', 'in:down_payment,full']]);
        $order->update(['status' => 'waiting_payment', 'payment_scheme' => $request->string('payment_scheme'), 'customer_approved_at' => now(), 'revision_requested_at' => null]);
        $order->statusHistories()->create(['status' => 'waiting_payment', 'note' => 'Pelanggan menyetujui harga final.']);
        $order->activityLogs()->create(['event' => 'customer.approved_price', 'properties' => ['payment_scheme' => $request->string('payment_scheme')->toString()]]);

        return back()->with('status', 'Harga disetujui. Silakan lanjutkan pembayaran.');
    }

    public function revise(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate(['revision_note' => ['required', 'string', 'max:1000']]);
        $order->update(['status' => 'revision_requested', 'revision_requested_at' => now(), 'customer_note' => trim(($order->customer_note ? $order->customer_note."\n\n" : '').'Revisi: '.$validated['revision_note'])]);
        $order->statusHistories()->create(['status' => 'revision_requested', 'note' => $validated['revision_note']]);
        $order->activityLogs()->create(['event' => 'customer.requested_revision']);

        return back()->with('status', 'Permintaan revisi dikirim ke admin.');
    }

    public function payment(Request $request, Order $order, RecordPaymentAction $action): RedirectResponse
    {
        $remaining = max(0, (int) $order->final_total - (int) $order->amount_paid);
        $minimumDownPayment = (int) ceil(((int) $order->final_total) / 2);
        $validated = $request->validate([
            'method' => ['required', 'in:qris,bank_transfer,cash'],
            'payment_type' => ['required', 'in:down_payment,full,settlement'],
            'proof' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);
        $amount = match ($validated['payment_type']) {
            'down_payment' => min($remaining, $minimumDownPayment),
            default => $remaining,
        };

        if ($amount <= 0) {
            return back()->withErrors(['payment' => 'Pesanan sudah lunas.']);
        }

        $action->execute($order, $validated['method'], $validated['payment_type'], $amount, $request->file('proof'));

        return back()->with('status', 'Pembayaran dikirim dan menunggu verifikasi admin.');
    }

    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?: '';

        return str_starts_with($digits, '0') ? '62'.substr($digits, 1) : (str_starts_with($digits, '62') ? $digits : '62'.$digits);
    }
}
