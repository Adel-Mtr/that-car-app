<?php

namespace App\Http\Requests;

use App\Models\Booking;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', Booking::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'vehicle_id' => ['required', 'integer', 'exists:vehicles,id'],
            'specialist_id' => ['required', 'integer', 'exists:specialists,id'],
            'service' => ['required', 'string', 'min:3', 'max:255'],
            'description' => ['nullable', 'string', 'max:3000'],
            'requested_start_at' => ['required', 'date', 'after:now'],
        ];
    }
}
