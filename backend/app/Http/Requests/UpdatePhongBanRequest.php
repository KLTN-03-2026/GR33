<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePhongBanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ma_phong_ban'  => [
                'sometimes', 
                'string', 
                'max:255', 
                Rule::unique('phong_bans')->ignore($this->route('phong_ban'))
            ],
            'ten_phong_ban' => ['sometimes', 'string', 'max:255'],
            'mo_ta'         => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'ma_phong_ban.string'    => 'Mã phòng ban phải là chuỗi.',
            'ma_phong_ban.unique'    => 'Mã phòng ban đã tồn tại.',
            'ma_phong_ban.max'       => 'Mã phòng ban không được vượt quá 255 ký tự.',
            'ten_phong_ban.string'   => 'Tên phòng ban phải là chuỗi.',
            'ten_phong_ban.max'      => 'Tên phòng ban không được vượt quá 255 ký tự.',
            'mo_ta.string'           => 'Mô tả phải là chuỗi.',
        ];
    }
}
