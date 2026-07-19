<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class InventoryAdjustmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()->canManageInventory();
    }

    public function rules(): array
    {
        return [
            'new_quantity' => 'required|integer|min:0',
            'notes' => 'required|string|max:1000',
        ];
    }

    public function attributes(): array
    {
        return [
            'new_quantity' => 'مقدار جدید',
            'notes' => 'علت تعدیل',
        ];
    }
}
