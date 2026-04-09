<template>
  <div class="app-shell sinh-vien-portal">
    <Sidebar :sidebar-collapsed="collapsed" />
    <Navbar 
      :sidebar-collapsed="collapsed" 
      @toggle-sidebar="collapsed = !collapsed"
    />
    <main class="main-content" :class="{ 'sidebar-collapsed': collapsed }">
      <router-view />
    </main>
  </div>
</template>

<script>
import Sidebar from '../../components/SinhVien/Sidebar.vue'
import Navbar from '../../components/SinhVien/Navbar.vue'

export default {
  name: 'SinhVienLayout',
  components: { Sidebar, Navbar },
  data() {
    return { 
      collapsed: false
    }
  }
}
</script>

<style scoped>
/* Tông màu Hồng phấn (Pink Theme) đồng bộ cho toàn bộ khu vực Sinh viên */
.sinh-vien-portal {
  --primary: #fdf2f8;        /* Pink 50 - Nền nhẹ */
  --primary-dark: #fce7f3;   /* Pink 100 - Hover */
  --primary-darker: #db2777; /* Pink 600 - Màu nhấn chính */
  --primary-text: #9d174d;   /* Pink 800 - Màu chữ */
  
  /* Ghi đè các màu bổ trợ khác nếu cần */
  --accent-blue: #db2777;
  --sidebar-bg: #fff9fa;
  --danger: #db2777; /* Ghi đè màu danger thành hồng cho khu vực sinh viên */
}

/* Đảm bảo các chấm thông báo kế thừa màu hồng */
:deep(.notif-dot) {
  background-color: var(--danger) !important;
}
</style>
