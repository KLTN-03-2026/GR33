<template>
  <header class="app-navbar" :class="{ 'sidebar-collapsed': sidebarCollapsed }">
    <button class="navbar-toggle" @click="$emit('toggle-sidebar')"><i class="bi bi-list"></i></button>
    <div class="navbar-spacer"></div>
    
    <!-- Notifications -->
    <div class="navbar-icon-btn position-relative" title="Thông báo" @click.stop="kich_hoat_thong_bao">
        <i class="bi bi-bell"></i>
        <span v-if="so_luong_chua_doc > 0 && !da_xem" class="notif-dot animate-pulse"></span>

        <!-- Dropdown Thông báo phong cách Admin -->
        <div v-if="hien_thong_bao" class="notif-dropdown shadow-lg transition-all" @click.stop>
            <div class="notif-header d-flex justify-content-between align-items-center">
                <div>
                    <span class="fw-800 fs-6 text-dark font-heading">Thông báo</span>
                    <span class="badge bg-pink-subtle text-pink-dark rounded-pill ms-2 fw-700">{{ so_luong_chua_doc }}</span>
                </div>
                <button v-if="so_luong_chua_doc > 0" @click="doc_tat_ca" class="btn btn-sm text-pink p-0 shadow-none border-0 fw-700">
                    Đọc tất cả
                </button>
            </div>

            <div class="notif-list custom-scrollbar">
                <div v-if="danh_sach.length === 0" class="text-center py-5 opacity-50 small">
                    <i class="bi bi-bell-slash d-block fs-2 mb-2"></i>
                    Không có thông báo mới
                </div>
                <div v-for="item in danh_sach" :key="item.id" 
                    class="notif-item" :class="{ 'not-read': item.is_read == 0 }"
                    @click="doc_thong_bao(item)">
                    <div class="notif-icon" :class="lay_icon_class(item.loai)">
                        <i class="bi" :class="lay_icon(item.loai)"></i>
                    </div>
                    <div class="notif-content text-start">
                        <div class="notif-text">
                            <div class="fw-800 text-dark mb-1 notification-title">{{ item.tieu_de }}</div>
                            <div class="notification-body">{{ item.noi_dung }}</div>
                        </div>
                        <div class="notif-time d-flex justify-content-between align-items-center mt-1">
                            <span>{{ dinh_dang_thoi_gian(item.created_at) }}</span>
                            <span v-if="item.is_read == 0" class="notif-unread-dot-v2"></span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Cài đặt -->
    <router-link to="/sinh-vien/setting" class="navbar-icon-btn ms-1 text-decoration-none" title="Cài đặt">
        <i class="bi bi-gear"></i>
    </router-link>

    <!-- Avatar -->
    <div class="navbar-avatar ms-2 shadow-sm">
        <img v-if="nguoi_dung.hinh_anh" :src="nguoi_dung.hinh_anh" class="navbar-avatar-img" alt="Avatar">
        <span v-else>{{ ten_viet_tat }}</span>
    </div>
  </header>
</template>

<script>
import baseRequestSinhVien from "../../../core/baseRequestSinhVien";

export default {
  name: 'NavbarSinhVien',
  props: { sidebarCollapsed: { type: Boolean, default: false } },
  emits: ['toggle-sidebar', 'toggle-customizer'],
  data() {
      return {
          hien_thong_bao: false,
          danh_sach: [],
          so_luong_chua_doc: 0,
          da_xem: false,
          nguoi_dung: {},
          bo_dem: null
      };
  },
  computed: {
      ten_viet_tat() {
          if (!this.nguoi_dung.ho_ten) return 'SV';
          const parts = this.nguoi_dung.ho_ten.split(' ');
          return parts[parts.length - 1].charAt(0).toUpperCase();
      }
  },
  mounted() {
      this.nguoi_dung = JSON.parse(localStorage.getItem('sinh_vien_user') || '{}');
      this.lay_thong_bao();
      
      // Lắng nghe sự kiện hồ sơ cập nhật
      window.addEventListener('student-profile-updated', this.cap_nhat_nguoi_dung);
      this.bo_dem = setInterval(this.lay_thong_bao, 30000);
      window.addEventListener('click', this.dong_dropdown);
  },
  beforeUnmount() {
      if (this.bo_dem) clearInterval(this.bo_dem);
      window.removeEventListener('click', this.dong_dropdown);
      window.removeEventListener('student-profile-updated', this.cap_nhat_nguoi_dung);
  },
  methods: {
      cap_nhat_nguoi_dung() {
          this.nguoi_dung = JSON.parse(localStorage.getItem('sinh_vien_user') || '{}');
      },
      kich_hoat_thong_bao() {
          this.hien_thong_bao = !this.hien_thong_bao;
          if (this.hien_thong_bao) this.da_xem = true;
      },
      dong_dropdown() {
          this.hien_thong_bao = false;
      },
      lay_thong_bao() {
          baseRequestSinhVien.get("thong-bao/get-new")
              .then(res => {
                  this.danh_sach = res.data.data || [];
                  this.so_luong_chua_doc = res.data.chua_doc || 0;
                  if (this.so_luong_chua_doc > 0) this.da_xem = false;
              });
      },
      lay_icon(loai) {
          const map = { 'success': 'bi-check-circle-fill', 'warning': 'bi-exclamation-triangle-fill', 'danger': 'bi-x-circle-fill' };
          return map[loai] || 'bi-info-circle-fill';
      },
      lay_icon_class(loai) {
          switch (loai) {
              case 'success': return 'bg-success-subtle text-success';
              case 'warning': return 'bg-warning-subtle text-warning';
              case 'danger':  return 'bg-danger-subtle text-danger';
              default:        return 'bg-info-subtle text-info';
          }
      },
      dinh_dang_thoi_gian(at) {
          if (!at) return 'Vừa xong';
          const date = new Date(at);
          return date.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' }) + ' ' + date.toLocaleDateString('vi-VN');
      },
      doc_thong_bao(item) {
          this.hien_thong_bao = false;
          if (item.is_read == 0) {
              baseRequestSinhVien.post("thong-bao/mark-read", { id: item.id })
                  .then(() => {
                      this.lay_thong_bao();
                  });
          }
          if (item.link) this.$router.push(item.link);
      },
      doc_tat_ca() {
          baseRequestSinhVien.post("thong-bao/read-all")
              .then(res => {
                  if (res.data.status) {
                      this.lay_thong_bao();
                      this.da_xem = true;
                      this.$toast.success("Đã đọc tất cả thông báo.");
                  }
              });
      }
  }
}
</script>

<style scoped>
/* Same styles */
.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: .5; }
}

.notif-dropdown {
    position: absolute; top: 110%; right: 0;
    width: 320px; background: white; border-radius: 16px;
    z-index: 1000; overflow: hidden;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1) !important;
}

.notif-header { padding: 16px; border-bottom: 1px solid rgba(0, 0, 0, 0.05); }
.notif-list { max-height: 380px; overflow-y: auto; }

.notif-item {
    display: flex; align-items: start; padding: 12px 16px;
    cursor: pointer; transition: all 0.2s;
    border-bottom: 1px solid rgba(0, 0, 0, 0.03);
}
.notif-item:hover { background: #fdf2f8; }
.notif-item.not-read { background: rgba(219, 39, 119, 0.03); }

.notif-icon {
    width: 40px; height: 40px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    margin-right: 12px; flex-shrink: 0; font-size: 1.1rem;
}

.notif-content { flex: 1; min-width: 0; }
.notif-text { font-size: 13px; line-height: 1.4; margin-bottom: 4px; }
.notification-title { font-size: 14px; }
.notification-body { 
    color: #64748b; 
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.notif-time { font-size: 11px; color: #94a3b8; }
.notif-unread-dot-v2 {
    width: 8px; height: 8px; background: #db2777;
    border-radius: 50%;
}


.bg-pink-subtle { background: rgba(219, 39, 119, 0.08); }
.text-pink-dark { color: #831843; }
.text-pink { color: #db2777; }

/* Status colors map to project standards */
.bg-success-subtle { background-color: #d1fae5 !important; }
.bg-warning-subtle { background-color: #fef3c7 !important; }
.bg-danger-subtle  { background-color: #fee2e2 !important; }
.bg-info-subtle    { background-color: #e0f2fe !important; }
.text-success { color: #059669 !important; }
.text-warning { color: #d97706 !important; }
.text-danger  { color: #dc2626 !important; }
.text-info    { color: #0284c7 !important; }

.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #fce7f3; border-radius: 10px; }

.notif-dot {
    position: absolute; top: 4px; right: 4px;
    width: 8px; height: 8px; background: #ef4444;
    border-radius: 50%; border: 1.5px solid white;
}

.fw-800 { font-weight: 800; }
.fw-700 { font-weight: 700; }
</style>
