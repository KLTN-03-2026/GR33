# Hệ Thống Hồ Sơ Học Tập Số Phi Tập Trung - Task List

## 1. Thiết kế Cơ sở dữ liệu và Khởi tạo Models (Models & Migrations)
- [x] Xây dựng nhóm người dùng & phân quyền (RBAC) (`sinh_vien`, `nhan_vien`, `chuc_vu`, `chuc_nang`, `phan_quyen`)
- [x] Xây dựng nhóm dữ liệu đào tạo/học tập (`mon_hoc`, `lop_hoc`, `bang_diem`, `chung_chi`, `don_vi_cap`, `du_an`)
- [x] Xây dựng nhóm quản lý Blockchain/Web3 (`smart_contracts`, `nft_van_bangs`, `lich_su_giao_dichs`, `vi_sinh_viens`, `vi_nhan_viens`)
- [x] Thiết lập liên kết đa hình (Polymorphic Relation) cho bảng `nft_van_bangs`
- [x] Cập nhật toàn bộ Seeder khớp với kiến trúc bảo mật mới và tối ưu hóa để có thể chạy lại nhiều lần (idempotent).

## 2. Phát triển Backend APIs (Khối Off-chain)
- [x] **Hệ thống RBAC & Bảo mật**:
    - [x] Thiết lập 6 nhóm chức vụ cốt lõi (Super Admin -> Giảng viên).
    - [x] Triển khai Middleware `CheckPermission` hỗ trợ nhiều mã quyền cùng lúc.
    - [x] **Tối ưu hóa**: Chuyển toàn bộ logic kiểm tra quyền hệ thống vào `api.php`, loại bỏ code thủ công trong Controller.
- [x] **Refactor & Đồng bộ hóa Controller**:
    - [x] Đồng bộ hóa tên hàm: `getData...`, `create...`, `update...`, `delete...`, `getDetail...`.
    - [x] Đổi tên `ChucVuChucNangController` thành `PhanQuyenController`.
    - [x] Loại bỏ các chức năng CRUD không cần thiết khỏi `ChucNangController`.
    - [x] Đồng bộ hóa toàn bộ tên cột trạng thái về `trang_thai` (thay cho `tinh_trang`).
    - [x] Bổ sung trường `so_dien_thoai` cho bảng `nhan_viens`.
    - [x] Đồng bộ `so_dien_thoai` cho `sinh_viens` và chuẩn hóa validation (10 số, bắt đầu bằng 09).
    - [x] Bổ sung trường `dia_chi` cho cả `nhan_viens` và `sinh_viens`.
    - [x] Bổ sung số điện thoại và địa chỉ cho toàn bộ bản ghi nhân viên và sinh viên hiện có.
- [x] **Tính năng mở rộng**:
    - [x] Triển khai chức năng **Tìm kiếm** (`search...`) cho tất cả các module.
    - [x] Bổ sung chức năng **Quản lý Đơn vị cấp** (ID 14) cho Ban Giám hiệu.
    - [x] Chức năng Giảng dạy (ID 13) cho phép nhập điểm/duyệt dự án.
- [x] **Logic Nghiệp vụ**:
    - [x] Tính điểm tổng kết: 45% quá trình, 55% cuối kỳ.
    - [x] Cơ chế khóa dữ liệu (`is_locked`) và trạng thái (`trang_thai`).
- [x] **Tài liệu hóa**:
    - [x] Hệ thống Postman theo Module: Các file test riêng biệt trong thư mục `postman/` (ngoại trừ PhanQuyen).
    - [x] Webview API Documentation tại `/api/docs` (Scramble).
    - [x] Đã dọn dẹp các script và file test cũ không còn sử dụng.
- [x] Refactor toàn bộ Controller để sử dụng `Request $request` thay cho `\Illuminate\Http\Request $request`.

## 3. Tích hợp Blockchain & Cơ chế Khóa (Luồng Xử lý Dữ liệu)
- [ ] Xây dựng API "Yêu cầu cấp phát NFT": Cập nhật `is_locked = true` cho dữ liệu nguồn
- [ ] Cài đặt thuật toán Băm (Hashing) đóng gói dữ liệu tham chiếu
- [ ] Tích hợp cơ chế Ký số (Digital Signature) của người uỷ quyền trước khi gửi giao dịch
- [ ] Tích hợp thư viện Web3 (ethers.js/web3.js) để gửi giao dịch đúc NFT (Mint) đến Smart Contract
- [ ] Xây dựng Worker (Message Queue) lắng nghe trạng thái (Event Listener) từ Blockchain / node
- [ ] Hàm xử lý sau giao dịch: Nhận phản hồi, lưu `tx_hash_thanh_cong` vào `nft_van_bang`, ghi log `lich_su_giao_dich`, và mở khóa `is_locked = false`

## 4. Phát triển Smart Contract (Khối On-chain)
- [ ] Viết Smart Contract đúc NFT (chuẩn ERC-721 chuẩn hóa dữ liệu giáo dục)
- [ ] Viết hàm Mint xử lý Metadata (Token URI có thể dẫn tới IPFS) chứa thông tin bảo mật của hồ sơ
- [ ] Cấu hình kiểm soát quyền (Access Control) hợp lệ trên Contract (chỉ cho phép ví của trường đúc)
- [ ] Triển khai (Deploy) lện mạng Testnet và đồng bộ contract_address vào Database
