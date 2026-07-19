<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Check if we are editing an existing user
        $user = $this->route('user');
        
        // Handle both model binding and ID string
        $userId = null;
        if ($user instanceof \Illuminate\Database\Eloquent\Model) {
            $userId = $user->id;
        } elseif (is_string($user) || is_numeric($user)) {
            $userId = $user;
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => [
                'required', 
                'string', 
                'max:20', 
                Rule::unique('users', 'phone')->ignore($userId)
            ],
            'email' => [
                'nullable', 
                'email', 
                Rule::unique('users', 'email')->ignore($userId)
            ],
            'password' => [
                $userId ? 'nullable' : 'required', 
                'string', 
                'min:8', 
                'confirmed'
            ],
            'role' => ['required', 'array', 'min:1'],
            'role.*' => ['exists:roles,name'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'نام و نام خانوادگی',
            'phone' => 'شماره تلفن',
            'email' => 'ایمیل',
            'password' => 'رمز عبور',
            'password_confirmation' => 'تکرار رمز عبور',
            'role' => 'نقش‌های کاربر',
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator): void
    {
        parent::failedValidation($validator);
    }

    public function messages(): array
    {
        return [
            'required' => 'فیلد :attribute الزامی است.',
            'string' => 'فیلد :attribute باید متنی باشد.',
            'max' => 'فیلد :attribute نباید بیشتر از :max کاراکتر باشد.',
            'min' => 'فیلد :attribute نباید کمتر از :min کاراکتر باشد.',
            'unique' => ':attribute وارد شده قبلاً در سیستم ثبت شده است.',
            'email' => 'فرمت :attribute معتبر نیست.',
            'confirmed' => 'تکرار :attribute با مقدار اصلی مطابقت ندارد.',
            'role.required' => 'انتخاب حداقل یک نقش برای کاربر الزامی است.',
            'role.min' => 'انتخاب حداقل یک نقش برای کاربر الزامی است.',
            'exists' => 'مقدار انتخاب شده برای :attribute معتبر نیست.',
        ];
    }
}
