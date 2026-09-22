<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class ProductPriceCalculator
{
    /** @param array<int|string, int|string> $selectedValues */
    public function calculate(Product $product, array $selectedValues, int $quantity): array
    {
        if (! $product->is_active || $quantity < $product->minimum_order) {
            throw ValidationException::withMessages(['quantity' => 'Jumlah minimal adalah '.$product->minimum_order.' '.$product->unit.'.']);
        }

        $options = $product->options()->with(['values' => fn ($query) => $query->where('is_active', true)])->where('is_active', true)->get();
        $selected = collect($selectedValues)->mapWithKeys(fn ($value, $optionId) => [(int) $optionId => (int) $value]);

        foreach ($options->where('is_required', true) as $option) {
            if (! $selected->has($option->id)) {
                throw ValidationException::withMessages(['options.'.$option->id => 'Pilihan '.$option->name.' wajib diisi.']);
            }
        }

        $values = $this->resolveValues($options, $selected);
        $optionsTotal = $values->sum('price_adjustment');
        $unitEstimate = $product->base_price + $optionsTotal;

        return ['base_price' => $product->base_price, 'options_total' => $optionsTotal, 'unit_estimate' => $unitEstimate, 'subtotal' => $unitEstimate * $quantity, 'values' => $values];
    }

    private function resolveValues(Collection $options, Collection $selected): Collection
    {
        return $selected->map(function (int $valueId, int $optionId) use ($options) {
            $option = $options->firstWhere('id', $optionId);
            $value = $option?->values->firstWhere('id', $valueId);
            if (! $option || ! $value) {
                throw ValidationException::withMessages(['options' => 'Pilihan produk tidak valid.']);
            }

            return ['option_id' => $option->id, 'option_name' => $option->name, 'value_id' => $value->id, 'option_value' => $value->name, 'price_adjustment' => $value->price_adjustment];
        })->values();
    }
}
