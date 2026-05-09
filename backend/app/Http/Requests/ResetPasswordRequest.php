<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    /**
     * Xác định người dùng có quyền thực hiện yêu cầu này không.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Quy tắc xác thực.
     */
    public function rules(): array
    {
        return [
            'email'    => 'required|email',
            'token'    => 'required',
            'mat_khau' => 'required|min:6',
        ];
    }

    /**
     * Thông báo lỗi tiếng Việt.
     */
    public function messages(): array
    {
        return [
            'email.required'    => 'Vui lòng cung cấp địa chỉ Email.',
            'email.email'       => 'Địa chỉ Email không đúng định dạng.',
            'token.required'    => 'Mã xác thực không hợp lệ.',
            'mat_khau.required' => 'Vui lòng nhập mật khẩu mới.',
            'mat_khau.min'      => 'Mật khẩu phải có ít nhất 6 ký tự.',
        ];
    }

}
