<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CartItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'quantity' => 'required|integer|min:0|max:99',
        ];
    }

    public function attributes(): array
    {
        return [
            'quantity' => 'تعداد',
        ];
    }
}
