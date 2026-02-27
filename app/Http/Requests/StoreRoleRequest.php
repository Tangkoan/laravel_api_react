<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        // បើជាការ Update យើងត្រូវអនុញ្ញាតឱ្យស្ទួនឈ្មោះសម្រាប់ ID ខ្លួនឯង
        // $roleId = $this->route('role'); 
        $id = $this->route('role') ?? $this->route('id');

        return [
            'name' => 'required|unique:roles,name,' . $id,
            'code' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique'   => 'ឈ្មោះនេះមានរួចហើយ!',
            'name.required' => 'សូមបញ្ចូលឈ្មោះ Role!',
            'code.required' => 'សូមបញ្ចូលកូដ Role!',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status'  => 'error',
            'message' => $validator->errors()->first(),
        ], 422));
    }
}