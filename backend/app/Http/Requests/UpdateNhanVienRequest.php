<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateNhanVienRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'chuc_vu_id'   => ['sometimes', 'exists:chuc_vus,id'],
            'ho_ten'       => ['sometimes', 'string', 'max:255'],
            'email'        => [
                'sometimes', 
                'string', 
                'email', 
                'max:255', 
                Rule::unique('nhan_viens')->ignore($this->route('nhan_vien'))
            ],
            'mat_khau'     => ['sometimes', 'string', 'min:6'],
            'so_dien_thoai' => ['nullable', 'string', 'regex:/^0[0-9]{9}$/'],
            'dia_chi'      => ['nullable', 'string'],
            'phong_ban_id' => ['nullable', 'exists:phong_bans,id'],
            'trang_thai'   => ['sometimes', 'integer', 'in:0,1,2'],
            'hinh_anh'     => ['sometimes', 'nullable', 'string', 'url'],
        ];
    }

    public function messages(): array
    {
        return [
            'chuc_vu_id.exists'   => 'Chức vụ không tồn tại.',
            'phong_ban_id.exists' => 'Phòng ban không tồn tại.',
            'ma_nhan_vien.string' => 'Mã nhân viên phải là chuỗi.',
            'ma_nhan_vien.unique' => 'Mã nhân viên đã tồn tại.',
            'ma_nhan_vien.max'    => 'Mã nhân viên không được vượt quá 255 ký tự.',
            'ho_ten.string'       => 'Họ tên phải là chuỗi.',
            'ho_ten.max'          => 'Họ tên không được vượt quá 255 ký tự.',
            'email.string'        => 'Email phải là chuỗi.',
            'email.email'         => 'Email không đúng định dạng.',
            'email.unique'        => 'Email đã tồn tại.',
            'email.max'           => 'Email không được vượt quá 255 ký tự.',
            'mat_khau.string'     => 'Mật khẩu phải là chuỗi.',
            'mat_khau.min'        => 'Mật khẩu phải có ít nhất 6 ký tự.',
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
