<?php

namespace App\Jobs;

use App\Models\NftVanBang;
use App\Models\LichSuGiaoDich;
use App\Models\ThongBao;
use App\Services\BlockchainService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;

class VerifyBlockchainTransaction implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $idVanBangNft;

    /**
     * Create a new job instance.
     */
    public function __construct($idVanBangNft)
    {
        $this->idVanBangNft = $idVanBangNft;
    }

    /**
     * Execute the job.
     */
    public function handle(BlockchainService $blockchainService): void
    {
        $nftVanBang = NftVanBang::find($this->idVanBangNft);
        
        if (!$nftVanBang || !$nftVanBang->tx_hash_thanh_cong) return;

        $hoaDonJson = $blockchainService->layHoaDonGiaoDich($nftVanBang->tx_hash_thanh_cong);

        if (empty($hoaDonJson)) {
            Log::warning("Chưa có hóa đơn cho giao dịch: " . $nftVanBang->tx_hash_thanh_cong);
            // Đẩy lại vào hàng chờ sau 10 giây để kiểm tra lại
            $this->release(10);
            return;
        }

        $duLieu = json_decode($hoaDonJson, true);
        
        // Sepolia/Foundry có thể trả về status là '0x1' hoặc '1' (dạng chuỗi hoặc số)
        $isThanhCong = isset($duLieu['status']) && ( (string)$duLieu['status'] === '0x1' || (string)$duLieu['status'] === '1' );
        $isThatBai = isset($duLieu['status']) && ( (string)$duLieu['status'] === '0x0' || (string)$duLieu['status'] === '0' );

        if ($isThanhCong) {
            // THÀNH CÔNG: Trích xuất Token ID từ logs (Ưu tiên log Transfer của ERC721)
            $maToken = null;
            if (isset($duLieu['logs'])) {
                foreach ($duLieu['logs'] as $log) {
                    // Sự kiện Transfer(address from, address to, uint256 tokenId) 
                    // có mã hash: 0xddf252ad1be2c89b69c2b068fc378daa952ba7f163c4a11628f55a4df523b3ef
                    if (isset($log['topics'][0]) && $log['topics'][0] === '0xddf252ad1be2c89b69c2b068fc378daa952ba7f163c4a11628f55a4df523b3ef') {
                        if (isset($log['topics'][3])) {
                            $maToken = hexdec($log['topics'][3]);
                            break;
                        }
                    }
                }
            }

            $nftVanBang->update([
                'trang_thai' => NftVanBang::STATUS_SUCCESS,
                'token_id' => $maToken,
                'token_uri' => url("/api/nft/metadata/" . $maToken)
            ]);
            
            // Cập nhật hoặc ghi mới lịch sử giao dịch thành công
            LichSuGiaoDich::updateOrCreate(
                ['transaction_hash' => $nftVanBang->tx_hash_thanh_cong],
                [
                    'nft_van_bang_id' => $nftVanBang->id,
                    'nguoi_thuc_hien_id' => $nftVanBang->nhan_vien_ky_id,
                    'hanh_dong' => 'MINT_SUCCESS',
                    'block_number' => isset($duLieu['blockNumber']) ? hexdec($duLieu['blockNumber']) : null,
                    'gas_used' => isset($duLieu['gasUsed']) ? hexdec($duLieu['gasUsed']) : null,
                    'gas_price' => isset($duLieu['effectiveGasPrice']) ? hexdec($duLieu['effectiveGasPrice']) : null,
                    'trang_thai' => 1,
                ]
            );

            // Cập nhật bảng nguồn (BangDiem, ChungChi, DuAn) sang MINTED
            $hoSoNguon = $nftVanBang->nftable;
            if ($hoSoNguon) {
                $hoSoNguon->update([
                    'trang_thai' => 1, // STATUS_MINTED
                    'is_locked'  => false
                ]);

                // Gửi thông báo cho Sinh viên khi đúc thành công
                $loai = class_basename($hoSoNguon);
                $loaiTen = $loai == 'BangDiem' ? 'Bảng điểm' : ($loai == 'ChungChi' ? 'Chứng chỉ' : 'Dự án');
                $link = $loai == 'BangDiem' ? '/sinh-vien/bang-diem' : ($loai == 'ChungChi' ? '/sinh-vien/chung-chi' : '/sinh-vien/du-an');

                ThongBao::create([
                    'sinh_vien_id' => $hoSoNguon->sinh_vien_id,
                    'tieu_de'      => 'Đúc NFT thành công!',
                    'noi_dung'     => 'Hồ sơ ' . $loaiTen . ' của bạn đã được đúc NFT thành công trên Blockchain. Token ID: ' . $maToken,
                    'link'         => $link,
                    'loai'         => 'success'
                ]);
            }
        } elseif ($isThatBai) {
            // THẤT BẠI
            $nftVanBang->update(['trang_thai' => NftVanBang::STATUS_FAILURE]);
            
            LichSuGiaoDich::updateOrCreate(
                ['transaction_hash' => $nftVanBang->tx_hash_thanh_cong],
                [
                    'nft_van_bang_id' => $nftVanBang->id,
                    'nguoi_thuc_hien_id' => $nftVanBang->nhan_vien_ky_id,
                    'hanh_dong' => 'MINT_FAILED',
                    'trang_thai' => 0,
                    'loi_chi_tiet' => 'Blockchain REVERT or Out of Gas on Sepolia.'
                ]
            );

            // Mở khóa bản ghi để có thể đúc lại
            $hoSoNguon = $nftVanBang->nftable;
            if ($hoSoNguon) {
                $hoSoNguon->update(['is_locked' => false]);

                // Gửi thông báo cho Sinh viên khi đúc thất bại
                $loai = class_basename($hoSoNguon);
                $loaiTen = $loai == 'BangDiem' ? 'Bảng điểm' : ($loai == 'ChungChi' ? 'Chứng chỉ' : 'Dự án');
                
                ThongBao::create([
                    'sinh_vien_id' => $hoSoNguon->sinh_vien_id,
                    'tieu_de'      => 'Đúc NFT thất bại!',
                    'noi_dung'     => 'Đã có lỗi xảy ra khi đúc NFT cho ' . $loaiTen . ' của bạn. Vui lòng liên hệ Admin.',
                    'link'         => '#',
                    'loai'         => 'danger'
                ]);
            }
        } else {
            // Nếu vẫn đang chờ (chưa có hóa đơn hoặc đang pending), đẩy lại vào hàng chờ sau 15 giây
            $this->release(15);
        }
    }
}
