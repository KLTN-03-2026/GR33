<template>
  <nav class="sidebar" :class="{ collapsed: sidebarCollapsed }">
    <div class="sidebar-brand">
      <div class="brand-icon"><i class="bi bi-mortarboard-fill"></i></div>
      <div class="brand-name-wrap">
        <div class="brand-name">Sinh Viên</div>
        <div class="brand-sub">Student Portal</div>
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
        <div class="user-role">Sinh Viên</div>
      </div>
      <button class="user-logout" title="Đăng xuất" @click="dang_xuat">
        <i class="bi bi-box-arrow-right"></i>
      </button>
    </div>
  </nav>
</template>

<script>
import baseRequestSinhVien from "../../../core/baseRequestSinhVien";

export default {
  name: 'SidebarSinhVien',
  props: {
    sidebarCollapsed: { type: Boolean, default: false }
  },
  data() {
    return {
      thong_tin_sinh_vien: {}
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
      const du_lieu = localStorage.getItem("sinh_vien_user");
      if (du_lieu) {
        this.thong_tin_sinh_vien = JSON.parse(du_lieu);
      }
    },
    dang_xuat() {
      baseRequestSinhVien
        .post("logout")
        .then((res) => {
          if (res.data.status) {
            localStorage.removeItem("sinh_vien_token");
            localStorage.removeItem("sinh_vien_user");
            this.$toast.success(res.data.message);
            this.$router.push("/sinh-vien/login");
          } else {
            this.$toast.error(res.data.message);
          }
        })
        .catch((err) => {
          this.$toast.error(err.response?.data?.message || "Có lỗi xảy ra khi gọi API đăng xuất!");
          localStorage.removeItem("sinh_vien_token");
          localStorage.removeItem("sinh_vien_user");
          this.$router.push("/sinh-vien/login");
        });
    }
  }
}
</script>
