<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateSupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'business_id' => 'required|exists:businesses,id',
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'tax_id' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'country' => 'nullable|string|size:2',
            'type' => 'nullable|in:customer,vendor,both',
            'credit_limit' => 'nullable|numeric|min:0',
            'payment_terms' => 'nullable|string',
        ];
    }
}
