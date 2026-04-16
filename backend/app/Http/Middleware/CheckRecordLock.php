<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\BangDiem;

class CheckRecordLock
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Chỉ kiểm tra cho các hành động sửa (PUT/PATCH) và xóa (DELETE)
        if (!in_array($request->method(), ['PUT', 'PATCH', 'DELETE'])) {
            return $next($request);
        }

        // Tìm đối tượng record từ Route parameters
        $record = null;
        $params = $request->route()->parameters();
        
        // Ưu tiên kiểm tra các bảng có cơ chế khóa
        $record = $params['bang_diem'] ?? $params['chung_chi'] ?? $params['du_an'] ?? null;

        if ($record) {
            // Kiểm tra is_locked (Bản khóa kỹ thuật khi đang giao dịch)
            if ($record->is_locked) {
                return response()->json([
                    'status' => false,
                    'message' => 'Dữ liệu đang được xử lý đúc NFT, vui lòng không chỉnh sửa.'
                ]);
            }

            // 2. Kiểm tra trang_thai (Trạng thái nghiệp vụ)
            // Nếu hồ sơ đã đúc NFT thành công, chỉ chặn XÓA. Vẫn cho PHÉP SỬA.
            if ($record->trang_thai === $record::STATUS_MINTED && $request->method() === 'DELETE') {
                return response()->json([
                    'status' => false,
                    'message' => 'Hồ sơ đã đúc NFT thành công, không thể xóa để đảm bảo tính toàn vẹn dữ liệu.'
                ]);
            }

            // Nếu hồ sơ đang chờ duyệt đúc NFT, vẫn cho phép sửa (sẽ tự động cập nhật yêu cầu) 
            // nhưng chặn xóa để tránh lỗi logic duyệt.
            if ($record->trang_thai === $record::STATUS_PENDING_MINT && $request->method() === 'DELETE') {
                return response()->json([
                    'status' => false,
                    'message' => 'Hồ sơ đang trong quá trình chờ duyệt đúc NFT, không thể xóa.'
                ]);
            }
        }

        return $next($request);
    }
}
