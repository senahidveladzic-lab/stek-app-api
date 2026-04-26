<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'amount' => ['sometimes', 'numeric', 'min:0.01'],
            'currency' => ['sometimes', 'string', 'size:3'],
            'category_id' => ['sometimes', 'exists:categories,id'],
            'tag_id' => [
                'sometimes',
                'nullable',
                'integer',
                Rule::exists('tags', 'id')->where('household_id', $this->user()?->household_id),
            ],
            'description' => ['nullable', 'string', 'max:255'],
            'expense_date' => ['sometimes', 'date'],
        ];
    }
}
