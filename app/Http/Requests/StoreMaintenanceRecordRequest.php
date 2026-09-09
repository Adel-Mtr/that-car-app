<?php

namespace App\Http\Requests;

use App\Models\MaintenanceRecord;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMaintenanceRecordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', [MaintenanceRecord::class, $this->route('vehicle')]) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'type' => ['required', Rule::in(['service', 'repair', 'inspection', 'upgrade', 'mot_advisory', 'tyres', 'other'])],
            'status' => ['required', Rule::in(['planned', 'completed'])],
            'urgency' => ['required', Rule::in(['routine', 'attention', 'critical'])],
            'description' => ['nullable', 'string', 'max:3000'],
            'due_at' => ['nullable', 'date'],
            'completed_at' => ['nullable', 'date', 'before_or_equal:today'],
            'mileage' => ['nullable', 'integer', 'min:0', 'max:2000000'],
            'cost' => ['nullable', 'numeric', 'min:0', 'max:1000000'],
            'provider_name' => ['nullable', 'string', 'max:255'],
        ];
    }
}
