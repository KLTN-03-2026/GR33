<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateChucVuChucNangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'chuc_vu_id'   => ['sometimes', 'exists:chuc_vus,id'],
            'chuc_nang_id' => [
                'sometimes', 
                'exists:chuc_nangs,id',
                Rule::unique('chuc_vu_chuc_nangs')->where(function ($query) {
                    return $query->where('chuc_vu_id', $this->input('chuc_vu_id') ?? $this->route('chuc_vu_chuc_nang')->chuc_vu_id);
                })->ignore($this->route('chuc_vu_chuc_nang'))
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'chuc_vu_id.exists'     => 'Chức vụ không tồn tại.',
            'chuc_nang_id.exists'   => 'Chức năng không tồn tại.',
            'chuc_nang_id.unique'   => 'Chức năng này đã được gán cho chức vụ được chọn.',
        ];
    }
}
