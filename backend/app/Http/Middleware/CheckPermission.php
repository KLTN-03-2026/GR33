<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$ids)
    {
        // TẠM THỜI BỎ QUA KIỂM TRA QUYỀN ĐỂ TEST LOCAL THEO YÊU CẦU
        return $next($request);

        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'Bạn chưa đăng nhập.'
            ]);
        }

        // Kiểm tra quyền qua bảng phan_quyens (chấp nhận mảng IDs)
        $hasPermission = \App\Models\PhanQuyen::where('chuc_vu_id', $user->chuc_vu_id)
            ->whereIn('chuc_nang_id', $ids)
            ->exists();

        if (!$hasPermission) {
            return response()->json([
                'status'  => false,
                'message' => 'Bạn không có quyền thực hiện hành động này.'
            ]);
        }

        return $next($request);
    }
}
