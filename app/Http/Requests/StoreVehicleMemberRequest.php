<?php

namespace App\Http\Requests;

use App\Models\Vehicle;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreVehicleMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manageMembers', $this->route('vehicle')) ?? false;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        /** @var Vehicle $vehicle */
        $vehicle = $this->route('vehicle');

        return [
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::exists('users', 'email'),
                Rule::notIn([$vehicle->owner()->value('email')]),
            ],
            'role' => ['required', Rule::in(['viewer', 'manager'])],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['email' => Str::lower(trim($this->string('email')))]);
    }

    public function messages(): array
    {
        return [
            'email.exists' => 'That person needs an account with '.config('app.name').' before they can join the garage.',
            'email.not_in' => 'You already own this vehicle.',
        ];
    }
}
