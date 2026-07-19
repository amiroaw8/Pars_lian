<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class InventoryStockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()->canManageInventory();
    }

    public function rules(): array
    {
        return [
            'quantity_change' => 'required|integer|min:1',
            'transaction_type' => 'required|in:purchase,sale,use,adjustment,return,warranty_sent,warranty_return',
            'notes' => 'nullable|string|max:1000',
            'receiver' => 'nullable|string|max:255',
            'organization' => 'nullable|string|max:255',
            'reason' => 'nullable|string|max:255',
        ];
    }

    public function attributes(): array
    {
        return [
            'quantity_change' => 'مقدار تغییر',
            'transaction_type' => 'نوع تراکنش',
            'notes' => 'توضیحات',
            'receiver' => 'تحویل گیرنده',
            'organization' => 'ارگان',
            'reason' => 'بابت',
        ];
    }
}
