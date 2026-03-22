<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHouseholdRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isHouseholdOwner();
    }

    /**
     * @return array<string, array<string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'default_currency' => ['sometimes', 'string', 'size:3'],
        ];
    }
}
