<?php

namespace App\Http\Requests;

use App\Models\Inventory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class RepairItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = Auth::user();

        return $user->isSuperAdmin()
            || $user->canManageRepairs()
            || $user->canManageAccounting()
            || $user->isReceptionist();
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('item_type') === 'service') {
            $this->merge(['item_type' => 'labor']);
        }

        $cost = $this->input('cost');
        if (is_string($cost)) {
            $cost = preg_replace('/[^\d.]/', '', str_replace(
                ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹', ','],
                ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', ''],
                $cost
            ));
        }

        $inventory = $this->filled('inventory_id') ? Inventory::find($this->input('inventory_id')) : null;

        if ($inventory && ! $this->filled('name')) {
            $this->merge(['name' => $inventory->name]);
        }

        $numericCost = is_numeric($cost) ? (float) $cost : 0.0;
        if ($inventory && $numericCost <= 0) {
            $numericCost = max(0, (int) round((float) $inventory->price));
        } elseif ($cost === null || $cost === '') {
            $numericCost = 0.0;
        }

        $this->merge(['cost' => $numericCost]);

        $name = trim((string) $this->input('name', ''));
        if ($name !== '') {
            $this->merge(['name' => $name]);
        }

        if (in_array($this->input('item_type'), ['labor', 'service', 'other'], true)) {
            $this->merge(['inventory_id' => null]);
        }
    }

    public function messages(): array
    {
        return [
            'name.required_without' => 'لطفاً شرح خدمت یا عنوان آیتم را وارد کنید.',
            'name.max' => 'شرح خدمت نباید بیشتر از :max کاراکتر باشد.',
        ];
    }

    public function rules(): array
    {
        return [
            'item_type' => 'required|in:part,labor,other,service',
            'inventory_id' => 'nullable|exists:inventories,id',
            'name' => 'required_without:inventory_id|nullable|string|max:10000',
            'quantity' => 'required|integer|min:1',
            'cost' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:10000',
        ];
    }

    public function attributes(): array
    {
        return [
            'item_type' => 'نوع آیتم',
            'inventory_id' => 'کالای انبار',
            'name' => 'نام آیتم',
            'quantity' => 'تعداد/مقدار',
            'cost' => 'هزینه',
            'description' => 'توضیحات',
        ];
    }
}
