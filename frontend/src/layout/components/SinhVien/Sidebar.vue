<template>
  <nav class="sidebar" :class="{ collapsed: sidebarCollapsed }">
    <div class="sidebar-brand">
      <div class="brand-icon">
        <img src="/main_logo.jpg" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
      </div>
      <div class="brand-name-wrap">
        <div class="brand-name">DAR</div>
        <div class="brand-sub">sinh viên</div>
      </div>
    </div>

    <div class="sidebar-nav">
      <div class="menu-label">Cá nhân</div>
      <router-link class="menu-item" :class="{ active: $route.path === '/sinh-vien' }" to="/sinh-vien">
        <span class="menu-icon"><i class="bi bi-speedometer2"></i></span>
        <span class="menu-text">Giới Thiệu</span>
      </router-link>
      <router-link class="menu-item" to="/sinh-vien/ket-qua">
        <span class="menu-icon"><i class="bi bi-journal-check"></i></span>
        <span class="menu-text">Kết quả học tập</span>
      </router-link>
      <router-link class="menu-item" to="/sinh-vien/mon-hoc">
        <span class="menu-icon"><i class="bi bi-book"></i></span>
        <span class="menu-text">Tra cứu môn học</span>
      </router-link>
      <router-link class="menu-item" to="/sinh-vien/lop-hoc">
        <span class="menu-icon"><i class="bi bi-calendar-event"></i></span>
        <span class="menu-text">Đăng ký lớp học</span>
      </router-link>
      <router-link class="menu-item" to="/sinh-vien/chung-chi">
        <span class="menu-icon"><i class="bi bi-patch-check"></i></span>
        <span class="menu-text">Chứng chỉ của tôi</span>
      </router-link>
      <router-link class="menu-item" to="/sinh-vien/du-an">
        <span class="menu-icon"><i class="bi bi-folder-check"></i></span>
        <span class="menu-text">Dự án cá nhân</span>
      </router-link>
    </div>

    <div class="sidebar-user">
      <div class="user-avatar shadow-sm">
        <img :src="thong_tin_sinh_vien.hinh_anh" alt="Avatar" />
      </div>
      <div class="user-info">
        <div class="user-name">{{ thong_tin_sinh_vien.ho_ten || 'Đang tải...' }}</div>
        <div class="user-role-wrap d-flex align-items-center gap-1">
            <span class="user-role">Sinh Viên</span>
            <span v-if="thong_tin_sinh_vien.trang_thai == 1" class="badge-mini bg-success" title="Đang theo học"></span>
            <span v-else-if="thong_tin_sinh_vien.trang_thai == 2" class="badge-mini bg-warning" title="Đang Bảo lưu"></span>
            <span v-else-if="thong_tin_sinh_vien.trang_thai == 3" class="badge-mini bg-info" title="Đã Tốt nghiệp"></span>
            <span v-else class="badge-mini bg-danger" title="Đã Đình chỉ / Nghỉ học"></span>
        </div>
      </div>
      <button class="user-logout" title="Đăng xuất" @click="dang_xuat">
        <i class="bi bi-box-arrow-right"></i>
      </button>
    </div>
  </nav>
</template>

<script>
import baseRequestSinhVien from "../../../core/baseRequestSinhVien";
import { authStoreSinhVien } from "../../../core/authStoreSinhVien";

export default {
  name: 'SidebarSinhVien',
  props: {
    sidebarCollapsed: { type: Boolean, default: false }
  },
  data() {
    return {
      // Dùng authStoreSinhVien reactive
    }
  },
  computed: {
    thong_tin_sinh_vien() {
      return authStoreSinhVien.user;
    }
  },
  created() {
    this.layThongTin();
    // Lắng nghe sự kiện cập nhật hồ sơ nếu có
    window.addEventListener('student-profile-updated', this.layThongTin);
  },
  beforeUnmount() {
    window.removeEventListener('student-profile-updated', this.layThongTin);
  },
  methods: {
    layThongTin() {
      // 1. Store đã tự khởi tạo từ localStorage
      
      // 2. Chủ động làm mới dữ liệu từ server
      baseRequestSinhVien.get("profile")
        .then(res => {
            if (res.data.status) {
                authStoreSinhVien.updateUser(res.data.data);
            }
        })
        .catch(err => {
            console.error("Lỗi đồng bộ Sidebar Sinh viên:", err);
        });
    },
    dang_xuat() {
      baseRequestSinhVien
        .post("logout")
        .then((res) => {
          if (res.data.status) {
            authStoreSinhVien.clearUser();
            this.$toast.success(res.data.message);
            this.$router.push("/sinh-vien/login");
          } else {
            this.$toast.error(res.data.message);
          }
        })
        .catch((err) => {
          this.$toast.error(err.response?.data?.message || "Có lỗi xảy ra khi gọi API đăng xuất!");
          authStoreSinhVien.clearUser();
          this.$router.push("/sinh-vien/login");
        });
    }
  }
}
</script>
<style scoped>
.badge-mini {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
    border: 1px solid rgba(255,255,255,0.2);
}
</style>
