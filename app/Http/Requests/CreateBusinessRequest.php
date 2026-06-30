<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateBusinessRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'tax_id' => 'required|string|unique:businesses,tax_id',
            'crn' => 'nullable|string|unique:businesses,crn',
            'email' => 'required|email',
            'phone' => 'required|string',
            'address' => 'required|string',
            'address_ar' => 'nullable|string',
            'city' => 'required|string',
            'postal_code' => 'required|string',
            'country' => 'nullable|string|size:2',
            'description' => 'nullable|string',
            'industry_category' => 'nullable|string',
            'is_vat_registered' => 'nullable|boolean',
            'vat_registration_date' => 'nullable|date',
            'bank_name' => 'nullable|string',
            'bank_account' => 'nullable|string',
            'bank_iban' => 'nullable|string',
        ];
    }
}
