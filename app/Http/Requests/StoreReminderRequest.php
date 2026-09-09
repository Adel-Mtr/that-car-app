<?php

namespace App\Http\Requests;

use App\Models\Reminder;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReminderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', [Reminder::class, $this->route('vehicle')]) ?? false;
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
            'category' => ['required', Rule::in(['mot', 'tax', 'insurance', 'service', 'maintenance', 'warranty', 'breakdown', 'other'])],
            'description' => ['nullable', 'string', 'max:2000'],
            'due_at' => ['required', 'date', 'after_or_equal:today'],
            'lead_days' => ['required', 'integer', Rule::in([1, 3, 7, 14, 30, 60])],
            'recurrence' => ['nullable', Rule::in(['monthly', 'quarterly', 'yearly'])],
        ];
    }
}
