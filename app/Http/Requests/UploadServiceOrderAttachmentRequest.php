<?php

namespace App\Http\Requests;

use App\Services\FileStorage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Redirect;

class UploadServiceOrderAttachmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return FileStorage::attachmentValidationRules();
    }

    public function attributes(): array
    {
        return [
            'attachments' => 'فایل‌های ضمیمه',
            'attachments.*' => 'فایل ضمیمه',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            Redirect::back()->withInput()->withErrors($validator)
        );
    }
}
