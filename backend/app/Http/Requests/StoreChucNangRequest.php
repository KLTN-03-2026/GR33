<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreChucNangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ma_chuc_nang'  => ['required', 'string', 'max:255', 'unique:chuc_nangs,ma_chuc_nang'],
            'ten_chuc_nang' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'ma_chuc_nang.required'  => 'Mã chức năng là bắt buộc.',
            'ma_chuc_nang.string'    => 'Mã chức năng phải là chuỗi.',
            'ma_chuc_nang.unique'    => 'Mã chức năng đã tồn tại.',
            'ma_chuc_nang.max'       => 'Mã chức năng không được vượt quá 255 ký tự.',
            'ten_chuc_nang.required' => 'Tên chức năng là bắt buộc.',
            'ten_chuc_nang.string'   => 'Tên chức năng phải là chuỗi.',
            'ten_chuc_nang.max'      => 'Tên chức năng không được vượt quá 255 ký tự.',
        ];
    }
}
