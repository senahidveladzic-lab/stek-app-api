<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreExpenseRequest extends FormRequest
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
            'amount' => ['required', 'numeric', 'min:0.01'],
            'currency' => ['sometimes', 'string', 'size:3'],
            'category_id' => ['required', 'exists:categories,id'],
            'tag_id' => [
                'nullable',
                'integer',
                Rule::exists('tags', 'id')->where('household_id', $this->user()?->household_id),
            ],
            'description' => ['nullable', 'string', 'max:255'],
            'expense_date' => ['required', 'date'],
            'voice_correction' => ['sometimes', 'nullable', 'array'],
            'voice_correction.whisper_transcript' => ['sometimes', 'nullable', 'string', 'max:2000'],
            'voice_correction.original_description' => ['sometimes', 'nullable', 'string', 'max:255'],
            'voice_correction.original_category_key' => ['sometimes', 'nullable', 'string', 'max:50'],
            'voice_correction.original_amount' => ['sometimes', 'nullable', 'numeric', 'min:0'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'amount.required' => __('validation.required', ['attribute' => __('expenses.amount')]),
            'amount.numeric' => __('validation.numeric', ['attribute' => __('expenses.amount')]),
            'category_id.required' => __('validation.required', ['attribute' => __('expenses.category')]),
            'expense_date.required' => __('validation.required', ['attribute' => __('expenses.date')]),
        ];
    }
}
