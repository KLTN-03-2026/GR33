import { reactive } from 'vue';

// Khởi tạo store từ localStorage
const userInitial = JSON.parse(localStorage.getItem("nhan_vien_user") || "{}");

export const authStore = reactive({
    user: userInitial,
    
    // Hàm cập nhật dữ liệu user (được dùng bởi Profile hoặc Interceptor)
    updateUser(userData) {
        this.user = userData;
        localStorage.setItem("nhan_vien_user", JSON.stringify(userData));
    },
    
    // Hàm xóa dữ liệu khi logout
    clearUser() {
        this.user = {};
        localStorage.removeItem("nhan_vien_user");
        localStorage.removeItem("nhan_vien_token");
    },

    // Helper kiểm tra quyền nhanh
    hasPermission(permissionId) {
        if (!this.user || Object.keys(this.user).length === 0) return false;
        const roleId = Number(this.user.chuc_vu_id || this.user.chuc_vu?.id || this.user.chucVu?.id);
        if (roleId === 1) return true; // Super Admin có mọi quyền

        const listQuyens = (this.user.list_quyens || []).map(id => Number(id));
        const chucVuObj = this.user.chuc_vu || this.user.chucVu || {};
        const permissions = (chucVuObj.chuc_nangs || chucVuObj.chucNangs || []).map(p => Number(p.id));
        
        return listQuyens.includes(Number(permissionId)) || permissions.includes(Number(permissionId));
    }
});
