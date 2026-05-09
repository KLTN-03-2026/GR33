<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLopHocRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ma_lop_hoc'    => ['required', 'string', 'max:255', 'unique:lop_hocs,ma_lop_hoc'],
            'ten_lop_hoc'   => ['required', 'string', 'max:255'],
            'mon_hoc_id'    => ['required', 'exists:mon_hocs,id'],
            'giang_vien_id' => ['nullable', 'exists:nhan_viens,id'],
            'nam_hoc'       => ['required', 'string', 'max:255'],
            'hoc_ky'        => ['required', 'integer', 'min:1', 'max:3'],
            'trang_thai'    => ['required', 'in:sap_bat_dau,dang_mo,da_ket_thuc'],
            'si_so'         => ['nullable', 'integer', 'min:0', 'max:40'],
        ];
    }

    public function messages(): array
    {
        return [
            'ma_lop_hoc.required'  => 'Mã lớp học là bắt buộc.',
            'ma_lop_hoc.string'    => 'Mã lớp học phải là chuỗi.',
            'ma_lop_hoc.unique'    => 'Mã lớp học đã tồn tại.',
            'ma_lop_hoc.max'       => 'Mã lớp học không được vượt quá 255 ký tự.',
            'ten_lop_hoc.required' => 'Tên lớp học là bắt buộc.',
            'ten_lop_hoc.string'   => 'Tên lớp học phải là chuỗi.',
            'ten_lop_hoc.max'      => 'Tên lớp học không được vượt quá 255 ký tự.',
            'mon_hoc_id.required'  => 'Môn học là bắt buộc.',
            'mon_hoc_id.exists'    => 'Môn học không hợp lệ.',
            'giang_vien_id.exists' => 'Giảng viên không tồn tại.',
            'nam_hoc.required'     => 'Năm học là bắt buộc.',
            'nam_hoc.string'       => 'Năm học phải là chuỗi.',
            'nam_hoc.max'          => 'Năm học không được vượt quá 255 ký tự.',
            'hoc_ky.required'      => 'Học kỳ là bắt buộc.',
            'hoc_ky.integer'       => 'Học kỳ phải là số nguyên.',
            'hoc_ky.min'           => 'Học kỳ không hợp lệ.',
            'hoc_ky.max'           => 'Học kỳ không hợp lệ.',
            'trang_thai.required'  => 'Trạng thái là bắt buộc.',
            'trang_thai.in'        => 'Trạng thái không hợp lệ.',
            'si_so.integer'        => 'Sĩ số phải là số nguyên.',
            'si_so.min'            => 'Sĩ số tối thiểu là 0.',
            'si_so.max'            => 'Sĩ số tối đa là 40.',
        ];
    }
}
