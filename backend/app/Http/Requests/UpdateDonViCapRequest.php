<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDonViCapRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ma_don_vi'   => [
                'sometimes', 
                'string', 
                'max:255', 
                Rule::unique('don_vi_caps')->ignore($this->route('don_vi_cap'))
            ],
            'ten_don_vi'  => ['sometimes', 'string', 'max:255'],
            'loai_don_vi' => ['sometimes', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'ma_don_vi.string'    => 'Mã đơn vị phải là chuỗi.',
            'ma_don_vi.unique'    => 'Mã đơn vị đã tồn tại.',
            'ma_don_vi.max'       => 'Mã đơn vị không được vượt quá 255 ký tự.',
            'ten_don_vi.string'   => 'Tên đơn vị phải là chuỗi.',
            'ten_don_vi.max'      => 'Tên đơn vị không được vượt quá 255 ký tự.',
            'loai_don_vi.string'  => 'Loại đơn vị phải là chuỗi.',
            'loai_don_vi.max'     => 'Loại đơn vị không được vượt quá 50 ký tự.',
        ];
    }
}
