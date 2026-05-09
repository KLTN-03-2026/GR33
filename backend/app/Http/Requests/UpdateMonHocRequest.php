<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMonHocRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ma_mon_hoc'  => [
                'sometimes', 
                'string', 
                'max:255', 
                Rule::unique('mon_hocs')->ignore($this->route('mon_hoc'))
            ],
            'ten_mon_hoc' => ['sometimes', 'string', 'max:255'],
            'so_tin_chi'  => ['sometimes', 'integer', 'min:1'],
            'mo_ta'       => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'ma_mon_hoc.string'  => 'Mã môn học phải là chuỗi.',
            'ma_mon_hoc.unique'  => 'Mã môn học đã tồn tại.',
            'ma_mon_hoc.max'     => 'Mã môn học không được vượt quá 255 ký tự.',
            'ten_mon_hoc.string' => 'Tên môn học phải là chuỗi.',
            'ten_mon_hoc.max'    => 'Tên môn học không được vượt quá 255 ký tự.',
            'so_tin_chi.integer' => 'Số tín chỉ phải là số nguyên.',
            'so_tin_chi.min'     => 'Số tín chỉ tối thiểu là 1.',
            'mo_ta.string'       => 'Mô tả phải là chuỗi.',
        ];
    }
}
