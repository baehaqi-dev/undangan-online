<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInvitationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'slug' => ['sometimes', 'string', 'max:255', 'alpha_dash', Rule::unique('invitations', 'slug')->ignore($this->invitation)],
            'groom_name' => ['sometimes', 'string', 'max:255'],
            'bride_name' => ['sometimes', 'string', 'max:255'],
            'event_date' => ['sometimes', 'date'],
            'akad_time' => ['nullable', 'date_format:H:i'],
            'resepsi_time' => ['nullable', 'date_format:H:i'],
            'location' => ['sometimes', 'string', 'max:255'],
            'location_url' => ['nullable', 'url', 'max:255'],
            'description' => ['nullable', 'string'],
        ];
    }
}