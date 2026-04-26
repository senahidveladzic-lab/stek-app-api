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
            'audio' => ['required_without:text', 'file', 'mimes:mp4,m4a,mp3,wav,webm,ogg,flac', 'max:25600'],
            'text' => ['required_without:audio', 'string', 'max:1000'],
        ];
    }
}
