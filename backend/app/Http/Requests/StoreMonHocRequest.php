<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMonHocRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ma_mon_hoc'  => ['required', 'string', 'max:255', 'unique:mon_hocs,ma_mon_hoc'],
            'ten_mon_hoc' => ['required', 'string', 'max:255'],
            'so_tin_chi'  => ['required', 'integer', 'min:1'],
            'mo_ta'       => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'ma_mon_hoc.required'  => 'Mã môn học là bắt buộc.',
            'ma_mon_hoc.string'    => 'Mã môn học phải là chuỗi.',
            'ma_mon_hoc.unique'    => 'Mã môn học đã tồn tại.',
            'ma_mon_hoc.max'       => 'Mã môn học không được vượt quá 255 ký tự.',
            'ten_mon_hoc.required' => 'Tên môn học là bắt buộc.',
            'ten_mon_hoc.string'   => 'Tên môn học phải là chuỗi.',
            'ten_mon_hoc.max'      => 'Tên môn học không được vượt quá 255 ký tự.',
            'so_tin_chi.required'  => 'Số tín chỉ là bắt buộc.',
            'so_tin_chi.integer'   => 'Số tín chỉ phải là số nguyên.',
            'so_tin_chi.min'       => 'Số tín chỉ tối thiểu là 1.',
            'mo_ta.string'         => 'Mô tả phải là chuỗi.',
        ];
    }
}
