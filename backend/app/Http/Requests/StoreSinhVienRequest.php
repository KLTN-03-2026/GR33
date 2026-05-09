<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSinhVienRequest extends FormRequest
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
            'ho_ten'       => ['required', 'string', 'max:255'],
            'nganh_hoc'    => ['required', 'string', 'max:255'],
            'mat_khau'     => ['nullable', 'string', 'min:6'],
            'email'        => ['required', 'string', 'email', 'max:255', 'unique:sinh_viens,email'],
            'nam_bat_dau'  => ['required', 'integer'],
            'so_nam_hoc'   => ['required', 'integer', 'in:4,5'],
            'so_dien_thoai' => ['nullable', 'string', 'regex:/^0[0-9]{9}$/'],
            'dia_chi'      => ['nullable', 'string'],
            'trang_thai'   => ['nullable', 'integer', 'in:0,1,2,3'],
            'hinh_anh'     => ['nullable', 'string', 'url'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'ho_ten.required'       => 'Họ tên là bắt buộc.',
            'ho_ten.string'         => 'Họ tên phải là chuỗi.',
            'ho_ten.max'            => 'Họ tên không được vượt quá 255 ký tự.',
            'nganh_hoc.required'    => 'Ngành học là bắt buộc.',
            'nganh_hoc.string'      => 'Ngành học phải là chuỗi.',
            'nganh_hoc.max'         => 'Ngành học không được vượt quá 255 ký tự.',
            'mat_khau.required'     => 'Mật khẩu là bắt buộc.',
            'mat_khau.string'       => 'Mật khẩu phải là chuỗi.',
            'mat_khau.min'          => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'email.required'        => 'Email là bắt buộc.',
            'email.string'          => 'Email phải là chuỗi.',
            'email.email'           => 'Email không đúng định dạng.',
            'email.unique'          => 'Email đã tồn tại.',
            'email.max'             => 'Email không được vượt quá 255 ký tự.',
            'nam_bat_dau.required'  => 'Năm bắt đầu là bắt buộc.',
            'nam_bat_dau.integer'   => 'Năm bắt đầu phải là số nguyên.',
            'so_nam_hoc.required'   => 'Số năm học là bắt buộc.',
            'so_nam_hoc.integer'    => 'Số năm học phải là số nguyên.',
            'so_nam_hoc.in'         => 'Số năm học chỉ có thể là 4 hoặc 5.',
            'so_dien_thoai.string'  => 'Số điện thoại phải là chuỗi.',
            'so_dien_thoai.regex'   => 'Số điện thoại không đúng định dạng.',
            'dia_chi.string'        => 'Địa chỉ phải là chuỗi.',
            'trang_thai.integer'    => 'Trạng thái phải là số nguyên.',
            'trang_thai.in'         => 'Trạng thái không hợp lệ.',
            'hinh_anh.string'       => 'Hình ảnh phải là chuỗi.',
            'hinh_anh.url'          => 'Hình ảnh không đúng định dạng URL.',
        ];
    }
}
