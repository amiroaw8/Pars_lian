<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateCostsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()->canManageRepairs() || Auth::user()->canManageAccounting();
    }

    public function rules(): array
    {
        return [
            'costs' => 'required|array',
            'costs.*' => 'nullable|numeric|min:0',
        ];
    }

    public function attributes(): array
    {
        return [
            'costs' => 'هزینه‌ها',
            'costs.*' => 'هزینه',
        ];
    }
}
