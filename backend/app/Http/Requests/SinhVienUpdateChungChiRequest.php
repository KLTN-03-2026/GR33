<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request cập nhật chứng chỉ cho sinh viên
 */
class SinhVienUpdateChungChiRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'ten_chung_chi'       => ['required', 'string', 'max:255'],
            'don_vi_cap_id'       => ['nullable', 'exists:don_vi_caps,id', 'required_without:ten_don_vi_cap_khac'],
            'ten_don_vi_cap_khac' => ['nullable', 'string', 'max:255', 'required_without:don_vi_cap_id'],
            'loai_chung_chi'      => ['required', 'string', 'max:50'],
            'ngay_cap'            => ['required', 'date', 'before_or_equal:today'],
            'ngay_het_han'        => ['nullable', 'date', 'after_or_equal:ngay_cap'],
            'diem_so'             => ['nullable', 'string', 'max:50'],
            'xep_loai'            => ['nullable', 'string', 'max:50'],
            'file_dinh_kem'       => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'ten_chung_chi.required'       => 'Tên chứng chỉ là bắt buộc.',
            'don_vi_cap_id.required_without' => 'Vui lòng chọn đơn vị cấp hoặc nhập tên đơn vị ngoài.',
            'ten_don_vi_cap_khac.required_without' => 'Vui lòng chọn đơn vị cấp hoặc nhập tên đơn vị ngoài.',
            'loai_chung_chi.required'      => 'Vui lòng chọn loại chứng chỉ.',
            'ngay_cap.required'            => 'Ngày cấp là bắt buộc.',
            'ngay_cap.date'                => 'Ngày cấp phải là định dạng ngày.',
            'ngay_cap.before_or_equal'     => 'Ngày cấp không được là ngày trong tương lai.',
            'ngay_het_han.date'            => 'Ngày hết hạn phải là định dạng ngày.',
            'ngay_het_han.after_or_equal'  => 'Ngày hết hạn phải sau hoặc bằng ngày cấp.',
            'file_dinh_kem.mimes'          => 'Tài liệu phải có định dạng: jpg, jpeg, png, pdf.',
            'file_dinh_kem.max'            => 'Tài liệu không được vượt quá 5MB.',
        ];
    }
}
