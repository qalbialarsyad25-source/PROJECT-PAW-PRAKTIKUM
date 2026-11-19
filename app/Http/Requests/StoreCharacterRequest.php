<?php

namespace App\Http\Requests;

use App\Services\GameService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCharacterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $validClasses = array_keys(GameService::CLASSES);
        $validRaces = array_keys(GameService::RACES);

        return [
            'name' => ['required', 'string', 'max:60'],
            'race' => ['required', 'string', Rule::in($validRaces)],
            'class' => ['required', 'string', Rule::in($validClasses)],
            'backstory' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama karakter wajib diisi.',
            'name.max' => 'Nama karakter maksimal 60 karakter.',
            'race.required' => 'Pilihlah salah satu ras karakter.',
            'race.in' => 'Ras yang dipilih tidak valid.',
            'class.required' => 'Pilihlah salah satu kelas karakter.',
            'class.in' => 'Kelas yang dipilih tidak valid.',
            'backstory.max' => 'Latar belakang karakter maksimal 2000 karakter.',
        ];
    }
}
