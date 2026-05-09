<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

/**
 * Request thêm dự án cho sinh viên
 */
class SinhVienStoreDuAnRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $sinhVienId = Auth::guard('sanctum')->id();

        return [
            'ten_du_an'  => ['required', 'string', 'max:255'],
            'mo_ta'      => ['required', 'string'],
            'link_du_an' => [
                'required', 
                'string', 
                'max:255', 
                Rule::unique('du_ans', 'link_du_an')->where(function ($query) use ($sinhVienId) {
                    return $query->where('sinh_vien_id', $sinhVienId);
                })
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'ten_du_an.required'  => 'Tên dự án là bắt buộc.',
            'mo_ta.required'      => 'Mô tả dự án không được để trống.',
            'link_du_an.required' => 'Đường dẫn dự án (Link) là bắt buộc.',
            'link_du_an.unique'   => 'Bạn đã nộp một dự án với đường dẫn này rồi (vui lòng kiểm tra lại).',
        ];
    }
}
