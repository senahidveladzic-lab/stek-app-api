<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InviteMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        $household = $this->user()->household;

        return $household
            && $household->owner_id === $this->user()->id
            && $household->members()->count() < $household->max_members;
    }

    /**
     * @return array<string, array<string>>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:255'],
        ];
    }
}
