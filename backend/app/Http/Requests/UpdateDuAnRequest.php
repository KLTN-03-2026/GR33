<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDuAnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ten_du_an'          => ['sometimes', 'string', 'max:255'],
            'mo_ta'              => ['sometimes', 'string'],
            'sinh_vien_id'       => ['sometimes', 'exists:sinh_viens,id'],
            'link_du_an'         => ['sometimes', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'ten_du_an.string'   => 'Tên dự án phải là chuỗi.',
            'ten_du_an.max'      => 'Tên dự án không được vượt quá 255 ký tự.',
            'mo_ta.string'       => 'Mô tả phải là chuỗi.',
            'sinh_vien_id.exists'  => 'Sinh viên không tồn tại.',
            'link_du_an.string'    => 'Link dự án phải là chuỗi.',
            'link_du_an.max'       => 'Link dự án không được vượt quá 255 ký tự.',
        ];
    }
}
