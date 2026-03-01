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
            'description' => 'required',
            'status' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique'   => 'This Role already!',
            'name.required' => 'Please Enter Role Name!',
            'code.required' => 'Please Enter Role Code!',
            'description.required' => 'Please Enter Description!',
            'status.required' => 'Please Select Status!',
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