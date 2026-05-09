<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateChungChiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ma_chung_chi'   => [
                'sometimes', 
                'string', 
                'max:255', 
                Rule::unique('chung_chis')->ignore($this->route('chung_chi'))
            ],
            'ten_chung_chi'  => ['required', 'string', 'max:255'],
            'sinh_vien_id'        => ['sometimes', 'exists:sinh_viens,id'],
            'don_vi_cap_id'       => ['nullable', 'exists:don_vi_caps,id', 'required_without:ten_don_vi_cap_khac'],
            'ten_don_vi_cap_khac' => ['nullable', 'string', 'max:255', 'required_without:don_vi_cap_id'],
            'loai_chung_chi' => ['sometimes', 'string', 'max:50'],
            'ngay_cap'       => ['sometimes', 'date', 'before_or_equal:today'],
            'ngay_het_han'   => ['nullable', 'date', 'after_or_equal:ngay_cap'],
            'diem_so'        => ['nullable', 'string', 'max:50'],
            'xep_loai'       => ['nullable', 'string', 'max:50'],
            'file_dinh_kem'  => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'ma_chung_chi.string'    => 'Mã chứng chỉ phải là chuỗi.',
            'ma_chung_chi.unique'    => 'Mã chứng chỉ đã tồn tại.',
            'ma_chung_chi.max'       => 'Mã chứng chỉ không được vượt quá 255 ký tự.',
            'ten_chung_chi.string'   => 'Tên chứng chỉ phải là chuỗi.',
            'ten_chung_chi.max'      => 'Tên chứng chỉ không được vượt quá 255 ký tự.',
            'sinh_vien_id.exists'    => 'Sinh viên không tồn tại.',
            'don_vi_cap_id.required_without' => 'Vui lòng chọn đơn vị cấp hoặc nhập tên đơn vị khác.',
            'don_vi_cap_id.exists'   => 'Đơn vị cấp không tồn tại.',
            'ten_don_vi_cap_khac.required_without' => 'Vui lòng nhập tên đơn vị cấp khác nếu không chọn từ danh sách.',
            'ten_don_vi_cap_khac.string' => 'Tên đơn vị cấp khác phải là chuỗi.',
            'ten_don_vi_cap_khac.max'    => 'Tên đơn vị cấp khác không được vượt quá 255 ký tự.',
            'loai_chung_chi.string'  => 'Loại chứng chỉ phải là chuỗi.',
            'loai_chung_chi.max'     => 'Loại chứng chỉ không được vượt quá 50 ký tự.',
            'ngay_cap.date'          => 'Ngày cấp không hợp lệ.',
            'ngay_cap.before_or_equal'=> 'Ngày cấp không được là ngày trong tương lai.',
            'ngay_het_han.date'      => 'Ngày hết hạn không hợp lệ.',
            'ngay_het_han.after_or_equal' => 'Ngày hết hạn phải sau hoặc bằng ngày cấp.',
            'diem_so.string'         => 'Điểm số phải là chuỗi.',
            'diem_so.max'            => 'Điểm số không được vượt quá 50 ký tự.',
            'xep_loai.string'        => 'Xếp loại phải là chuỗi.',
            'xep_loai.max'           => 'Xếp loại không được vượt quá 50 ký tự.',
        ];
    }
}
