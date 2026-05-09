<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBangDiemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'diem_qua_trinh' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'diem_cuoi_ky'   => ['nullable', 'numeric', 'min:0', 'max:10'],
        ];
    }

    public function messages(): array
    {
        return [
            'diem_qua_trinh.numeric'  => 'Điểm quá trình phải là số.',
            'diem_qua_trinh.min'      => 'Điểm quá trình tối thiểu là 0.',
            'diem_qua_trinh.max'      => 'Điểm quá trình tối đa là 10.',
            'diem_cuoi_ky.numeric'    => 'Điểm cuối kỳ phải là số.',
            'diem_cuoi_ky.min'        => 'Điểm cuối kỳ tối thiểu là 0.',
            'diem_cuoi_ky.max'        => 'Điểm cuối kỳ tối đa là 10.',
        ];
    }
}
