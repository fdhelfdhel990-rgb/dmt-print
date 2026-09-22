<?php

namespace App\Actions;

use App\Models\CashbookEntry;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class VerifyPaymentAction
{
    public function verify(Payment $payment, ?User $admin = null, ?string $note = null): Payment
    {
        return DB::transaction(function () use ($payment, $admin, $note): Payment {
            $payment = Payment::query()->lockForUpdate()->findOrFail($payment->id);
            $order = Order::query()->lockForUpdate()->findOrFail($payment->order_id);

            if ($payment->status !== 'verified') {
                $payment->update(['status' => 'verified', 'verified_at' => now(), 'rejected_at' => null, 'verified_by' => $admin?->id, 'admin_note' => $note]);
                $order->increment('amount_paid', $payment->amount);
                $order->refresh();
                $order->update(['status' => $order->final_total !== null && $order->amount_paid >= $order->final_total ? 'paid' : 'waiting_payment']);

                CashbookEntry::firstOrCreate(
                    ['reference' => 'payment:'.$payment->id],
                    ['direction' => 'in', 'category' => 'payment', 'amount' => $payment->amount, 'order_id' => $order->id, 'payment_id' => $payment->id, 'note' => 'Pembayaran '.$order->order_number, 'user_id' => $admin?->id]
                );

                $order->statusHistories()->create(['status' => $order->status, 'note' => 'Pembayaran diverifikasi admin.', 'user_id' => $admin?->id]);
                $order->activityLogs()->create(['event' => 'payment.verified', 'properties' => ['payment_id' => $payment->id, 'amount' => $payment->amount], 'user_id' => $admin?->id]);
            }

            return $payment->refresh();
        });
    }

    public function reject(Payment $payment, ?User $admin = null, ?string $note = null): Payment
    {
        return DB::transaction(function () use ($payment, $admin, $note): Payment {
            $payment = Payment::query()->lockForUpdate()->findOrFail($payment->id);

            if ($payment->status === 'verified') {
                $this->cancelVerification($payment, $admin, $note);
            }

            $payment->update(['status' => 'rejected', 'rejected_at' => now(), 'admin_note' => $note, 'verified_at' => null, 'verified_by' => null]);
            $payment->order->statusHistories()->create(['status' => 'waiting_payment', 'note' => 'Pembayaran ditolak admin.', 'user_id' => $admin?->id]);
            $payment->order->activityLogs()->create(['event' => 'payment.rejected', 'properties' => ['payment_id' => $payment->id], 'user_id' => $admin?->id]);

            return $payment->refresh();
        });
    }

    public function cancelVerification(Payment $payment, ?User $admin = null, ?string $note = null): Payment
    {
        return DB::transaction(function () use ($payment, $admin, $note): Payment {
            $payment = Payment::query()->lockForUpdate()->findOrFail($payment->id);

            if ($payment->status === 'verified') {
                $order = Order::query()->lockForUpdate()->findOrFail($payment->order_id);
                $order->update(['amount_paid' => max(0, $order->amount_paid - $payment->amount), 'status' => 'waiting_payment']);
                $payment->update(['status' => 'pending', 'verified_at' => null, 'verified_by' => null, 'admin_note' => $note]);
                CashbookEntry::query()->where('reference', 'payment:'.$payment->id)->whereNull('reversed_at')->update(['reversed_at' => now(), 'note' => trim(($note ?? '').' Verifikasi pembayaran dibatalkan.')]);
                $order->statusHistories()->create(['status' => 'waiting_payment', 'note' => 'Verifikasi pembayaran dibatalkan.', 'user_id' => $admin?->id]);
                $order->activityLogs()->create(['event' => 'payment.verification_cancelled', 'properties' => ['payment_id' => $payment->id], 'user_id' => $admin?->id]);
            }

            return $payment->refresh();
        });
    }
}
