<?php

namespace App\Http\Requests;

use App\Models\DeviceType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;

class DeviceTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()->canManageInventory() || Auth::user()->isReceptionist();
    }

    public function rules(): array
    {
        $id = $this->route('device_type')?->id;
        
        return [
            'name' => 'required|string|max:255|unique:device_types,name,' . $id,
            'parent_id' => 'nullable|exists:device_types,id',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $parentId = $this->input('parent_id');
            if ($parentId === null || $parentId === '') {
                return;
            }

            $parentId = (int) $parentId;
            $id = (int) ($this->route('device_type')?->id ?? 0);

            if ($id > 0 && $parentId === $id) {
                $validator->errors()->add('parent_id', 'دسته والد نمی‌تواند خودِ همین دسته باشد.');

                return;
            }

            if ($id > 0 && in_array($id, DeviceType::descendantIdsFor($parentId), true)) {
                $validator->errors()->add('parent_id', 'انتخاب این والد باعث حلقه در ساختار درختی می‌شود.');
            }
        });
    }

    public function attributes(): array
    {
        return [
            'name' => 'نام نوع دستگاه',
            'parent_id' => 'دسته والد',
        ];
    }
}
