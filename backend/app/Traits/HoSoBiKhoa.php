<?php

namespace App\Traits;

trait HoSoBiKhoa
{
    /**
     * Tự động khởi tạo Trait khi Model được instantiate.
     * Tên hàm bắt đầu bằng "initialize" + "TênTrait".
     */
    public function initializeHoSoBiKhoa()
    {
        // Tự động ép kiểu is_locked cho bất kỳ Model nào sử dụng Trait này
        $this->mergeCasts([
            'is_locked' => 'boolean',
        ]);
    }
}
