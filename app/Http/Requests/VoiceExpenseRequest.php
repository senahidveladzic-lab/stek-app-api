<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VoiceExpenseRequest extends FormRequest
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
            'text' => ['required', 'string', 'max:1000'],
        ];
    }
}
