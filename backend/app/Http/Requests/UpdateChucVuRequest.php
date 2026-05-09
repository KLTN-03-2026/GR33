<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateChucVuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ten_chuc_vu' => [
                'sometimes', 
                'string', 
                'max:255', 
                Rule::unique('chuc_vus')->ignore($this->route('chuc_vu'))
            ],
            'trang_thai'  => ['sometimes', 'integer', 'in:0,1,2'],
        ];
    }

    public function messages(): array
    {
        return [
            'ten_chuc_vu.string' => 'Tên chức vụ phải là chuỗi.',
            'ten_chuc_vu.unique' => 'Tên chức vụ đã tồn tại.',
            'ten_chuc_vu.max'    => 'Tên chức vụ không được vượt quá 255 ký tự.',
            'trang_thai.integer' => 'Trạng thái phải là số nguyên.',
            'trang_thai.in'      => 'Trạng thái không hợp lệ.',
        ];
    }
}
