<template>
  <div class="stats-container">
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
      <div>
        <h3 class="page-title">Thống Kê Hệ Thống</h3>
        <p class="page-subtitle">Theo dõi hiệu suất và các hoạt động đúc NFT tính đến ngày {{ currentDate }}</p>
      </div>
      <div class="d-flex gap-2">
      </div>
    </div>

    <!-- Monthly Summary Section -->
    <div class="section-title mb-4 d-flex align-items-center gap-3">
        <div class="dot bg-rose"></div>
        <h4 class="fw-800 mb-0">Thống kê trong tháng này</h4>
    </div>
    <div class="row g-4 mb-5">
      <!-- Total Month -->
      <div class="col-xl-3 col-md-6">
        <div class="stat-card p-4 h-100 card-rose shadow-sm">
          <div class="d-flex justify-content-between align-items-start mb-3">
            <div class="icon-box-lg bg-white-20 text-white">
              <i class="bi bi-patch-check-fill"></i>
            </div>
            <div class="stat-trend bg-white-20 text-white px-2 py-1 rounded">Tháng này</div>
          </div>
          <h2 class="display-5 fw-800 text-white mb-1">{{ thongKe.thang_nay?.tat_ca || 0 }}</h2>
          <p class="text-white-75 mb-0 fw-600">Tổng hồ sơ đã đúc</p>
        </div>
      </div>
      <!-- Bảng điểm -->
      <div class="col-xl-3 col-md-6">
        <div class="stat-card p-4 h-100 card-glass shadow-sm">
          <div class="d-flex justify-content-between mb-3">
            <div class="icon-box-lg bg-blue-subtle text-blue">
              <i class="bi bi-journal-text"></i>
            </div>
          </div>
          <h2 class="display-5 fw-800 text-dark mb-1">{{ thongKe.thang_nay?.bang_diem || 0 }}</h2>
          <p class="text-muted mb-0 fw-600">Bảng điểm thành công</p>
        </div>
      </div>
      <!-- Chứng chỉ -->
      <div class="col-xl-3 col-md-6">
        <div class="stat-card p-4 h-100 card-glass shadow-sm">
          <div class="d-flex justify-content-between mb-3">
            <div class="icon-box-lg bg-purple-subtle text-purple">
              <i class="bi bi-award"></i>
            </div>
          </div>
          <h2 class="display-5 fw-800 text-dark mb-1">{{ thongKe.thang_nay?.chung_chi || 0 }}</h2>
          <p class="text-muted mb-0 fw-600">Chứng chỉ cấp ra</p>
        </div>
      </div>
      <!-- Dự án -->
      <div class="col-xl-3 col-md-6">
        <div class="stat-card p-4 h-100 card-glass shadow-sm">
          <div class="d-flex justify-content-between mb-3">
            <div class="icon-box-lg bg-orange-subtle text-orange">
              <i class="bi bi-briefcase"></i>
            </div>
          </div>
          <h2 class="display-5 fw-800 text-dark mb-1">{{ thongKe.thang_nay?.du_an || 0 }}</h2>
          <p class="text-muted mb-0 fw-600">Dự án được đúc NFT</p>
        </div>
      </div>
    </div>

    <div class="row g-4 mb-5">
      <!-- Chart Placeholder/Visualization -->
      <div class="col-lg-7">
        <div class="info-card p-4 shadow-sm border-0 bg-white min-h-400">
          <div class="d-flex justify-content-between mb-4">
            <h5 class="fw-800 mb-0">Biểu đồ xu hướng (6 tháng)</h5>
            <i class="bi bi-graph-up text-rose"></i>
          </div>
          <div class="chart-container d-flex align-items-end justify-content-around p-4 h-100" style="height: 300px;">
            <div v-for="(item, index) in thongKe.thong_ke_thang" :key="index" class="chart-bar-wrapper text-center">
                <div class="chart-bar bg-rose rounded-pill mb-2" 
                     :style="{ height: (item.count > 0 ? (item.count / maxCount) * 200 : 5) + 'px' }"
                     v-bind:title="item.count + ' hồ sơ'"></div>
                <div class="small text-muted fw-600">{{ item.label.split(' ')[1] }}</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Overall Performance -->
      <div class="col-lg-5">
        <div class="info-card p-4 shadow-sm border-0 bg-white min-h-400">
          <h5 class="fw-800 mb-4 text-dark">Tổng hợp toàn thời gian</h5>
          <div class="all-time-stats mb-4">
            <div class="d-flex justify-content-between mb-3 text-dark">
                <span class="opacity-75">Tổng cộng</span>
                <span class="fw-800 fs-4">{{ thongKe.tong_quat?.tat_ca || 0 }} hồ sơ</span>
            </div>
            <div class="progress mb-4" style="height: 12px; background: rgba(0,0,0,0.05); border-radius: 10px;">
                <div class="progress-bar bg-primary" :style="{ width: (thongKe.tong_quat?.bang_diem / (thongKe.tong_quat?.tat_ca || 1) * 100) + '%' }"></div>
                <div class="progress-bar bg-info" :style="{ width: (thongKe.tong_quat?.chung_chi / (thongKe.tong_quat?.tat_ca || 1) * 100) + '%' }"></div>
                <div class="progress-bar bg-warning" :style="{ width: (thongKe.tong_quat?.du_an / (thongKe.tong_quat?.tat_ca || 1) * 100) + '%' }"></div>
            </div>
            <div class="list-group list-group-flush bg-transparent">
                <div class="list-group-item bg-transparent border-light d-flex justify-content-between align-items-center text-dark p-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="dot-sm bg-primary"></div> Bảng điểm
                    </div>
                    <span class="fw-700">{{ thongKe.tong_quat?.bang_diem || 0 }}</span>
                </div>
                <div class="list-group-item bg-transparent border-light d-flex justify-content-between align-items-center text-dark p-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="dot-sm bg-info"></div> Chứng chỉ
                    </div>
                    <span class="fw-700">{{ thongKe.tong_quat?.chung_chi || 0 }}</span>
                </div>
                <div class="list-group-item bg-transparent border-0 d-flex justify-content-between align-items-center text-dark p-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="dot-sm bg-warning"></div> Dự án
                    </div>
                    <span class="fw-700">{{ thongKe.tong_quat?.du_an || 0 }}</span>
                </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Activity Table -->
    <div class="info-card p-4 shadow-sm border-0 bg-white">
      <div class="d-flex justify-content-between mb-4 align-items-center">
        <h5 class="fw-800 mb-0">Hoạt động đúc NFT gần đây</h5>
        <button class="btn btn-light btn-sm fw-700 rounded-pill px-3">Xem tất cả</button>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle custom-table">
          <thead>
            <tr>
              <th class="border-0 text-muted small fw-700">TOKEN ID</th>
              <th class="border-0 text-muted small fw-700">SINH VIÊN</th>
              <th class="border-0 text-muted small fw-700">LOẠI HỒ SƠ</th>
              <th class="border-0 text-muted small fw-700">TÊN HỒ SƠ</th>
              <th class="border-0 text-muted small fw-700">THỜI GIAN ĐÚC</th>
              <th class="border-0 text-muted small fw-700 text-end">GIAO DỊCH</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in thongKe.gan_day" :key="index">
              <td><span class="badge bg-light text-rose border">#{{ item.token_id || '...' }}</span></td>
              <td class="fw-700">{{ item.ho_ten }}</td>
              <td>
                <span class="badge" :class="getTypeClass(item.loai)">{{ item.loai }}</span>
              </td>
              <td class="text-truncate" style="max-width: 250px;">{{ item.ten_ho_so }}</td>
              <td class="small text-muted">{{ item.ngay_duc }}</td>
              <td class="text-end">
                <a :href="'https://sepolia.etherscan.io/tx/' + item.tx_hash" target="_blank" class="btn btn-icon-sm bg-light-rose text-rose">
                  <i class="bi bi-box-arrow-up-right"></i>
                </a>
              </td>
            </tr>
            <tr v-if="!thongKe.gan_day || thongKe.gan_day.length === 0">
                <td colspan="6" class="text-center py-5 text-muted fst-italic">Chưa có hoạt động đúc NFT nào gần đây.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
import baseRequestAdmin from '../../../core/baseRequestAdmin';

export default {
  name: 'ThongKeIndex',
  data() {
    return {
      thongKe: {
        tong_quat: {},
        thang_nay: {},
        thong_ke_thang: [],
        gan_day: []
      },
      currentDate: new Date().toLocaleDateString('vi-VN', { day: 'numeric', month: 'long', year: 'numeric' }),
      maxCount: 1
    }
  },
  mounted() {
    this.layDuLieu();
  },
  methods: {
    layDuLieu() {
      baseRequestAdmin.get('thong-ke')
        .then(res => {
          if (res.data.status) {
            this.thongKe = res.data.data;
            // Tìm giá trị cao nhất để vẽ biểu đồ
            this.maxCount = Math.max(...this.thongKe.thong_ke_thang.map(o => o.count), 1);
          }
        })
        .catch(err => {
          console.error(err);
          this.$toast.error("Không thể lấy dữ liệu thống kê.");
        });
    },
    getTypeClass(type) {
        if (type === 'Bảng điểm') return 'bg-blue-subtle text-blue';
        if (type === 'Chứng chỉ') return 'bg-purple-subtle text-purple';
        if (type === 'Dự án') return 'bg-orange-subtle text-orange';
        return 'bg-light text-muted';
    }
  }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

.stats-container {
  font-family: 'Plus Jakarta Sans', sans-serif;
}

.fw-800 { font-weight: 800; }
.fw-700 { font-weight: 700; }
.fw-600 { font-weight: 600; }
.text-rose { color: #BE123C; }
.bg-rose { background-color: #BE123C; }

.date-badge {
  border-radius: 20px;
}

.section-title .dot {
    width: 12px; height: 12px;
    border-radius: 4px;
}

/* Stat Cards */
.stat-card {
  border-radius: 28px;
  border: none;
  transition: all 0.3s ease;
}
.stat-card:hover { transform: translateY(-8px); }

.card-rose {
  background: linear-gradient(135deg, #BE123C 0%, #9F1239 100%);
}
.card-glass {
  background: rgba(255, 255, 255, 0.7);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255,255,255,0.3);
}

.icon-box-lg {
  width: 52px; height: 52px;
  border-radius: 16px;
  display: flex; align-items: center; justify-content: center;
  font-size: 24px;
}

.bg-white-20 { background: rgba(255, 255, 255, 0.2); }
.text-white-75 { color: rgba(255, 255, 255, 0.75); }

/* Custom Badge Colors */
.bg-blue-subtle { background-color: #e0f2fe; }
.text-blue { color: #0369a1; }
.bg-purple-subtle { background-color: #f3e8ff; }
.text-purple { color: #7e22ce; }
.bg-orange-subtle { background-color: #ffedd5; }
.text-orange { color: #c2410c; }

/* Info Cards */
.info-card {
  border-radius: 28px;
}
.min-h-400 { min-height: 400px; }
.border-white-10 { border-color: rgba(255,255,255,0.1); }
.dot-sm { width: 8px; height: 8px; border-radius: 50%; }

/* Chart Bars */
.chart-bar {
    width: 40px;
    transition: all 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    background: linear-gradient(to top, #BE123C, #FB7185);
}
.chart-bar:hover {
    filter: brightness(1.1);
    transform: scaleX(1.1);
}

/* Table Style */
.custom-table th {
    padding: 20px 15px;
    background-color: transparent;
}
.custom-table td {
    padding: 18px 15px;
}
.bg-light-rose { background-color: #fff1f2; }
.btn-icon-sm {
    width: 32px; height: 32px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    border: none;
    transition: all 0.2s;
}
.btn-icon-sm:hover { transform: scale(1.1); }
</style>
