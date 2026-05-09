<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NftVanBang;
use App\Models\BangDiem;
use App\Models\ChungChi;
use App\Models\DuAn;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ThongKeController extends Controller
{
    public function layDuLieuThongKe()
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        // 1. Tổng hợp số lượng đã đúc thành công (trang_thai = 1) - TẤT CẢ THỜI GIAN
        $tong_bang_diem = NftVanBang::where('nftable_type', BangDiem::class)->where('trang_thai', 1)->count();
        $tong_chung_chi = NftVanBang::where('nftable_type', ChungChi::class)->where('trang_thai', 1)->count();
        $tong_du_an = NftVanBang::where('nftable_type', DuAn::class)->where('trang_thai', 1)->count();

        // 2. Thống kê số lượng đúc trong THÁNG NÀY (theo yêu cầu user)
        $thang_nay_bang_diem = NftVanBang::where('nftable_type', BangDiem::class)
            ->where('trang_thai', 1)
            ->whereBetween('updated_at', [$startOfMonth, $endOfMonth])
            ->count();
        $thang_nay_chung_chi = NftVanBang::where('nftable_type', ChungChi::class)
            ->where('trang_thai', 1)
            ->whereBetween('updated_at', [$startOfMonth, $endOfMonth])
            ->count();
        $thang_nay_du_an = NftVanBang::where('nftable_type', DuAn::class)
            ->where('trang_thai', 1)
            ->whereBetween('updated_at', [$startOfMonth, $endOfMonth])
            ->count();

        // 3. Thống kê theo 6 tháng gần nhất (Biểu đồ)
        $thong_ke_thang = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $s = $date->copy()->startOfMonth();
            $e = $date->copy()->endOfMonth();

            $count = NftVanBang::where('trang_thai', 1)
                ->whereBetween('updated_at', [$s, $e])
                ->count();

            $thong_ke_thang[] = [
                'label' => "Tháng " . $date->format('m/Y'),
                'count' => $count
            ];
        }

        // 4. Danh sách 10 bản ghi đúc gần nhất
        $gan_day = NftVanBang::where('trang_thai', 1)
            ->with(['nftable.sinhVien'])
            ->latest('updated_at')
            ->take(10)
            ->get()
            ->map(function($item) {
                $ho_ten = "Không rõ";
                $loai = "Hồ sơ";
                $ten_ho_so = "N/A";

                if ($item->nftable) {
                    $ho_ten = $item->nftable->sinhVien->ho_ten ?? "Không rõ";
                    if ($item->nftable instanceof BangDiem) {
                        $loai = "Bảng điểm";
                        $ten_ho_so = "Kết quả học tập";
                    } elseif ($item->nftable instanceof ChungChi) {
                        $loai = "Chứng chỉ";
                        $ten_ho_so = $item->nftable->ten_chung_chi;
                    } elseif ($item->nftable instanceof DuAn) {
                        $loai = "Dự án";
                        $ten_ho_so = $item->nftable->ten_du_an;
                    }
                }

                return [
                    'id'            => $item->id,
                    'token_id'      => $item->token_id,
                    'ho_ten'        => $ho_ten,
                    'loai'          => $loai,
                    'ten_ho_so'     => $ten_ho_so,
                    'ngay_duc'      => Carbon::parse($item->updated_at)->format('d/m/Y H:i'),
                    'tx_hash'       => $item->tx_hash_thanh_cong,
                ];
            });

        return response()->json([
            'status' => true,
            'data'   => [
                'tong_quat' => [
                    'bang_diem' => $tong_bang_diem,
                    'chung_chi' => $tong_chung_chi,
                    'du_an'      => $tong_du_an,
                    'tat_ca'    => $tong_bang_diem + $tong_chung_chi + $tong_du_an,
                ],
                'thang_nay' => [
                    'bang_diem' => $thang_nay_bang_diem,
                    'chung_chi' => $thang_nay_chung_chi,
                    'du_an'      => $thang_nay_du_an,
                    'tat_ca'    => $thang_nay_bang_diem + $thang_nay_chung_chi + $thang_nay_du_an,
                ],
                'thong_ke_thang' => $thong_ke_thang,
                'gan_day'        => $gan_day
            ]
        ]);
    }
}
