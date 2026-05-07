<template>
  <div class="dashboard-container">
    <!-- Welcome Header -->
    <div class="welcome-banner p-5 mb-4 shadow-sm overflow-hidden position-relative">
      <div class="row align-items-center position-relative" style="z-index: 2;">
        <div class="col-lg-7 text-white">
          <h1 class="display-5 fw-800 mb-3">Chào mừng trở lại, Admin!</h1>
          <p class="lead opacity-75 mb-4">Hệ thống quản lý đào tạo đã sẵn sàng. Bạn có thể quản lý nhân viên, môn học và các lớp học một cách dễ dàng.</p>
          <div class="d-flex gap-2">
            <router-link to="/admin/nhan-vien" class="btn btn-light fw-700 px-4 py-2 rounded-pill text-rose">Bắt đầu ngay</router-link>
            <button class="btn btn-outline-light fw-600 px-4 py-2 rounded-pill">Xem hướng dẫn</button>
          </div>
        </div>
        <div class="col-lg-5 d-none d-lg-block text-center">
            <i class="bi bi-shield-lock-fill display-1 text-white opacity-25"></i>
        </div>
      </div>
      <!-- Decorative circles -->
      <div class="deco-circle circle-1"></div>
      <div class="deco-circle circle-2"></div>
    </div>

    <!-- Quick Access Cards -->
    <div class="row g-4 mb-5">
      <div class="col-md-4">
        <router-link to="/admin/nhan-vien" class="text-decoration-none">
          <div class="access-card p-4 h-100 shadow-sm border-0">
            <div class="icon-box-xl bg-rose-subtle text-rose mb-4">
              <i class="bi bi-people-fill"></i>
            </div>
            <h4 class="fw-800 text-dark mb-2">Nhân Viên</h4>
            <p class="text-muted small">Quản lý danh sách, hồ sơ và quyền hạn của cán bộ nhân viên trong trường.</p>
            <div class="text-rose fw-700 mt-3 d-flex align-items-center gap-2">
               Truy cập ngay <i class="bi bi-arrow-right"></i>
            </div>
          </div>
        </router-link>
      </div>
      <div class="col-md-4">
        <router-link to="/admin/mon-hoc" class="text-decoration-none">
          <div class="access-card p-4 h-100 shadow-sm border-0 middle-card">
            <div class="icon-box-xl bg-rose text-white mb-4 shadow-sm">
              <i class="bi bi-journal-bookmark-fill"></i>
            </div>
            <h4 class="fw-800 text-dark mb-2">Môn Học</h4>
            <p class="text-muted small">Thiết lập chương trình đào tạo, quản lý mã môn học và số tín chỉ tương ứng.</p>
            <div class="text-rose fw-700 mt-3 d-flex align-items-center gap-2">
               Truy cập ngay <i class="bi bi-arrow-right"></i>
            </div>
          </div>
        </router-link>
      </div>
      <div class="col-md-4">
        <router-link to="/admin/lop-hoc" class="text-decoration-none">
          <div class="access-card p-4 h-100 shadow-sm border-0">
            <div class="icon-box-xl bg-rose-subtle text-rose mb-4">
              <i class="bi bi-mortarboard-fill"></i>
            </div>
            <h4 class="fw-800 text-dark mb-2">Lớp Học</h4>
            <p class="text-muted small">Phân bổ lớp học, quản lý sĩ số sinh viên và phân công giảng viên chủ nhiệm.</p>
            <div class="text-rose fw-700 mt-3 d-flex align-items-center gap-2">
               Truy cập ngay <i class="bi bi-arrow-right"></i>
            </div>
          </div>
        </router-link>
      </div>
    </div>

    <!-- System Info Section -->
    <div class="row g-4">

        <div class="col-lg-4">
            <div class="info-card p-4 shadow-sm border-0 bg-white text-center h-100" style="border-radius: 24px;">
                <div class="mb-3 mt-2">
                    <i class="bi bi-calendar-event text-rose display-5"></i>
                </div>
                <div class="h3 fw-800 mb-0">{{ (new Date()).toLocaleDateString('vi-VN', { weekday: 'long' }) }}</div>
                <div class="h5 text-muted mb-4">{{ (new Date()).toLocaleDateString('vi-VN', { day: 'numeric', month: 'long', year: 'numeric' }) }}</div>
                <hr class="opacity-10 my-4">
                <div class="d-grid">
                    <button class="btn btn-rose-light py-2 fw-700 rounded-pill"> <i class="bi bi-clock me-2"></i> Xem lịch trực</button>
                </div>
            </div>
        </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AdminDashboard',
  data() {
    return {
      // Dữ liệu đã được lượt bỏ các phần thống kê phức tạp
    }
  }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

.dashboard-container {
  font-family: 'Plus Jakarta Sans', sans-serif;
  padding: 1rem;
}

.fw-800 { font-weight: 800; }
.fw-700 { font-weight: 700; }
.text-rose { color: #BE123C; }

/* Welcome Banner */
.welcome-banner {
  background: linear-gradient(135deg, #BE123C 0%, #9F1239 100%);
  border-radius: 32px;
}
.deco-circle {
  position: absolute;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.05);
}
.circle-1 { width: 300px; height: 300px; top: -100px; right: -50px; }
.circle-2 { width: 150px; height: 150px; bottom: -50px; left: 10%; }

/* Access Cards */
.access-card {
  background: #fff;
  border-radius: 28px;
  transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.access-card:hover { 
  transform: translateY(-12px) scale(1.02); 
  box-shadow: 0 20px 40px rgba(0,0,0,0.08) !important;
}
.middle-card {
  background: #fff;
  border: 1px solid rgba(190, 18, 60, 0.1);
}

.icon-box-xl {
  width: 64px; height: 64px;
  border-radius: 20px;
  display: flex; align-items: center; justify-content: center;
  font-size: 28px;
}
.bg-rose-subtle { background: #fff1f2; }
.bg-rose { background: #BE123C; }

/* Info Cards */
.info-card {
    border-radius: 24px;
}
.btn-rose-light {
    background: #fff1f2;
    color: #BE123C;
    border: none;
    transition: all 0.3s;
}
.btn-rose-light:hover { background: #ffe4e6; }
</style>
