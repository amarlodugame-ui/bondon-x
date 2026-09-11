<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->status === 0 && ! $this->user()->is_deleted;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'address_id' => ['required', 'integer', 'min:1'],
            'shipping_method_id' => ['required', 'integer', 'min:1'],
            'coupon' => ['nullable', 'string', 'max:100'],
            'customer_note' => ['nullable', 'string', 'max:500'],
            'checkout_token' => ['required', 'uuid'],
            'quote_hash' => ['required', 'string', 'size:64'],
        ];
    }
}
