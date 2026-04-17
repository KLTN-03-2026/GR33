<template>
  <nav class="sidebar" :class="{ collapsed: sidebarCollapsed }">
    <!-- Brand -->
    <div class="sidebar-brand">
      <div class="brand-icon">
        <img src="/main_logo.jpg" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
      </div>
      <div class="brand-name-wrap">
        <div class="brand-name">DAR</div>
        <div class="brand-sub">Quản Trị Viên</div>
      </div>
    </div>

    <!-- Nav -->
    <div class="sidebar-nav">
      <!-- TỔNG QUAN -->
      <div class="menu-label">Tổng quan</div>
      <router-link class="menu-item" :class="{ active: $route.path === '/admin' }" to="/admin">
        <span class="menu-icon"><i class="bi bi-house-door"></i></span>
        <span class="menu-text">Trang chủ</span>
      </router-link>
      <router-link v-if="$hasPermission(61)" class="menu-item" :class="{ active: $route.path.includes('/admin/thong-ke') }" to="/admin/thong-ke">
        <span class="menu-icon"><i class="bi bi-bar-chart-line"></i></span>
        <span class="menu-text">Thống kê</span>
      </router-link>
      <router-link v-if="$hasPermission(51)" class="menu-item" :class="{ active: $route.path.includes('/admin/phe-duyet') }" to="/admin/phe-duyet">
        <span class="menu-icon"><i class="bi bi-check-all"></i></span>
        <span class="menu-text">Phê duyệt</span>
      </router-link>

      <!-- QUẢN LÝ NGƯỜI DÙNG -->
      <div v-if="$hasPermission(23) || $hasPermission(26) || $hasPermission(21)" class="menu-label" style="margin-top:8px">Quản lý người dùng</div>
      <router-link v-if="$hasPermission(23)" class="menu-item" :class="{ active: $route.path.includes('/admin/nhan-vien') }" to="/admin/nhan-vien">
        <span class="menu-icon"><i class="bi bi-people"></i></span>
        <span class="menu-text">Nhân viên</span>
      </router-link>
      <router-link v-if="$hasPermission(26)" class="menu-item" :class="{ active: $route.path.includes('/admin/sinh-vien') }" to="/admin/sinh-vien">
        <span class="menu-icon"><i class="bi bi-person-badge"></i></span>
        <span class="menu-text">Sinh viên</span>
      </router-link>
      <router-link v-if="$hasPermission(21)" class="menu-item" :class="{ active: $route.path.includes('/admin/phong-ban') }" to="/admin/phong-ban">
        <span class="menu-icon"><i class="bi bi-building"></i></span>
        <span class="menu-text">Phòng ban</span>
      </router-link>

      <!-- QUẢN LÝ ĐÀO TẠO -->
      <div v-if="$hasPermission(31) || $hasPermission(33) || $hasPermission(35) || $hasPermission(41)" class="menu-label" style="margin-top:8px">Quản lý đào tạo</div>
      <router-link v-if="$hasPermission(31)" class="menu-item" :class="{ active: $route.path.includes('/admin/mon-hoc') }" to="/admin/mon-hoc">
        <span class="menu-icon"><i class="bi bi-journal-bookmark"></i></span>
        <span class="menu-text">Môn học</span>
      </router-link>
      <router-link v-if="$hasPermission(33)" class="menu-item" :class="{ active: $route.path.includes('/admin/lop-hoc') }" to="/admin/lop-hoc">
        <span class="menu-icon"><i class="bi bi-mortarboard"></i></span>
        <span class="menu-text">Lớp học</span>
      </router-link>
      <router-link v-if="$hasPermission(35)" class="menu-item" :class="{ active: $route.path.includes('/admin/don-vi-cap') }" to="/admin/don-vi-cap">
        <span class="menu-icon"><i class="bi bi-patch-check"></i></span>
        <span class="menu-text">Đơn vị cấp</span>
      </router-link>
      <router-link v-if="$hasPermission(41)" class="menu-item" :class="{ active: $route.path.includes('/admin/chung-chi') }" to="/admin/chung-chi">
        <span class="menu-icon"><i class="bi bi-journal-check"></i></span>
        <span class="menu-text">Chứng chỉ</span>
      </router-link>
      <router-link v-if="$hasPermission(41)" class="menu-item" :class="{ active: $route.path.includes('/admin/du-an') }" to="/admin/du-an">
        <span class="menu-icon"><i class="bi bi-folder"></i></span>
        <span class="menu-text">Dự án</span>
      </router-link>
      <router-link v-if="$hasPermission(41)" class="menu-item" :class="{ active: $route.path.includes('/admin/bang-diem') }" to="/admin/bang-diem">
        <span class="menu-icon"><i class="bi bi-clipboard-data"></i></span>
        <span class="menu-text">Bảng điểm</span>
      </router-link>
      
      <!-- BLOCKCHAIN -->
      <div v-if="$hasPermission(51)" class="menu-label" style="margin-top:8px">Blockchain</div>
      <router-link v-if="$hasPermission(51)" class="menu-item" :class="{ active: $route.path.includes('/admin/nft') }" to="/admin/nft">
        <span class="menu-icon"><i class="bi bi-patch-check-fill"></i></span>
        <span class="menu-text">Quản lý NFT</span>
      </router-link>

      <!-- HỆ THỐNG -->
      <div v-if="$hasPermission(11)" class="menu-label" style="margin-top:8px">Hệ thống</div>
      <router-link v-if="$hasPermission(11)" class="menu-item" :class="{ active: $route.path.includes('/admin/phan-quyen') }" to="/admin/phan-quyen">
        <span class="menu-icon"><i class="bi bi-shield-lock"></i></span>
        <span class="menu-text">Chức Vụ & Phân quyền</span>
      </router-link>
    </div>

    <!-- User -->
    <div class="sidebar-user">
      <div class="user-avatar shadow-sm">
        <img v-if="thong_tin_nhan_vien.hinh_anh" :src="thong_tin_nhan_vien.hinh_anh" alt="Avatar" />
        <i v-else class="bi bi-person-fill text-rose"></i>
      </div>
      <div class="user-info">
        <div class="user-name">{{ thong_tin_nhan_vien.ho_ten || 'Đang tải...' }}</div>
        <div class="user-role">{{ thong_tin_nhan_vien.chuc_vu?.ten_chuc_vu || 'Nhân viên' }}</div>
      </div>
      <button class="user-logout" title="Đăng xuất" @click="dang_xuat">
        <i class="bi bi-box-arrow-right"></i>
      </button>
    </div>
  </nav>
</template>

<script>
import baseRequestAdmin from "../../../core/baseRequestAdmin";
import { authStore } from "../../../core/authStore";

export default {
  name: 'SidebarAdmin',
  props: {
    sidebarCollapsed: { type: Boolean, default: false }
  },
  data() {
    return {
      // Dùng authStore thay cho data riêng để có tính reactive toàn cục
    }
  },
  computed: {
    thong_tin_nhan_vien() {
      return authStore.user;
    }
  },
  created() {
    this.layThongTin();
  },
  methods: {
    layThongTin() {
      // 1. Dữ liệu ban đầu đã có sẵn trong authStore (hàm khởi tạo của store)
      
      // 2. Chủ động gọi API để cập nhật dữ liệu mới nhất từ DIGITALOCEAN
      baseRequestAdmin.get("profile")
        .then(res => {
          if (res.data.status) {
            // Cập nhật store trung tâm, đồng thời tự động cập nhật localStorage
            authStore.updateUser(res.data.data);
          }
        })
        .catch(err => {
          console.error("Lỗi đồng bộ hồ sơ nhân viên:", err);
        });
    },
    layKyTuDau(ten) {
      if (!ten) return "??";
      const words = ten.trim().split(" ");
      if (words.length === 1) return words[0].charAt(0).toUpperCase();
      return (words[0].charAt(0) + words[words.length - 1].charAt(0)).toUpperCase();
    },
    dang_xuat() {
      baseRequestAdmin
        .post("logout")
        .then((res) => {
          if (res.data.status) {
            // Dùng hàm clear sạch sẽ của store
            authStore.clearUser();
            this.$toast.success(res.data.message);
            this.$router.push("/admin/login");
          } else {
            this.$toast.error(res.data.message);
          }
        })
        .catch((err) => {
          this.$toast.error(err.response?.data?.message || "Có lỗi xảy ra khi gọi API đăng xuất!");
          authStore.clearUser();
          this.$router.push("/admin/login");
        });
    }
  }
}
</script>
