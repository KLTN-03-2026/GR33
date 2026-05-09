<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SinhVienUpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'ho_ten'        => 'required|string|max:255',
            'so_dien_thoai' => ['required', 'string', 'regex:/^0[0-9]{9}$/'],
            'dia_chi'       => 'nullable|string|max:500',
            'hinh_anh'      => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'ho_ten.required'        => 'Họ tên không được để trống.',
            'ho_ten.string'          => 'Họ tên phải là chuỗi ký tự.',
            'ho_ten.max'             => 'Họ tên không được vượt quá 255 ký tự.',
            'so_dien_thoai.required' => 'Số điện thoại là bắt buộc.',
            'so_dien_thoai.regex'    => 'Số điện thoại không đúng định dạng (phải có 10 số và bắt đầu bằng số 0).',
            'dia_chi.max'            => 'Địa chỉ không được vượt quá 500 ký tự.',
            'hinh_anh.image'         => 'File tải lên phải là hình ảnh.',
            'hinh_anh.mimes'         => 'Hình ảnh phải có định dạng jpeg, png, jpg hoặc gif.',
            'hinh_anh.max'           => 'Dung lượng ảnh không được vượt quá 2MB.',
        ];
    }
}
