<?php

namespace App\Models;

use App\Support\UploadDisk;
use Database\Factories\PaymentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

    public function hasProofFile(): bool
    {
        return filled($this->proof_disk) && filled($this->proof_path);
    }

    public function proofExists(): bool
    {
        return $this->hasProofFile() && Storage::disk(UploadDisk::resolve($this->proof_disk, UploadDisk::private()))->exists($this->proof_path);
    }

    public function safeProofDownloadName(): string
    {
        $name = $this->proof_original_name ?: 'bukti-pembayaran-'.$this->id;
        $extension = pathinfo($name, PATHINFO_EXTENSION);
        $base = pathinfo($name, PATHINFO_FILENAME);
        $safe = Str::slug($base) ?: 'bukti-pembayaran-'.$this->id;

        return $extension ? $safe.'.'.strtolower($extension) : $safe;
    }

    public function isProofPreviewable(): bool
    {
        return in_array($this->proof_mime_type, ['image/jpeg', 'image/png', 'application/pdf'], true);
    }

    protected function casts(): array
    {
        return ['amount' => 'integer', 'verified_at' => 'datetime', 'rejected_at' => 'datetime'];
    }
}
