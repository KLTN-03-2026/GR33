<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateChucNangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ma_chuc_nang'  => [
                'sometimes', 
                'string', 
                'max:255', 
                Rule::unique('chuc_nangs')->ignore($this->route('chuc_nang'))
            ],
            'ten_chuc_nang' => ['sometimes', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'ma_chuc_nang.string'    => 'Mã chức năng phải là chuỗi.',
            'ma_chuc_nang.unique'    => 'Mã chức năng đã tồn tại.',
            'ma_chuc_nang.max'       => 'Mã chức năng không được vượt quá 255 ký tự.',
            'ten_chuc_nang.string'   => 'Tên chức năng phải là chuỗi.',
            'ten_chuc_nang.max'      => 'Tên chức năng không được vượt quá 255 ký tự.',
        ];
    }
}
