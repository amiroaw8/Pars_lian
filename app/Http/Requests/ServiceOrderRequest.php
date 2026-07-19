<?php

namespace App\Http\Requests;

use App\Services\FileStorage;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Redirect;

class ServiceOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('debt_amount')) {
            $this->merge([
                'debt_amount' => \App\Support\ShopFormat::toIntegerAmount($this->input('debt_amount')),
            ]);
        }
    }

    public function rules(): array
    {
        if ($this->boolean('debt_only')) {
            return [
                'debt_amount' => ['required', 'numeric', 'min:1'],
                'debt_reason' => ['nullable', 'string', 'max:500'],
            ];
        }

        $rules = [
            'customer_id' => ['required', 'exists:customers,id'],
            'service_type' => ['required', 'in:in_company,on_site'],
            'receiver_name' => ['nullable', 'string', 'max:255'],
            'receiver_phone' => ['nullable', 'string', 'max:20'],
            'debt_amount' => ['nullable', 'numeric', 'min:0'],
            'debt_reason' => ['nullable', 'string', 'max:500'],
            'fault' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
            'user_department' => ['nullable', 'string', 'max:255'],
            'accessories' => ['nullable', 'string'],
            'inventory_items' => ['nullable', 'array'],
            'inventory_items.*.inventory_id' => ['nullable', 'exists:inventories,id'],
            'inventory_items.*.quantity' => ['nullable', 'integer', 'min:1'],
            'inventory_items.*.note' => ['nullable', 'string'],
        ];

        $rules = array_merge($rules, FileStorage::attachmentValidationRules(false));

        if ($this->isMethod('POST')) {
            if ($this->boolean('skip_device_registration')) {
                $rules['skip_device_registration'] = ['sometimes', 'boolean'];
                $rules['device_type'] = ['nullable', 'string', 'max:255'];
                $rules['device_model'] = ['nullable', 'string', 'max:255'];
            } else {
                $rules['device_type'] = ['required', 'string', 'max:255'];
                $rules['device_model'] = ['required', 'string', 'max:255'];
            }
            $rules['serial_number'] = ['nullable', 'string', 'max:255'];
            $rules['asset_number'] = ['nullable', 'string', 'max:255'];
            $rules['technician_id'] = ['required', 'exists:users,id'];
        }

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['device_type'] = ['required', 'string', 'max:255'];
            $rules['device_model'] = ['required', 'string', 'max:255'];
            $rules['serial_number'] = ['nullable', 'string', 'max:255'];
            $rules['asset_number'] = ['nullable', 'string', 'max:255'];
            $rules['technician_id'] = ['nullable', 'exists:users,id'];
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'customer_id' => 'مشتری',
            'device_type' => 'نوع دستگاه',
            'device_model' => 'مدل دستگاه',
            'service_type' => 'نوع سرویس',
            'receiver_name' => 'نام تحویل دهنده',
            'receiver_phone' => 'شماره تماس تحویل دهنده',
            'fault' => 'ایراد فنی',
            'notes' => 'توضیحات',
            'serial_number' => 'شماره سریال',
            'asset_number' => 'شماره اموال',
            'technician_id' => 'تکنسین',
            'attachments' => 'فایل‌های ضمیمه',
            'debt_amount' => 'میزان بدهی',
            'debt_reason' => 'دلیل بدهی',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            Redirect::back()->withInput()->withErrors($validator)
        );
    }
}
