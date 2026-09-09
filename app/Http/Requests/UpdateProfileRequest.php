<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:80'],
            'username' => [
                'required',
                'string',
                'min:3',
                'max:30',
                'alpha_dash:ascii',
                'lowercase',
                Rule::unique('users', 'username')->ignore($this->user()),
            ],
            'bio' => ['nullable', 'string', 'max:500'],
            'postcode' => ['nullable', 'string', 'max:16'],
            'primary_goal' => ['required', Rule::in(['maintain', 'project', 'events', 'multi_vehicle'])],
            'email_reminders' => ['sometimes', 'boolean'],
        ];
    }
}
