<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveBudgetRequest extends FormRequest
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
            'month' => ['required', 'date'],
            'overall_amount' => ['nullable', 'numeric', 'min:0'],
            'categories' => ['nullable', 'array'],
            'categories.*.category_id' => ['required', 'exists:categories,id'],
            'categories.*.amount' => ['required', 'numeric', 'min:0'],
        ];
    }
}
