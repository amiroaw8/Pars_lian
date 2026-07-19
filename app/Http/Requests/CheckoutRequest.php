<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shipping_first_name' => ['required', 'string', 'max:255'],
            'shipping_last_name' => ['required', 'string', 'max:255'],
            'shipping_email' => ['nullable', 'email', 'max:255'],
            'shipping_phone' => ['required', 'string', 'max:20'],
            'shipping_address' => ['required', 'string', 'max:500'],
            'shipping_city' => ['required', 'string', 'max:255'],
            'shipping_state' => ['required', 'string', 'max:255'],
            'shipping_postal_code' => [
                $this->isDeliveryService() ? 'required' : 'nullable',
                'string',
                'max:20',
            ],
            'shipping_method' => ['required', 'in:tipax,dekapost,snapp,pickup'],
            'payment_method' => ['required', 'in:cod,online'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($this->input('payment_method') === 'cod' && $this->input('shipping_method') !== 'pickup') {
                $validator->errors()->add('payment_method', 'پرداخت در محل فقط برای تحویل حضوری امکان‌پذیر است.');
            }
        });
    }

    private function isDeliveryService(): bool
    {
        return in_array($this->input('shipping_method'), ['tipax', 'dekapost', 'snapp'], true);
    }

    public function attributes(): array
    {
        return [
            'shipping_first_name' => 'نام',
            'shipping_last_name' => 'نام خانوادگی',
            'shipping_email' => 'ایمیل',
            'shipping_phone' => 'شماره تماس',
            'shipping_address' => 'آدرس',
            'shipping_city' => 'شهر',
            'shipping_state' => 'استان',
            'shipping_postal_code' => 'کد پستی',
            'shipping_method' => 'روش ارسال',
            'payment_method' => 'روش پرداخت',
        ];
    }
}
