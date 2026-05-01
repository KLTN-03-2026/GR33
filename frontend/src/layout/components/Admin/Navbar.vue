<template>
    <header class="app-navbar" :class="{ 'sidebar-collapsed': sidebarCollapsed }">
        <!-- Toggle sidebar -->
        <button class="navbar-toggle" @click="$emit('toggle-sidebar')" title="Mở/Đóng Sidebar">
            <i class="bi bi-list"></i>
        </button>


        <div class="navbar-spacer"></div>

        <!-- Notifications -->
        <div class="navbar-icon-btn position-relative" title="Thông báo" @click.stop="toggleThongBao">
            <i class="bi bi-bell"></i>
            <span v-if="tongThongBao > 0 && !daXem" class="notif-dot animate-pulse"></span>

            <!-- Dropdown Thông báo -->
            <div v-if="hienThongBao" class="notif-dropdown shadow-lg transition-all" @click.stop>
                <div class="notif-header d-flex justify-content-between align-items-center">
                    <div>
                        <span class="fw-800 fs-6">Thông báo</span>
                        <span class="badge bg-rose-subtle text-rose-dark rounded-pill ms-2">{{ tongThongBao }}</span>
                    </div>
                    <button v-if="tongThongBao > 0" @click="docTatCa" class="btn btn-sm text-primary p-0 shadow-none border-0">
                        Đọc tất cả
                    </button>
                </div>

                <div class="notif-list custom-scrollbar">
                    <div v-if="danhSachThongBao.length === 0" class="text-center py-4 opacity-50 small">
                        Không có thông báo mới
                    </div>
                    <div v-for="tb in danhSachThongBao" :key="tb.id" 
                        class="notif-item" :class="{ 'not-read': tb.is_read == 0 }"
                        @click="docThongBao(tb)">
                        <div class="notif-icon" :class="getIconClass(tb.loai)">
                            <i :class="getIcon(tb.loai)"></i>
                        </div>
                        <div class="notif-content">
                            <div class="notif-text">
                                <div class="fw-bold mb-1">{{ tb.tieu_de }}</div>
                                {{ tb.noi_dung }}
                            </div>
                            <div class="notif-time d-flex justify-content-between">
                                <span>{{ formattedTime(tb.created_at) }}</span>
                                <span v-if="tb.is_read == 0" class="notif-unread-dot"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <router-link to="/admin/phe-duyet" class="notif-footer" @click="hienThongBao = false">
                    Xem tất cả yêu cầu <i class="bi bi-arrow-right ms-1"></i>
                </router-link>
            </div>
        </div>

        <router-link to="/admin/setting" class="navbar-icon-btn text-decoration-none" title="Cài đặt">
            <i class="bi bi-gear"></i>
        </router-link>

        <div class="navbar-avatar" title="Tài khoản">
            <img v-if="user.hinh_anh" :src="user.hinh_anh" class="navbar-avatar-img" alt="Avatar">
            <span v-else>AD</span>
        </div>
    </header>
</template>

<script>
import baseRequestAdmin from "../../../core/baseRequestAdmin";

export default {
    name: 'NavbarAdmin',
    props: {
        sidebarCollapsed: { type: Boolean, default: false }
    },
    emits: ['toggle-sidebar'],
    data() {
        return {
            hienThongBao: false,
            danhSachThongBao: [],
            tongThongBao: 0,
            daXem: false, // Thêm trạng thái đã xem
            boDem: null,
            user: {}
        }
    },
    mounted() {
        this.user = JSON.parse(localStorage.getItem('nhan_vien_user') || '{}');
        this.layThongBao();
        
        // Lắng nghe sự kiện hồ sơ cập nhật
        window.addEventListener('admin-profile-updated', this.updateUser);
        // Tự động load mỗi 30s (30000ms)
        this.boDem = setInterval(() => {
            this.layThongBao();
        }, 30000);

        // Đóng dropdown khi click ngoài
        window.addEventListener('click', this.dongDropdown);
    },
    beforeUnmount() {
        if (this.boDem) clearInterval(this.boDem);
        window.removeEventListener('click', this.dongDropdown);
        window.removeEventListener('admin-profile-updated', this.updateUser);
    },
    methods: {
        updateUser() {
            this.user = JSON.parse(localStorage.getItem('nhan_vien_user') || '{}');
        },
        toggleThongBao() {
            this.hienThongBao = !this.hienThongBao;
            if (this.hienThongBao) {
                this.daXem = true; // Đánh dấu đã xem khi mở menu
            }
        },
        dongDropdown() {
            this.hienThongBao = false;
        },
        layThongBao() {
            baseRequestAdmin.get("thong-bao/get-new")
                .then(res => {
                    if (res.data.status) {
                        this.danhSachThongBao = res.data.data;
                        this.tongThongBao = res.data.chua_doc;
                        // Reset daXem nếu có thông báo mới (số lượng chưa đọc > 0 và khác với trạng thái cũ)
                        if (this.tongThongBao > 0) {
                            this.daXem = false;
                        }
                    }
                })
                .catch(err => {
                    console.error("Lỗi lấy thông báo:", err);
                });
        },
        formattedTime(time) {
            if (!time) return "Vừa xong";
            const date = new Date(time);
            return date.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' }) + ' ' + date.toLocaleDateString('vi-VN');
        },
        getIcon(loai) {
            switch (loai) {
                case 'success': return 'bi bi-check-circle-fill';
                case 'warning': return 'bi bi-exclamation-triangle-fill';
                case 'danger':  return 'bi bi-x-circle-fill';
                default:        return 'bi bi-info-circle-fill';
            }
        },
        getIconClass(loai) {
            switch (loai) {
                case 'success': return 'bg-success-subtle text-success';
                case 'warning': return 'bg-warning-subtle text-warning';
                case 'danger':  return 'bg-danger-subtle text-danger';
                default:        return 'bg-info-subtle text-info';
            }
        },
        docThongBao(tb) {
            this.hienThongBao = false;
            if (tb.is_read == 0) {
                baseRequestAdmin.post("thong-bao/mark-read", { id: tb.id })
                    .then(() => {
                        this.layThongBao();
                    });
            }
            if (tb.link && tb.link !== '#') {
                this.$router.push(tb.link);
            }
        },
        docTatCa() {
            baseRequestAdmin.post("thong-bao/read-all")
                .then(res => {
                    if (res.data.status) {
                        this.layThongBao();
                        this.daXem = true;
                    }
                });
        }
    }
}
</script>

<style scoped>
.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {

    0%,
    100% {
        opacity: 1;
    }

    50% {
        opacity: .5;
    }
}

.notif-dropdown {
    position: absolute;
    top: 100%;
    right: 0;
    width: 320px;
    background: white;
    border-radius: 16px;
    margin-top: 10px;
    z-index: 1000;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1) !important;
}

.notif-header {
    padding: 16px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.notif-list {
    max-height: 380px;
    overflow-y: auto;
}

.notif-item {
    display: flex;
    align-items: center;
    padding: 12px 16px;
    text-decoration: none;
    color: inherit;
    transition: all 0.2s;
    border-bottom: 1px solid rgba(0, 0, 0, 0.03);
}

.notif-item:hover {
    background: #f8f9fa;
}

.notif-item.not-read {
    background: rgba(59, 130, 246, 0.05);
    cursor: pointer;
}

.navbar-avatar {
  width: 36px; height: 36px;
  border-radius: 50%;
  background: var(--primary);
  display: flex; align-items: center; justify-content: center;
  font-size: 12px; font-weight: 700;
  color: var(--primary-text);
  cursor: pointer;
  border: 2px solid var(--primary-dark);
  overflow: hidden;
}

.navbar-avatar-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.notif-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
    flex-shrink: 0;
    font-size: 1.1rem;
}

.notif-content {
    flex: 1;
    min-width: 0;
}

.notif-text {
    font-size: 13px;
    line-height: 1.4;
    margin-bottom: 4px;
}

.notif-time {
    font-size: 11px;
    color: #999;
}

.notif-unread-dot {
    width: 8px;
    height: 8px;
    background: #3b82f6;
    border-radius: 50%;
    margin-top: 4px;
}

.notif-footer {
    display: block;
    padding: 12px;
    text-align: center;
    background: #fdfdfd;
    color: var(--primary-darker);
    font-weight: 700;
    font-size: 13px;
    text-decoration: none;
    border-top: 1px solid rgba(0, 0, 0, 0.05);
}

.notif-footer:hover {
    background: var(--primary-darker);
    color: white;
}

.bg-rose-subtle {
    background: rgba(190, 18, 60, 0.08);
}

.text-rose-dark {
    color: #881337;
}

.bg-success-subtle { background-color: #d1fae5 !important; }
.bg-warning-subtle { background-color: #fef3c7 !important; }
.bg-danger-subtle  { background-color: #fee2e2 !important; }
.bg-info-subtle    { background-color: #e0f2fe !important; }

.text-success { color: #059669 !important; }
.text-warning { color: #d97706 !important; }
.text-danger  { color: #dc2626 !important; }
.text-info    { color: #0284c7 !important; }

.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #eee;
    border-radius: 10px;
}
</style>
