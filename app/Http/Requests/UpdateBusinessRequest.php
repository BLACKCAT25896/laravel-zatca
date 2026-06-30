<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBusinessRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $businessId = $this->route('business')->id;

        return [
            'name' => 'nullable|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'tax_id' => 'nullable|string|unique:businesses,tax_id,' . $businessId,
            'crn' => 'nullable|string|unique:businesses,crn,' . $businessId,
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'address_ar' => 'nullable|string',
            'city' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'country' => 'nullable|string|size:2',
            'description' => 'nullable|string',
            'industry_category' => 'nullable|string',
            'is_vat_registered' => 'nullable|boolean',
            'vat_registration_date' => 'nullable|date',
            'bank_name' => 'nullable|string',
            'bank_account' => 'nullable|string',
            'bank_iban' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
        ];
    }
}
