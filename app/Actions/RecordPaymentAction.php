<?php

namespace App\Actions;

use App\Models\Order;
use App\Models\Payment;
use App\Support\UploadDisk;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class RecordPaymentAction
{
    public function execute(Order $order, string $method, string $paymentType, int $amount, ?UploadedFile $proof = null): Payment
    {
        $storedPath = null;
        $storedDisk = UploadDisk::private();

        try {
            return DB::transaction(function () use ($order, $method, $paymentType, $amount, $proof, &$storedPath, $storedDisk): Payment {
                $order = Order::query()->lockForUpdate()->findOrFail($order->id);
                $data = [
                    'method' => $method,
                    'payment_type' => $paymentType,
                    'amount' => $amount,
                    'status' => 'pending',
                ];

                if ($proof instanceof UploadedFile) {
                    $storedPath = $proof->store('payment-proofs/'.$order->public_token, $storedDisk);
                    $data += [
                        'proof_disk' => $storedDisk,
                        'proof_path' => $storedPath,
                        'proof_original_name' => $proof->getClientOriginalName(),
                        'proof_mime_type' => $proof->getMimeType() ?: 'application/octet-stream',
                        'proof_size' => $proof->getSize(),
                    ];
                }

                $payment = $order->payments()->create($data);
                $order->update(['status' => 'payment_review', 'payment_scheme' => $paymentType]);
                $order->statusHistories()->create(['status' => 'payment_review', 'note' => 'Bukti pembayaran menunggu verifikasi.']);
                $order->activityLogs()->create(['event' => 'payment.submitted', 'properties' => ['payment_id' => $payment->id, 'amount' => $amount, 'method' => $method]]);

                return $payment;
            });
        } catch (Throwable $exception) {
            if ($storedPath !== null) {
                Storage::disk($storedDisk)->delete($storedPath);
            }
            throw $exception;
        }
    }
}
