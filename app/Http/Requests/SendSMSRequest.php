<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SendSMSRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'phone' => 'required|string|regex:/^09[0-9]{9}$/',
            'message' => 'required|string|max:500',
        ];
    }

    public function attributes(): array
    {
        return [
            'phone' => 'شماره تلفن',
            'message' => 'متن پیامک',
        ];
    }
}
