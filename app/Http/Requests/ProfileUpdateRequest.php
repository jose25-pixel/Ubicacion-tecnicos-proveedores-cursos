<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'role' => ['nullable', 'in:user,technician,provider,admin'],
            'specialty' => ['nullable', 'string', 'max:255', 'required_if:role,technician'],
            'spare_parts_type' => ['nullable', 'in:washing,refrigerators,refrigeration_systems,mixed', 'required_if:role,provider'],
            'country' => ['nullable', 'string', 'max:120', 'required_if:role,technician,provider'],
            'state' => ['nullable', 'string', 'max:120', 'required_if:role,technician,provider'],
            'years_experience' => ['nullable', 'integer', 'min:0', 'max:70'],
            'phone' => ['nullable', 'string', 'max:30', 'required_if:role,technician,provider'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'studies' => ['nullable', 'string', 'max:500'],
            'diplomas' => ['nullable', 'string', 'max:500'],
            'diploma_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:4096'],
            'provider_photos' => ['nullable', 'array', 'max:4'],
            'provider_photos.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'address' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ];
    }
}
