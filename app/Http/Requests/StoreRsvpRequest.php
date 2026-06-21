<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRsvpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'guest_name' => ['required', 'string', 'max:255'],
            'attendance' => ['required', Rule::in(['hadir', 'tidak_hadir', 'ragu_ragu'])],
            'total_guests' => ['required', 'integer', 'min:1', 'max:10'],
            'message' => ['nullable', 'string', 'max:1000'],
        ];
    }
}