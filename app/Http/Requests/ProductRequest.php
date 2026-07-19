<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->canAccessAdminPanel();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('price')) {
            $this->merge(['price' => \App\Support\ShopFormat::toIntegerAmount($this->input('price'))]);
        }

        if (! $this->boolean('has_discount')) {
            $this->merge(['sale_price' => 0]);
        } elseif ($this->has('sale_price')) {
            $this->merge(['sale_price' => \App\Support\ShopFormat::toIntegerAmount($this->input('sale_price'))]);
        }

        if ($this->has('inventory_id') && $this->input('inventory_id') === '') {
            $this->merge(['inventory_id' => null]);
        }

        $this->merge([
            'inventory_linked' => $this->boolean('inventory_linked'),
        ]);
    }

    public function rules(): array
    {
        $productId = $this->route('product')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'category_id' => ['required', 'exists:product_categories,id'],
            'inventory_id' => [
                'nullable',
                'integer',
                'exists:inventories,id',
                Rule::unique('products', 'inventory_id')->ignore($productId),
            ],
            'price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0'], // Removed lt:price to allow products without discount
            'sku' => ['required', 'string', Rule::unique('products', 'sku')->ignore($productId)],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'short_description' => ['nullable', 'string'],
            'external_url' => ['nullable', 'url'],
            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],
            'images' => ['nullable', 'array'],
            'images.*' => [
                'nullable',
                'file',
                'mimes:'.config('upload.product_image_mimes'),
                'max:'.config('upload.max_kb'),
            ],
            'technical_specs' => ['nullable', 'array'],
            'change_reason' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'نام محصول',
            'name_en' => 'نام انگلیسی',
            'category_id' => 'دسته بندی',
            'inventory_id' => 'کالای انبار',
            'inventory_id.unique' => 'این کالای انبار قبلاً به محصول دیگری متصل شده است.',
            'price' => 'قیمت اصلی',
            'sale_price' => 'قیمت ویژه',
            'sku' => 'شناسه کالا (SKU)',
            'stock_quantity' => 'موجودی انبار',
            'description' => 'توضیحات کامل',
            'short_description' => 'توضیحات کوتاه',
            'external_url' => 'لینک خارجی',
            'is_active' => 'وضعیت فعال بودن',
            'is_featured' => 'محصول ویژه',
            'images' => 'تصاویر',
            'images.*' => 'تصویر محصول',
            'technical_specs' => 'مشخصات فنی',
            'change_reason' => 'دلیل تغییرات',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'sale_price.lt' => 'قیمت ویژه باید کمتر از قیمت اصلی باشد.',
            'sale_price.min' => 'قیمت ویژه نمی‌تواند منفی باشد.',
        ];
    }
}
