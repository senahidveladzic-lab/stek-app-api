<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<string>>
     */
    public function rules(): array
    {
        return [
            'amount' => ['sometimes', 'numeric', 'min:0.01'],
            'currency' => ['sometimes', 'string', 'size:3'],
            'category_id' => ['sometimes', 'exists:categories,id'],
            'description' => ['nullable', 'string', 'max:255'],
            'expense_date' => ['sometimes', 'date'],
        ];
    }
}
