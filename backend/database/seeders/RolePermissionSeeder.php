<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Dọn dẹp dữ liệu cũ để tránh trùng lặp
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('phan_quyens')->truncate();
        DB::table('chuc_nangs')->truncate();
        DB::table('group_chuc_nangs')->truncate();
        DB::table('chuc_vus')->truncate();
        
        // Cố định mọi User hiện hữu về Super Admin để không bị mất quyền login sau khi seed
        DB::table('nhan_viens')->update(['chuc_vu_id' => 1]); 
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. Tạo Group Chức Năng (6 Nhóm chính)
        $groups = [
            ['id' => 1, 'ten_group' => 'Quản lý Hệ thống & Cấu hình', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'ten_group' => 'Quản lý Người dùng & Nhân sự', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'ten_group' => 'Quản lý Khoa & Đào tạo', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'ten_group' => 'Quản lý Hồ sơ Văn bằng', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'ten_group' => 'Quản lý Blockchain & Phê duyệt', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'ten_group' => 'Thống kê & Báo cáo', 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('group_chuc_nangs')->insert($groups);

        // 3. Tạo Chức Năng Chi Tiết (Map khớp với api.php)
        $permissions = [
            // Group 1: Hệ thống
            ['id' => 11, 'group_id' => 1, 'ten_chuc_nang' => 'Xem cấu hình Chức vụ'],
            ['id' => 12, 'group_id' => 1, 'ten_chuc_nang' => 'Thêm mới Chức vụ'],
            ['id' => 13, 'group_id' => 1, 'ten_chuc_nang' => 'Thao tác cấu hình Chức vụ (Sửa / Xóa)'],
            ['id' => 14, 'group_id' => 1, 'ten_chuc_nang' => 'Truy cập Phân quyền hệ thống'],

            // Group 2: Người dùng & Tổ chức
            ['id' => 21, 'group_id' => 2, 'ten_chuc_nang' => 'Xem danh sách Phòng ban'],
            ['id' => 22, 'group_id' => 2, 'ten_chuc_nang' => 'Thao tác danh sách Phòng ban (Thêm / Sửa / Xóa)'],
            ['id' => 23, 'group_id' => 2, 'ten_chuc_nang' => 'Xem danh sách Nhân sự'],
            ['id' => 24, 'group_id' => 2, 'ten_chuc_nang' => 'Thêm Nhân sự mới'],
            ['id' => 25, 'group_id' => 2, 'ten_chuc_nang' => 'Thao tác Nhân sự (Sửa / Xóa / Khóa)'],
            ['id' => 26, 'group_id' => 2, 'ten_chuc_nang' => 'Xem danh sách Sinh viên'],
            ['id' => 27, 'group_id' => 2, 'ten_chuc_nang' => 'Thao tác Sinh viên (Thêm / Sửa / Xóa)'],

            // Group 3: Khoa & Đào tạo
            ['id' => 31, 'group_id' => 3, 'ten_chuc_nang' => 'Xem danh mục Môn học'],
            ['id' => 32, 'group_id' => 3, 'ten_chuc_nang' => 'Thao tác danh mục Môn học (Thêm / Sửa / Xóa)'],
            ['id' => 33, 'group_id' => 3, 'ten_chuc_nang' => 'Xem danh mục Lớp học'],
            ['id' => 34, 'group_id' => 3, 'ten_chuc_nang' => 'Thao tác danh mục Lớp học (Thêm / Sửa / Xóa)'],
            ['id' => 35, 'group_id' => 3, 'ten_chuc_nang' => 'Xem danh mục Đơn vị cấp'],
            ['id' => 36, 'group_id' => 3, 'ten_chuc_nang' => 'Thao tác danh mục Đơn vị cấp (Thêm / Sửa / Xóa)'],

            // Group 4: Hồ sơ Văn bằng
            ['id' => 41, 'group_id' => 4, 'ten_chuc_nang' => 'Xem thông tin Bảng điểm / Chứng chỉ / Dự án'],
            ['id' => 42, 'group_id' => 4, 'ten_chuc_nang' => 'Nhập liệu mới Bảng điểm / Chứng chỉ / Dự án'],
            ['id' => 43, 'group_id' => 4, 'ten_chuc_nang' => 'Cập nhật, sửa đổi Bảng điểm'],
            ['id' => 44, 'group_id' => 4, 'ten_chuc_nang' => 'Xóa bỏ Bảng điểm'],
            ['id' => 45, 'group_id' => 4, 'ten_chuc_nang' => 'Cập nhật, sửa đổi Chứng chỉ / Dự án'],
            ['id' => 46, 'group_id' => 4, 'ten_chuc_nang' => 'Xóa bỏ Chứng chỉ / Dự án'],

            // Group 5: Blockchain & Phê duyệt
            ['id' => 51, 'group_id' => 5, 'ten_chuc_nang' => 'Xem danh sách Chờ ký duyệt & Danh sách NFT'],
            ['id' => 52, 'group_id' => 5, 'ten_chuc_nang' => 'Thực hiện Ký số & Đúc (Mint) NFT'],
            ['id' => 53, 'group_id' => 5, 'ten_chuc_nang' => 'Thu hồi (Revoke) NFT vi phạm'],
            ['id' => 54, 'group_id' => 5, 'ten_chuc_nang' => 'Phê duyệt dữ liệu hồ sơ mới (Dự án / Chứng chỉ)'],

            // Group 6: Báo cáo
            ['id' => 61, 'group_id' => 6, 'ten_chuc_nang' => 'Xem Tích hợp Báo cáo Dashboard'],
        ];

        // Format timestamps
        foreach ($permissions as &$p) {
            $p['created_at'] = now();
            $p['updated_at'] = now();
        }
        DB::table('chuc_nangs')->insert($permissions);

        // 4. Tạo 6 Chức vụ (Roles)
        $roles = [
            ['id' => 1, 'ten_chuc_vu' => 'Super Admin', 'trang_thai' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'ten_chuc_vu' => 'Ban Giám Hiệu', 'trang_thai' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'ten_chuc_vu' => 'Chuyên viên Đào tạo', 'trang_thai' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'ten_chuc_vu' => 'Chuyên viên Quản lý Người dùng', 'trang_thai' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'ten_chuc_vu' => 'Quản trị viên Blockchain', 'trang_thai' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'ten_chuc_vu' => 'Giảng viên', 'trang_thai' => 1, 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('chuc_vus')->insert($roles);

        // 5. Gắn quyền lợi tự động (Phân Quyền)
        // Lấy tất cả quyền để rải cho Super Admin
        $allPermissions = array_column($permissions, 'id');
        
        // Define quyền cho từng role
        $rolePermissionsCount = [
            1 => $allPermissions, // Super Admin: All
            2 => [11, 21, 23, 26, 31, 33, 35, 41, 51, 52, 53, 61], // Ban Giám Hiệu: Xem (mọi thứ) + Ký/Đúc/Thu hồi NFT
            3 => [26, 31, 32, 33, 34, 41, 42, 45, 46, 51, 54], // CV Đào Tạo: Quản lý học vụ, hồ sơ văn bằng...
            4 => [21, 22, 23, 24, 25, 26, 27, 35, 36], // CV Người Dùng: Quản lý Phòng Ban, Nhân viên, Sinh viên, Đơn vị cấp (Full)
            5 => [51, 52, 53], // Blockchain Admin: Xem yc, Ký đúc, Thu hồi.
            6 => [26, 33, 41, 43] // Giảng Viên: Xem ds, Lớp, Hồ sơ & Cập nhật điểm (Chỉ được sửa, không được thêm/xóa)
        ];

        $insertPivot = [];
        foreach ($rolePermissionsCount as $roleId => $perms) {
            foreach ($perms as $permId) {
                $insertPivot[] = [
                    'chuc_vu_id' => $roleId,
                    'chuc_nang_id' => $permId,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
        }
        DB::table('phan_quyens')->insert($insertPivot);
    }
}
