<?php

namespace App\Services;

use Web3\Web3;
use Web3\Contract;
use Web3\Providers\HttpProvider;
use Web3\RequestManagers\HttpRequestManager;
use Web3p\EthereumUtil\Util;

class BlockchainService
{
    protected $web3;
    protected $contract;
    protected $duongDanRpc;
    protected $diaChiHopDong;
    protected $khoaBaoMat;
    protected $duongDanCast;

    public function __construct()
    {
        $this->duongDanRpc = config('blockchain.rpc_url', env('BLOCKCHAIN_RPC_URL', 'http://127.0.0.1:8545'));
        $this->khoaBaoMat = config('blockchain.private_key', env('BLOCKCHAIN_PRIVATE_KEY'));
        $this->diaChiHopDong = config('blockchain.contract_address', env('BLOCKCHAIN_CONTRACT_ADDRESS'));
        
        // Cơ chế nhận diện tự động cast path
        $this->duongDanCast = $this->timDuongDanCast();
    }

    private function timDuongDanCast()
    {
        // 1. Ưu tiên cấu hình thủ công trong .env
        $envPath = env('BLOCKCHAIN_CAST_PATH');
        if ($envPath && file_exists($envPath)) return $envPath;

        // 2. Tìm trong thư mục cục bộ của project (vừa cài qua composer install)
        $localPath = base_path('.foundry/bin/cast');
        if (file_exists($localPath)) return $localPath;

        // 3. Fallback cho máy Mac cá nhân (PONG)
        if (file_exists('/Users/pong/.foundry/bin/cast')) return '/Users/pong/.foundry/bin/cast';

        // 4. Fallback cho máy chủ DigitalOcean
        if (file_exists('/workspace/backend/.foundry/bin/cast')) return '/workspace/backend/.foundry/bin/cast';

        // 5. Mặc định dùng lệnh toàn cục
        return 'cast';
    }

    /**
     * Gửi giao dịch đúc NFT lên Blockchain kèm theo Metadata JSON
     */
    public function ducNftOnChain($diaChiNhan, $chuKy, $maHash, $duongDanToken, $idNhanVien, $metadata)
    {
        $diaChiHopDong = env('BLOCKCHAIN_CONTRACT_ADDRESS');
        $duongDanRpc = env('BLOCKCHAIN_RPC_URL');
        $duongDanCast = $this->duongDanCast;

        // Đảm bảo có prefix 0x cho các kiểu dữ liệu bytes/bytes32
        if (strpos($maHash, '0x') !== 0) $maHash = '0x' . $maHash;
        if (strpos($chuKy, '0x') !== 0) $chuKy = '0x' . $chuKy;
        
        // Escape quotes trong metadata JSON để truyền vào command line
        $metadataEscaped = str_replace('"', '\"', $metadata);

        // Command to call safeMint(address, bytes32, string, bytes, uint256, string)
        $command = "{$duongDanCast} send {$diaChiHopDong} \"safeMint(address,bytes32,string,bytes,uint256,string)\" {$diaChiNhan} {$maHash} \"{$duongDanToken}\" {$chuKy} {$idNhanVien} \"{$metadataEscaped}\" --rpc-url {$duongDanRpc} --private-key " . env('BLOCKCHAIN_PRIVATE_KEY') . " --json";

        $output = shell_exec($command . " 2>&1");
        $result = json_decode($output, true);

        if (isset($result['transactionHash'])) {
            return [
                'transaction_hash' => $result['transactionHash']
            ];
        }

        throw new \Exception("Lỗi Blockchain: " . ($output ?: 'Không có phản hồi từ cast'));
    }

    /**
     * Lấy Token ID tiếp theo sẽ được đúc từ Blockchain
     */
    public function layTokenIdTiepTheo()
    {
        $diaChiHopDong = env('BLOCKCHAIN_CONTRACT_ADDRESS');
        $duongDanRpc = env('BLOCKCHAIN_RPC_URL');
        $duongDanCast = $this->duongDanCast;

        $command = "{$duongDanCast} call {$diaChiHopDong} \"_nextTokenId()(uint256)\" --rpc-url {$duongDanRpc}";
        $output = shell_exec($command . " 2>&1");

        // cast trả về hex: 0x0000...0001
        return hexdec(trim($output));
    }

    /**
     * Kiểm tra trạng thái của một giao dịch (Trả về JSON thô để Job xử lý)
     */
    public function layHoaDonGiaoDich($maGiaoDich)
    {
        $duongDanRpc = env('BLOCKCHAIN_RPC_URL');
        $duongDanCast = $this->duongDanCast;

        $command = "{$duongDanCast} receipt {$maGiaoDich} --rpc-url {$duongDanRpc} --json";
        $output = shell_exec($command . " 2>&1");
        
        return $output;
    }

    public function layMetadataOnChain($tokenId)
    {
        // Ưu tiên đọc từ Cache/Env
        $diaChiHopDong = env('BLOCKCHAIN_CONTRACT_ADDRESS', '0x31cca7fcfd8ca2605bba23d580b7e0e569bd551f');
        $duongDanRpc = env('BLOCKCHAIN_RPC_URL', 'https://sepolia.infura.io/v3/53329c7a325a4569b250b25a2d01f81a');
        
        $duongDanCast = $this->duongDanCast;
        $command = "{$duongDanCast} call {$diaChiHopDong} \"tokenMetadata(uint256)(string)\" {$tokenId} --rpc-url {$duongDanRpc}";
        $output = shell_exec($command . " 2>&1");
        
        // cast trả về chuỗi đại diện (Ví dụ: "{\"key\":\"value\"}")
        // Dùng stripslashes hoặc json_decode hai lần để bóc tách escape character
        $cleaned = trim($output, " \n\r\t");
        
        // Nếu cast trả về "..." thì decode lần 1 để mất dấu ngoặc kép bọc ngoài và slashes
        $decodedString = json_decode($cleaned);
        
        // Nếu giải mã JSON string thành công, trả về string đó, nếu không, dùng chuỗi đã loại bỏ slash
        if (is_string($decodedString)) {
            return $decodedString;
        }

        // Dự phòng: chỉ bỏ backslash và quote
        return trim(stripslashes($cleaned), " \"");
    }

    /**
     * Tiêu hủy NFT trên Blockchain (Burn)
     */
    public function caLenhBurnNft($tokenId)
    {
        $diaChiHopDong = env('BLOCKCHAIN_CONTRACT_ADDRESS', '0x31cca7fcfd8ca2605bba23d580b7e0e569bd551f');
        $duongDanRpc = env('BLOCKCHAIN_RPC_URL', 'https://sepolia.infura.io/v3/53329c7a325a4569b250b25a2d01f81a');
        
        $duongDanCast = $this->duongDanCast;

        $command = "{$duongDanCast} send {$diaChiHopDong} \"burn(uint256)\" {$tokenId} --rpc-url {$duongDanRpc} --private-key " . env('BLOCKCHAIN_PRIVATE_KEY') . " --json";

        $output = shell_exec($command . " 2>&1");
        $result = json_decode($output, true);

        if (isset($result['transactionHash'])) {
            return [
                'transaction_hash' => $result['transactionHash']
            ];
        }

        throw new \Exception("Lỗi Blockchain Burn: " . ($output ?: 'Không có phản hồi từ cast'));
    }
}
