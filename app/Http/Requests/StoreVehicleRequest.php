<?php

namespace App\Http\Requests;

use App\Models\Vehicle;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreVehicleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', Vehicle::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'registration' => [
                'required',
                'string',
                'min:2',
                'max:16',
                'regex:/^[A-Z0-9]+$/',
                Rule::unique('vehicles', 'registration')->where(
                    fn ($query) => $query->where('owner_id', $this->user()?->id),
                ),
            ],
            'current_mileage' => ['nullable', 'integer', 'min:0', 'max:2000000'],
            'insurance_due_at' => ['nullable', 'date'],
            'visibility' => ['nullable', Rule::in(['private', 'shared', 'public'])],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'registration' => Str::upper((string) preg_replace('/[^A-Z0-9]/i', '', $this->string('registration'))),
        ]);
    }

    public function messages(): array
    {
        return [
            'registration.unique' => 'That vehicle is already in your garage.',
            'registration.regex' => 'Enter a valid registration using letters and numbers.',
        ];
    }
}
