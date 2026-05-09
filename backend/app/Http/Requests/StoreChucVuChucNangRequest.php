<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreChucVuChucNangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'chuc_vu_id'   => ['required', 'exists:chuc_vus,id'],
            'chuc_nang_id' => [
                'required', 
                'exists:chuc_nangs,id',
                Rule::unique('chuc_vu_chuc_nangs')->where(function ($query) {
                    return $query->where('chuc_vu_id', $this->chuc_vu_id);
                })
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'chuc_vu_id.required'   => 'Chức vụ là bắt buộc.',
            'chuc_vu_id.exists'     => 'Chức vụ không tồn tại.',
            'chuc_nang_id.required' => 'Chức năng là bắt buộc.',
            'chuc_nang_id.exists'   => 'Chức năng không tồn tại.',
            'chuc_nang_id.unique'   => 'Chức năng này đã được gán cho chức vụ được chọn.',
        ];
    }
}
