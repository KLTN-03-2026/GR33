// Sử dụng ethers từ CDN (đã nhúng ở index.html)
const ethers = window.ethers;

class Web3Service {
    constructor() {
        this.provider = null;
        this.signer = null;
    }

    /**
     * Khởi tạo provider nếu MetaMask tồn tại
     */
    khoi_tao() {
        return new Promise((resolve, reject) => {
            if (!window.ethereum) {
                reject(new Error("Vui lòng cài đặt MetaMask hoặc trình duyệt Web3 để thực hiện ký số!"));
                return;
            }
            if (!this.provider) {
                this.provider = new ethers.BrowserProvider(window.ethereum);
            }
            resolve(this.provider);
        });
    }

    /**
     * Lấy địa chỉ ví đang kết nối (Tự động lấy ví đã cấp quyền)
     */
    lay_tai_khoan_ket_noi() {
        return this.khoi_tao()
            .then(provider => {
                return provider.send("eth_requestAccounts", []);
            })
            .then(accounts => {
                return accounts[0];
            })
            .catch(err => {
                console.error("Lỗi lấy tài khoản:", err);
                throw err;
            });
    }

    /**
     * Ép MetaMask hiển thị bảng chọn tài khoản (Account Selector)
     * Để người dùng có thể chọn lại ví khác hoặc nhấn Hủy
     */
    yeu_cau_quyen_chon_tai_khoan() {
        return this.khoi_tao()
            .then(provider => {
                // Ép hiện bảng chọn bằng wallet_requestPermissions
                return provider.send("wallet_requestPermissions", [{ eth_accounts: {} }]);
            })
            .then(() => {
                // Sau khi chọn xong, gọi lại hàm lấy tài khoản để cập nhật địa chỉ mới
                return this.lay_tai_khoan_ket_noi();
            })
            .catch(err => {
                console.error("Lỗi yêu cầu quyền chọn tài khoản:", err);
                throw err;
            });
    }

    /**
     * Ký một mã băm (Hash) bằng ví
     * @param {string} ma_bam Mã băm cần ký (Dạng Hex)
     * @returns {Promise<string>} Chữ ký số thu được
     */
    ky_so(ma_bam) {
        return this.khoi_tao()
            .then(provider => {
                return provider.getSigner();
            })
            .then(signer => {
                this.signer = signer;
                const ma_bam_sach = (ma_bam || "").toString().trim();
                const ma_bam_dinh_dang = ma_bam_sach.startsWith('0x') ? ma_bam_sach : '0x' + ma_bam_sach;
                
                return this.signer.signMessage(ethers.getBytes(ma_bam_dinh_dang));
            })
            .catch(err => {
                console.error("Lỗi ký số:", err);
                throw err;
            });
    }

    /**
     * Đổi mạng (nếu cần thiết trong tương lai)
     */
    chuyen_mang(id_chuoi) {
        if (!window.ethereum) return Promise.reject("MetaMask chưa cài đặt");

        return window.ethereum.request({
            method: 'wallet_switchEthereumChain',
            params: [{ chainId: id_chuoi }],
        }).catch(err => {
            console.error("Lỗi chuyển mạng:", err);
            throw err;
        });
    }

    /**
     * Lắng nghe sự kiện thay đổi tài khoản hoặc ngắt kết nối từ MetaMask
     * @param {function} callback Hàm xử lý khi tài khoản thay đổi
     */
    lang_nghe_su_kien_vi(callback) {
        if (window.ethereum) {
            window.ethereum.on('accountsChanged', callback);
        }
    }

    /**
     * Hủy lắng nghe sự kiện khi component bị tiêu hủy
     * @param {function} callback Hàm đã đăng ký trước đó
     */
    huy_lang_nghe(callback) {
        if (window.ethereum && window.ethereum.removeListener) {
            window.ethereum.removeListener('accountsChanged', callback);
        }
    }
}

export default new Web3Service();
