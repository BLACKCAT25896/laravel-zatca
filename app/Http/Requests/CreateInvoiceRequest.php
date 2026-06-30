<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'business_id' => 'required|exists:businesses,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'type' => 'nullable|in:tax_invoice,simplified_invoice,debit_note,credit_note',
            'invoice_date' => 'nullable|date_format:Y-m-d H:i:s',
            'due_date' => 'nullable|date_format:Y-m-d H:i:s|after:invoice_date',
            'discount_amount' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|size:3',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.description_ar' => 'nullable|string',
            'items.*.sku' => 'nullable|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.unit' => 'nullable|string',
            'items.*.discount_amount' => 'nullable|numeric|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0|max:100',
        ];
    }
}
