<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AccountingServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()->canManageAccounting();
    }

    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:0'],
            'description' => ['required', 'string'],
            'service_order_id' => ['required', 'exists:service_orders,id'],
            'technician_id' => ['nullable', 'exists:users,id'],
            'transaction_date' => ['nullable', 'date'],
        ];
    }

    public function attributes(): array
    {
        return [
            'amount' => 'مبلغ',
            'description' => 'توضیحات',
            'service_order_id' => 'سفارش سرویس',
            'technician_id' => 'تعمیرکار',
            'transaction_date' => 'تاریخ تراکنش',
        ];
    }
}
