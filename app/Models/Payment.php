<?php

namespace App\Models;

use Database\Factories\PaymentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['order_id', 'method', 'payment_type', 'amount', 'status', 'proof_disk', 'proof_path', 'proof_original_name', 'proof_mime_type', 'proof_size', 'verified_at', 'rejected_at', 'verified_by', 'admin_note'])]
class Payment extends Model
{
    /** @use HasFactory<PaymentFactory> */
    use HasFactory;

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function cashbookEntry(): HasOne
    {
        return $this->hasOne(CashbookEntry::class);
    }

    protected function casts(): array
    {
        return ['amount' => 'integer', 'verified_at' => 'datetime', 'rejected_at' => 'datetime'];
    }
}
