<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AssignTechnicianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()->canManageRepairs();
    }

    public function rules(): array
    {
        return [
            'technician_id' => 'nullable|exists:users,id',
        ];
    }

    public function attributes(): array
    {
        return [
            'technician_id' => 'تعمیرکار',
        ];
    }
}
