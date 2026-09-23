<?php

namespace App\Models;

use App\Support\UploadDisk;
use Database\Factories\PaymentMethodFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['type', 'name', 'is_active', 'bank_name', 'account_number', 'account_name', 'image_path', 'instructions', 'sort_order'])]
class PaymentMethod extends Model
{
    /** @use HasFactory<PaymentMethodFactory> */
    use HasFactory;

    public function imageUrl(): ?string
    {
        return UploadDisk::publicUrl($this->image_path, UploadDisk::public());
    }

    public function isAvailableForCustomer(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        return match ($this->type) {
            'qris' => UploadDisk::isValidPath($this->image_path),
            'bank_transfer' => filled($this->bank_name) && filled($this->account_number) && filled($this->account_name),
            'cash' => true,
            default => false,
        };
    }

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'sort_order' => 'integer'];
    }
}
