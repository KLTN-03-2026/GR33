import { reactive } from 'vue';

// Khởi tạo store từ localStorage
const userInitial = JSON.parse(localStorage.getItem("sinh_vien_user") || "{}");

export const authStoreSinhVien = reactive({
    user: userInitial,
    
    // Hàm cập nhật dữ liệu sinh viên (được dùng bởi Profile hoặc Interceptor)
    updateUser(userData) {
        this.user = userData;
        localStorage.setItem("sinh_vien_user", JSON.stringify(userData));
    },
    
    // Hàm xóa dữ liệu khi logout
    clearUser() {
        this.user = {};
        localStorage.removeItem("sinh_vien_user");
        localStorage.removeItem("sinh_vien_token");
    },

    // Helper kiểm tra trạng thái nhanh
    isActive() {
        return Number(this.user.trang_thai) === 1;
    },

    isAlumni() {
        return Number(this.user.trang_thai) === 3;
    },

    isOnLeave() {
        return Number(this.user.trang_thai) === 2;
    },

    isDroppedOut() {
        return Number(this.user.trang_thai) === 0;
    }
});
