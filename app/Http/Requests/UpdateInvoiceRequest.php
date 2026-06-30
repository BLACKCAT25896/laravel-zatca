<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'due_date' => 'nullable|date_format:Y-m-d H:i:s',
        ];
    }
}
