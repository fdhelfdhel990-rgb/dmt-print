<?php

namespace App\Models;

use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

#[Fillable(['order_number', 'customer_id', 'public_token', 'status', 'estimated_subtotal', 'shipping_cost', 'estimated_total', 'final_total', 'final_priced_at', 'amount_paid', 'payment_scheme', 'fulfillment_method', 'shipping_address', 'shipping_region', 'postal_code', 'address_note', 'latitude', 'longitude', 'customer_note', 'internal_note', 'customer_approved_at', 'revision_requested_at', 'assigned_admin_id', 'production_started_at', 'ready_at', 'completed_at', 'cancelled_at'])]
class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function cashbookEntries(): HasMany
    {
        return $this->hasMany(CashbookEntry::class);
    }

    public function activityLogs(): MorphMany
    {
        return $this->morphMany(ActivityLog::class, 'subject');
    }

    public function assignedAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_admin_id');
    }

    public function getRouteKeyName(): string
    {
        return 'order_number';
    }

    protected function casts(): array
    {
        return ['customer_approved_at' => 'datetime', 'revision_requested_at' => 'datetime', 'final_priced_at' => 'datetime', 'production_started_at' => 'datetime', 'ready_at' => 'datetime', 'completed_at' => 'datetime', 'cancelled_at' => 'datetime', 'estimated_subtotal' => 'integer', 'estimated_total' => 'integer', 'final_total' => 'integer', 'amount_paid' => 'integer'];
    }
}
