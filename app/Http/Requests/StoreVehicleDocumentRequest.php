<?php

namespace App\Http\Requests;

use App\Models\VehicleDocument;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVehicleDocumentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', [VehicleDocument::class, $this->route('vehicle')]) ?? false;
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
            'type' => ['required', Rule::in(['service_invoice', 'mot', 'insurance', 'tax', 'warranty', 'receipt', 'v5c', 'other'])],
            'document_date' => ['nullable', 'date', 'before_or_equal:today'],
            'document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'extensions:pdf,jpg,jpeg,png,webp', 'max:10240'],
        ];
    }
}
