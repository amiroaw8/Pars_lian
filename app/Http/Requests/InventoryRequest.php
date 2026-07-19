<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:50', 'unique:inventories,sku,' . $this->route('inventory')?->id],
            'type' => ['required', 'in:device,part,tool,other'],
            'condition' => ['required', 'in:new,used'],
            'quantity' => ['required', 'integer', 'min:0'],
            'min_quantity' => ['required', 'integer', 'min:0'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'color' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'device_code' => ['nullable', 'string', 'max:100'],
            'rack_location' => ['nullable', 'string', 'max:100'],
            'compatibility_notes' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'نام کالا',
            'sku' => 'کد کالا (SKU)',
            'type' => 'نوع کالا',
            'condition' => 'وضعیت کالا',
            'quantity' => 'موجودی',
            'min_quantity' => 'حداقل موجودی',
            'price' => 'قیمت',
            'color' => 'رنگ',
            'device_code' => 'کد دستگاه مرتبط',
            'rack_location' => 'موقعیت در انبار',
            'compatibility_notes' => 'یادداشت‌های سازگاری',
        ];
    }
}
