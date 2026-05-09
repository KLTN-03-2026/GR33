<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request cập nhật dự án cho sinh viên
 */
class SinhVienUpdateDuAnRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'ten_du_an'  => ['required', 'string', 'max:255'],
            'mo_ta'      => ['required', 'string'],
            'link_du_an' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'ten_du_an.required'  => 'Tên dự án là bắt buộc.',
            'mo_ta.required'      => 'Mô tả dự án không được để trống.',
            'link_du_an.required' => 'Đường dẫn dự án (Link) là bắt buộc.',
        ];
    }
}
