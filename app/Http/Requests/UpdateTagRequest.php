<?php

namespace App\Http\Requests;

use App\Models\Tag;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        $tag = $this->route('tag');

        return $tag instanceof Tag
            && ($this->user()?->can('update', $tag) ?? false);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $tag = $this->route('tag');
        $tagId = $tag instanceof Tag ? $tag->id : null;
        $householdId = $this->user()?->household_id;

        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                Rule::unique('tags', 'name')
                    ->where('household_id', $householdId)
                    ->ignore($tagId),
            ],
            'color' => ['sometimes', 'required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ];
    }
}
