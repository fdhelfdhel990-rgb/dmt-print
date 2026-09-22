<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class CheckoutRequest extends FormRequest
{
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
        $extensions = config('dmt.uploads.allowed', ['jpg', 'jpeg', 'png', 'pdf', 'zip']);

        return [
            'name' => ['required', 'string', 'max:255'], 'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'], 'customer_note' => ['nullable', 'string', 'max:2000'],
            'fulfillment_method' => ['required', Rule::in(['shipping', 'pickup'])],
            'shipping_address' => ['required_if:fulfillment_method,shipping', 'nullable', 'string', 'max:2000'],
            'shipping_region' => ['required_if:fulfillment_method,shipping', 'nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:20'], 'address_note' => ['nullable', 'string', 'max:500'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'], 'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'approved' => ['accepted'], 'design_files' => ['sometimes', 'array'],
            'design_files.*' => ['nullable', File::types($extensions)->max(config('dmt.uploads.max_kb', 10240)), 'extensions:'.implode(',', $extensions)],
        ];
    }
}
