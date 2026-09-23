<?php

namespace App\Models;

use Database\Factories\CashbookEntryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['entry_date', 'direction', 'category', 'source', 'payment_method', 'amount', 'reference', 'order_id', 'payment_id', 'note', 'user_id', 'reversed_at', 'archived_at'])]
class CashbookEntry extends Model
{
    /** @use HasFactory<CashbookEntryFactory> */
    use HasFactory;

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    protected function casts(): array
    {
        return ['entry_date' => 'date', 'amount' => 'integer', 'reversed_at' => 'datetime', 'archived_at' => 'datetime'];
    }
}
