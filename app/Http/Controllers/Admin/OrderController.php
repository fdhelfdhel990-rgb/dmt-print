<?php

namespace App\Http\Controllers\Admin;

use App\Actions\AdvanceOrderProductionAction;
use App\Actions\SetOrderFinalPriceAction;
use App\Actions\VerifyPaymentAction;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderFile;
use App\Models\Payment;
use App\Models\SiteSetting;
use App\Support\UploadDisk;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::query()->with('customer', 'items')->when($request->boolean('archived'), fn ($q) => $q->whereNotNull('archived_at'), fn ($q) => $q->whereNull('archived_at'))->when($request->string('q')->isNotEmpty(), fn ($q) => $q->where(fn ($q) => $q->where('order_number', 'like', '%'.$request->string('q').'%')->orWhereHas('customer', fn ($q) => $q->where('name', 'like', '%'.$request->string('q').'%')->orWhere('phone', 'like', '%'.$request->string('q').'%'))))->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))->when($request->filled('fulfillment_method'), fn ($q) => $q->where('fulfillment_method', $request->string('fulfillment_method')))->latest()->paginate(15)->withQueryString();

        return view('admin.orders-dynamic', compact('orders'));
    }

    public function show(Order $order): View
    {
        return view('admin.order-detail-dynamic', ['order' => $order->load('customer', 'items.options', 'items.files', 'items.product', 'payments', 'statusHistories.user')]);
    }

    public function download(OrderFile $file): StreamedResponse
    {
        $disk = UploadDisk::resolve($file->disk, UploadDisk::private());

        abort_unless(Storage::disk($disk)->exists($file->path), 404);

        return Storage::disk($disk)->download($file->path, $file->original_name);
    }

    public function downloadPaymentProof(Request $request, Payment $payment): StreamedResponse
    {
        abort_unless($payment->hasProofFile(), 404);
        abort_if(UploadDisk::isPublic($payment->proof_disk), 404);
        abort_unless($payment->proofExists(), 404);
        $disk = UploadDisk::resolve($payment->proof_disk, UploadDisk::private());

        $payment->order->activityLogs()->create([
            'event' => 'payment.proof_downloaded',
            'properties' => ['payment_id' => $payment->id],
            'user_id' => $request->user()->id,
        ]);

        return Storage::disk($disk)->download($payment->proof_path, $payment->safeProofDownloadName());
    }

    public function previewPaymentProof(Payment $payment): StreamedResponse
    {
        abort_unless($payment->hasProofFile() && $payment->isProofPreviewable(), 404);
        abort_if(UploadDisk::isPublic($payment->proof_disk), 404);
        abort_unless($payment->proofExists(), 404);
        $disk = UploadDisk::resolve($payment->proof_disk, UploadDisk::private());

        return Storage::disk($disk)->response($payment->proof_path, $payment->safeProofDownloadName(), [
            'Content-Type' => $payment->proof_mime_type ?: 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="'.$payment->safeProofDownloadName().'"',
        ]);
    }

    public function invoice(Order $order): View
    {
        $order->load('customer', 'items.options', 'items.product', 'payments');
        $verifiedTotal = $order->payments->where('status', 'verified')->sum('amount');
        $total = (int) ($order->final_total ?? $order->estimated_total);

        return view('admin.order-invoice', [
            'business' => SiteSetting::value('business', ['business_name' => 'Darul Muttaqien Printing', 'phone' => '08xx-xxxx-xxxx', 'address' => 'Jl. Contoh No. 12, Sleman, DI Yogyakarta', 'opening_hours' => 'Senin-Sabtu, 08.00-20.00', 'maps_url' => '']),
            'invoiceNumber' => 'INV-'.$order->order_number,
            'order' => $order,
            'remaining' => max(0, $total - $verifiedTotal),
            'total' => $total,
            'verifiedTotal' => $verifiedTotal,
        ]);
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

    public function archive(Request $request, Order $order): RedirectResponse
    {
        abort_unless(in_array($order->status, ['completed', 'cancelled'], true), 422, 'Hanya pesanan selesai atau batal yang dapat diarsipkan.');

        $order->update(['archived_at' => now(), 'archived_by' => $request->user()->id]);
        $order->activityLogs()->create(['event' => 'order.archived', 'user_id' => $request->user()->id]);

        return redirect()->route('admin.orders')->with('status', 'Pesanan diarsipkan.');
    }

    public function restore(Request $request, Order $order): RedirectResponse
    {
        $order->update(['archived_at' => null, 'archived_by' => null]);
        $order->activityLogs()->create(['event' => 'order.restored', 'user_id' => $request->user()->id]);

        return back()->with('status', 'Pesanan dipulihkan.');
    }
}
