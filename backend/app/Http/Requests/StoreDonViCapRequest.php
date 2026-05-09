<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDonViCapRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ma_don_vi'   => ['required', 'string', 'max:255', 'unique:don_vi_caps,ma_don_vi'],
            'ten_don_vi'  => ['required', 'string', 'max:255'],
            'loai_don_vi' => ['required', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'ma_don_vi.required'  => 'Mã đơn vị là bắt buộc.',
            'ma_don_vi.string'    => 'Mã đơn vị phải là chuỗi.',
            'ma_don_vi.unique'    => 'Mã đơn vị đã tồn tại.',
            'ma_don_vi.max'       => 'Mã đơn vị không được vượt quá 255 ký tự.',
            'ten_don_vi.required' => 'Tên đơn vị là bắt buộc.',
            'ten_don_vi.string'   => 'Tên đơn vị phải là chuỗi.',
            'ten_don_vi.max'      => 'Tên đơn vị không được vượt quá 255 ký tự.',
            'loai_don_vi.required'=> 'Loại đơn vị là bắt buộc.',
            'loai_don_vi.string'  => 'Loại đơn vị phải là chuỗi.',
            'loai_don_vi.max'     => 'Loại đơn vị không được vượt quá 50 ký tự.',
        ];
    }
}
