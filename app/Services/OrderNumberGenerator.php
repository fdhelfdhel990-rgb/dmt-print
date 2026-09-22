<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Str;

class OrderNumberGenerator
{
    public function generate(): string
    {
        do {
            $number = 'DMT-'.now()->format('ymd').'-'.Str::upper(Str::random(4));
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }
}
