<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDuAnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ten_du_an'    => ['required', 'string', 'max:255'],
            'mo_ta'        => ['required', 'string'],
            'sinh_vien_id' => ['required', 'exists:sinh_viens,id'],
            'link_du_an'   => [
                'required', 
                'string', 
                'max:255',
                Rule::unique('du_ans', 'link_du_an')->where(function ($query) {
                    return $query->where('sinh_vien_id', $this->sinh_vien_id);
                })
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'ten_du_an.required'   => 'Tên dự án là bắt buộc.',
            'ten_du_an.string'     => 'Tên dự án phải là chuỗi.',
            'ten_du_an.max'        => 'Tên dự án không được vượt quá 255 ký tự.',
            'mo_ta.required'       => 'Mô tả là bắt buộc.',
            'mo_ta.string'         => 'Mô tả phải là chuỗi.',
            'sinh_vien_id.required'=> 'Sinh viên là bắt buộc.',
            'sinh_vien_id.exists'  => 'Sinh viên không tồn tại.',
            'link_du_an.required'  => 'Link dự án là bắt buộc.',
            'link_du_an.string'    => 'Link dự án phải là chuỗi.',
            'link_du_an.max'       => 'Link dự án không được vượt quá 255 ký tự.',
            'link_du_an.unique'    => 'Sinh viên này đã có một dự án với link này rồi.',
        ];
    }
}
