<?php

namespace App\Http\Requests;

use App\Enums\ServiceOrderStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Support\Facades\Auth;

class UpdateServiceOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'status' => ['required', new Enum(ServiceOrderStatus::class)],
        ];
    }

    public function attributes(): array
    {
        return [
            'status' => 'وضعیت سفارش',
        ];
    }
}
