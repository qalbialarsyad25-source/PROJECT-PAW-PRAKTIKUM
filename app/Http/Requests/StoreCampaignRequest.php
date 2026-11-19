<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->user()?->id;

        return [
            'character_id' => [
                'required',
                'integer',
                Rule::exists('characters', 'id')->where(function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                }),
            ],
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
            'difficulty' => ['required', 'string', Rule::in(['Easy', 'Medium', 'Hard', 'Legendary'])],
        ];
    }

    public function messages(): array
    {
        return [
            'character_id.required' => 'Pilihlah salah satu karakter Anda untuk memulai petualangan.',
            'character_id.exists' => 'Karakter yang dipilih tidak ditemukan atau bukan milik Anda.',
            'title.required' => 'Judul campaign petualangan wajib diisi.',
            'difficulty.required' => 'Pilihlah tingkat kesulitan petualangan.',
            'difficulty.in' => 'Tingkat kesulitan tidak valid.',
        ];
    }
}
