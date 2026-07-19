<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeviceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'exists:customers,id'],
            'type' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:255'],
            'asset_number' => ['nullable', 'string', 'max:255'],
            'has_guarantee' => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'customer_id' => 'مشتری',
            'type' => 'نوع دستگاه',
            'model' => 'مدل',
            'asset_number' => 'شماره اموال/سریال',
            'has_guarantee' => 'دارای گارانتی',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'has_guarantee' => $this->boolean('has_guarantee'),
        ]);
    }
}
