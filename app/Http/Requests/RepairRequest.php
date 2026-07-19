<?php

namespace App\Http\Requests;

use App\Enums\ServiceOrderStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class RepairRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', new Enum(ServiceOrderStatus::class)],
            'technician_id' => ['nullable', 'exists:users,id'],
            'repair_steps' => ['nullable', 'string'],
            'used_parts' => ['nullable', 'string'],
            'service_cost' => ['nullable', 'numeric', 'min:0'],
            'costs' => ['nullable', 'array'],
            'costs.*' => ['nullable', 'numeric', 'min:0'],
            'fault' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'status' => 'وضعیت',
            'technician_id' => 'تعمیرکار',
            'repair_steps' => 'مراحل تعمیر',
            'used_parts' => 'قطعات استفاده شده',
            'service_cost' => 'هزینه خدمات',
            'costs' => 'هزینه‌ها',
        ];
    }
}
