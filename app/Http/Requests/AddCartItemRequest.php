<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AddCartItemRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'options' => collect($this->input('options', []))
                ->filter(fn ($value): bool => filled($value))
                ->all(),
        ]);
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return ['quantity' => ['required', 'integer', 'min:1'], 'options' => ['sometimes', 'array'], 'options.*' => ['integer', 'exists:product_option_values,id'], 'price' => ['prohibited']];
    }
}
