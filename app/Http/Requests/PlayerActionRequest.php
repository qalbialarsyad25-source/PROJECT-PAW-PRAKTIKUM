<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlayerActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'action' => ['required', 'string', 'min:2', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'action.required' => 'Ketikkan tindakan yang ingin kamu lakukan.',
            'action.min' => 'Tindakan terlalu singkat, minimal 2 karakter.',
            'action.max' => 'Tindakan terlalu panjang, maksimal 1000 karakter.',
        ];
    }
}
