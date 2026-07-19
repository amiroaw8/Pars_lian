<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $customerId = $this->route('customer')?->id;
        $phoneRules = [
            'required',
            'string',
            'regex:/^09[0-9]{9}$/',
        ];

        if (!$this->boolean('in_person')) {
            $phoneRules[] = Rule::unique('customers', 'phone')
                ->ignore($customerId)
                ->whereNull('deleted_at');
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => $phoneRules,
            'address' => ['nullable', 'string'],
            'in_person' => ['sometimes', 'boolean'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'نام مشتری',
            'phone' => 'شماره تلفن',
            'address' => 'آدرس',
            'password' => 'رمز عبور',
        ];
    }
}
