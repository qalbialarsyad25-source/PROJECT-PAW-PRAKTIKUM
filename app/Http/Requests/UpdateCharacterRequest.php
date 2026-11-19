<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCharacterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:60'],
            'backstory' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama karakter wajib diisi.',
            'name.max' => 'Nama karakter maksimal 60 karakter.',
            'backstory.max' => 'Latar belakang karakter maksimal 2000 karakter.',
        ];
    }
}
