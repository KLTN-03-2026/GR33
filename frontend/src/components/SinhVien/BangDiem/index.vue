<template>
  <div class="bang-diem-view px-2">
    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
      <div>
        <h3 class="page-title text-dark fw-800">Kết quả học tập</h3>
        <p class="page-subtitle text-muted">Tra cứu điểm số và tiến độ hoàn thành chương trình đào tạo.</p>
      </div>
      <div class="d-flex gap-2">
        <button class="btn btn-light-pink shadow-sm" @click="layDuLieu">
            <i class="bi bi-arrow-clockwise me-1"></i> Làm mới
        </button>
      </div>
    </div>

    <!-- Overview Stats -->
    <div class="row g-4 mb-4" v-if="!dangTai && danhSach.length > 0">
      <div class="col-md-3">
        <div class="stat-card p-4 shadow-sm border-0 bg-white d-flex align-items-center gap-3" style="border-radius: 20px;">
          <div class="icon-box bg-pink-subtle text-accent"><i class="bi bi-star-half"></i></div>
          <div>
            <div class="small text-muted fw-600">ĐIỂM TRUNG BÌNH HỆ 10</div>
            <div class="fs-3 fw-800 text-accent">{{ tinhGPA }}</div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-card p-4 shadow-sm border-0 bg-white d-flex align-items-center gap-3" style="border-radius: 20px;">
          <div class="icon-box bg-pink-subtle text-accent"><i class="bi bi-star-fill"></i></div>
          <div>
            <div class="small text-muted fw-600">ĐIỂM TRUNG BÌNH HỆ 4</div>
            <div class="fs-3 fw-800 text-success">{{ tinhGPAHe4 }}</div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-card p-4 shadow-sm border-0 bg-white d-flex align-items-center gap-3" style="border-radius: 20px;">
          <div class="icon-box bg-pink-subtle text-accent"><i class="bi bi-journal-bookmark-fill"></i></div>
          <div>
            <div class="small text-muted fw-600">SỐ TÍN CHỈ TÍCH LŨY</div>
            <div class="fs-3 fw-800 text-dark">{{ tinhTongTinChi }}</div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-card p-4 shadow-sm border-0 bg-white d-flex align-items-center gap-3" style="border-radius: 20px;">
          <div class="icon-box bg-pink-subtle text-accent"><i class="bi bi-check-circle-fill"></i></div>
          <div>
            <div class="small text-muted fw-600">MÔN HỌC ĐÃ HOÀN THÀNH</div>
            <div class="fs-3 fw-800 text-dark">{{ danhSach.length }}</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Transcript Table -->
    <div class="data-card shadow-sm border-0 bg-white mb-4" style="border-radius: 20px; overflow: hidden;">
      <div class="table-responsive">
        <table class="flux-table text-nowrap">
          <thead>
            <tr class="bg-pink-light">
              <th width="60" class="text-center">STT</th>
              <th>Tên môn học</th>
              <th class="text-center">Số tín chỉ</th>
              <th class="text-center">LỚP</th>
              <th class="text-center">Điểm quá trình</th>
              <th class="text-center">Điểm thi</th>
              <th class="text-center">Điểm tổng kết</th>
              <th class="text-center">Hệ 4</th>
              <th class="text-center">NFT</th>
              <th class="text-center">Yêu cầu NFT</th>
              <th class="text-center">Xếp loại</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="dangTai">
              <td colspan="11" class="text-center py-5">
                <div class="spinner-border text-accent" role="status"></div>
                <div class="mt-2 text-muted small">Đang tải bảng điểm...</div>
              </td>
            </tr>
            <tr v-else-if="danhSach.length === 0">
              <td colspan="11" class="text-center py-5">
                <div class="empty-state py-4 text-center">
                  <i class="bi bi-journal-x d-block mb-3 opacity-25" style="font-size: 4rem"></i>
                  <p class="text-muted fw-600">Hiện tại chưa có dữ liệu điểm thi của bạn.</p>
                </div>
              </td>
            </tr>
            <tr v-else v-for="(item, index) in danhSach" :key="item.id">
              <td class="text-center text-muted fw-600">#{{ index + 1 }}</td>
              <td>
                <div class="fw-700 text-dark">{{ item.lop_hoc?.mon_hoc?.ten_mon_hoc || '---' }}</div>
                <div class="small text-muted">Mã môn: {{ item.lop_hoc?.mon_hoc?.ma_mon_hoc || '---' }}</div>
              </td>
              <td class="text-center fw-600">{{ item.lop_hoc?.mon_hoc?.so_tin_chi || 0 }} TC</td>
              <td class="text-center">
                <div class="fw-700 text-dark">{{ item.lop_hoc?.ten_lop_hoc || '---' }}</div>
                <div class="small fw-600 text-muted">{{ item.lop_hoc?.ma_lop_hoc || '---' }}</div>
              </td>
              <td class="text-center fw-700 text-muted">{{ item.diem_qua_trinh || 0 }}</td>
              <td class="text-center fw-700 text-muted">{{ item.diem_cuoi_ky || 0 }}</td>
              <td class="text-center">
                <div class="overall-mark fw-800 fs-5" :class="layMauDiem(item.diem_tong_ket || 0)">
                  {{ item.diem_tong_ket || '---' }}
                </div>
              </td>
              <td class="text-center fw-800 text-success">
                {{ quyDoiHe4(item.diem_chu) }}
              </td>
              <td class="text-center">
                <span class="badge" :class="layLopTrangThai(item.trang_thai)">
                    {{ layTenTrangThai(item.trang_thai) }}
                </span>
                  <div v-if="item.trang_thai === 1" class="mt-1 d-flex justify-content-center gap-2">
                    <a :href="'https://sepolia.etherscan.io/tx/' + item.nft_van_bang.tx_hash_thanh_cong" 
                       target="_blank" class="text-accent small text-decoration-none fw-700" title="Xem Etherscan">
                      <i class="bi bi-box-arrow-up-right"></i>
                    </a>
                    <a href="javascript:void(0)" @click="moModalQR(item.nft_van_bang)" 
                       class="text-dark small text-decoration-none fw-700" title="Xem mã QR">
                      <i class="bi bi-qr-code"></i>
                    </a>
                  </div>
                <div v-else-if="item.trang_thai === 4" class="mt-1 text-center">
                  <a v-if="item.nft_van_bang?.tx_hash_burn" 
                     :href="'https://sepolia.etherscan.io/tx/' + item.nft_van_bang.tx_hash_burn" 
                     target="_blank" class="text-danger small text-decoration-none fw-700">
                    <i class="bi bi-fire me-1"></i>Bằng chứng Hủy
                  </a>
                  <span v-else class="text-muted small fw-600">
                    <i class="bi bi-slash-circle me-1"></i>Đã thu hồi (Legacy)
                  </span>
                </div>
              </td>
              <td class="text-center">
                  <div class="d-flex justify-content-center align-items-center gap-2" style="min-height: 40px">
                    <button v-if="item.trang_thai == 0" 
                      class="btn btn-sm btn-pink-outline rounded-pill fw-700 px-3"
                      @click="guiYeuCauNFT(item)"
                      :disabled="dangLuu || $authSV.user.trang_thai == 0 || $authSV.user.trang_thai == 2 || item.diem_tong_ket == null"
                      :title="item.diem_tong_ket == null ? 'Chưa có điểm tổng kết, không thể yêu cầu NFT' : ($authSV.user.trang_thai == 0 || $authSV.user.trang_thai == 2) ? 'Trạng thái tài khoản không cho phép yêu cầu đúc NFT' : 'Gửi yêu cầu'">
                      <i class="bi bi-send-check-fill me-1"></i> Gửi yêu cầu
                    </button>
                    <span v-else-if="item.trang_thai == 1" class="text-success small fw-700">
                      <i class="bi bi-patch-check-fill me-1"></i> Đã cấp
                    </span>
                    <span v-else-if="item.trang_thai == 2" class="text-muted small fw-600">
                      <i class="bi bi-hourglass-split me-1"></i> Chờ duyệt
                    </span>
                    <span v-else class="text-muted small italic">---</span>
                  </div>
              </td>
              <td class="text-center">
                <span :class="layMauXepLoai(item.diem_chu)" class="fw-800">
                    {{ item.diem_chu || '---' }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal Xác nhận NFT -->
    <div class="modal fade" id="confirmNftModal" tabindex="-1" aria-hidden="true" ref="refsModalNFT">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 24px;">
                <div class="modal-header border-0 bg-pink-gradient text-white p-4" style="border-radius: 24px 24px 0 0">
                    <h5 class="modal-title fw-800">Xác nhận yêu cầu NFT</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <div class="mb-4">
                        <i class="bi bi-shield-lock-fill text-accent display-1 opacity-25"></i>
                    </div>
                    <h5 class="fw-800 text-dark mb-3">Bạn chắc chắn muốn tiếp tục?</h5>
                    <p class="text-muted mb-0">Hồ sơ sau khi gửi yêu cầu sẽ được <span class="text-accent fw-700">KHÓA TUYỆT ĐỐI</span> để đảm bảo tính toàn vẹn của dữ liệu trong suốt quá trình đúc NFT trên Blockchain.</p>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light-pink flex-fill py-2 fw-600" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="button" class="btn btn-pink flex-fill py-2 fw-800 shadow-pink" 
                        @click="thucHienGuiYeuCau" :disabled="dangLuu">
                        <span v-if="dangLuu" class="spinner-border spinner-border-sm me-2"></span>
                        Xác nhận gửi
                    </button>
                </div>
            </div>
        </div>
    </div>

    <ModalQR ref="modalQR" :item="selectedNft" />


  </div>
</template>

<script>
import baseRequestSinhVien from "../../../core/baseRequestSinhVien";
import ModalQR from "../../Common/ModalQR.vue";
import { authStoreSinhVien } from "../../../core/authStoreSinhVien";

export default {
  name: "SinhVienBangDiem",
  components: {
    ModalQR
  },
  data() {
    return {
      danhSach: [],
      dangTai: false,
      dangLuu: false,
      itemYeuCau: null,
      selectedNft: null,
      instanceModalNFT: null,
    };
  },
  computed: {
    tinhTongTinChi() {
      return this.danhSach.reduce((sum, item) => sum + (item.lop_hoc?.mon_hoc?.so_tin_chi || 0), 0);
    },
    tinhGPA() {
      if (this.danhSach.length === 0) return "0.0";
      let tongDiem = 0;
      let tongTinChi = 0;
      this.danhSach.forEach(item => {
        const diem = this.tinhDiemTongKet(item);
        const tinChi = item.lop_hoc?.mon_hoc?.so_tin_chi || 0;
        tongDiem += diem * tinChi;
        tongTinChi += tinChi;
      });
      return tongTinChi === 0 ? "0.0" : (tongDiem / tongTinChi).toFixed(2);
    },
    tinhGPAHe4() {
      if (this.danhSach.length === 0) return "0.00";
      let tongDiem4 = 0;
      let tongTinChi = 0;
      this.danhSach.forEach(item => {
        const diem4 = this.quyDoiHe4(item.diem_chu);
        const tinChi = item.lop_hoc?.mon_hoc?.so_tin_chi || 0;
        tongDiem4 += diem4 * tinChi;
        tongTinChi += tinChi;
      });
      return tongTinChi === 0 ? "0.00" : (tongDiem4 / tongTinChi).toFixed(2);
    }
  },
  mounted() {
    this.layDuLieu();
    if (window.bootstrap) {
        this.instanceModalNFT = new window.bootstrap.Modal(this.$refs.refsModalNFT);
    }
  },
  methods: {
    layDuLieu() {
      this.dangTai = true;
      // Đồng bộ profile mới nhất để cập nhật localStorage (ví dụ khi sinh viên vừa thêm ví)
      baseRequestSinhVien.get("profile")
        .then(res => {
            if (res.data.status) {
                authStoreSinhVien.updateUser(res.data.data);
            }
        });

      baseRequestSinhVien.get("bang-diems")
        .then(res => {
          this.danhSach = res.data.data || [];
        })
        .catch(err => {
          this.$toast.error("Không thể tải bảng điểm!");
        })
        .finally(() => {
          this.dangTai = false;
        });
    },
    moModalQR(nft) {
        this.selectedNft = nft;
        this.$refs.modalQR.show();
    },
    tinhDiemTongKet(item) {
      if (item.diem_tong_ket) return item.diem_tong_ket;
      const db = item.diem_qua_trinh || 0;
      const dck = item.diem_cuoi_ky || 0;
      return (db * 0.45 + dck * 0.55).toFixed(1);
    },
    layMauDiem(diem) {
      if (diem >= 8.5) return 'text-success';
      if (diem >= 7.0) return 'text-info';
      if (diem >= 5.0) return 'text-warning';
      return 'text-danger';
    },
    tinhXepLoai(diem) {
      if (diem >= 8.5) return 'A';
      if (diem >= 7.0) return 'B';
      if (diem >= 5.5) return 'C';
      if (diem >= 4.0) return 'D';
      return 'F';
    },
    layMauXepLoai(loai) {
      if (!loai) return 'text-muted';
      if (['A+', 'A', 'A-'].includes(loai)) return 'text-success';
      if (['B+', 'B', 'B-'].includes(loai)) return 'text-info';
      if (['C+', 'C', 'C-'].includes(loai)) return 'text-warning';
      if (['D+', 'D'].includes(loai)) return 'text-primary';
      return 'text-danger';
    },
    quyDoiHe4(diemChu) {
      if (!diemChu) return 0.0;
      const mapping = {
        'A+': 4.0, 'A': 4.0, 'A-': 3.67,
        'B+': 3.33, 'B': 3.0, 'B-': 2.67,
        'C+': 2.33, 'C': 2.0, 'C-': 1.67,
        'D+': 1.33, 'D': 1.0,
        'F': 0.0
      };
      return mapping[diemChu.toUpperCase()] || 0.0;
    },
    layTenTrangThai(trangThai) {
      if (trangThai == 1) return 'Đã Đúc NFT';
      if (trangThai == 2) return 'Chờ Duyệt';
      if (trangThai == 4) return 'Đã Thu Hồi';
      return 'Chưa Đúc';
    },
    layLopTrangThai(trangThai) {
      if (trangThai == 1) return 'bg-success-subtle text-success border border-success';
      if (trangThai == 2) return 'bg-warning-subtle text-warning border border-warning';
      if (trangThai == 4) return 'bg-danger-subtle text-danger border border-danger fw-800';
      return 'bg-secondary-subtle text-secondary border border-secondary';
    },
    guiYeuCauNFT(item) {
        const user = authStoreSinhVien.user;
        
        // Kiểm tra linh hoạt cả 2 định dạng (snake_case và camelCase) của Laravel
        const vi = user?.vi_sinh_vien || user?.viSinhVien;

        if (!vi?.dia_chi_vi) {
            this.$toast.error("Tài khoản của bạn chưa được liên kết với ví Blockchain. Vui lòng cài đặt ví tại thông tin cá nhân trước khi thực hiện yêu cầu NFT.");
            return;
        }

        if (item.diem_tong_ket == null) {
            this.$toast.error("Bảng điểm chưa hoàn thành (chưa có điểm tổng kết). Vui lòng đợi giảng viên cập nhật điểm trước khi yêu cầu NFT.");
            return;
        }

        this.itemYeuCau = item;
        this.instanceModalNFT.show();
    },
    thucHienGuiYeuCau() {
        if (!this.itemYeuCau) return;
        
        this.dangLuu = true;
        baseRequestSinhVien.post("nft/request", {
            record_id: this.itemYeuCau.id,
            type: 'bang_diem'
        }).then(res => {
            if (res.data.status) {
                this.$toast.success(res.data.message);
                this.instanceModalNFT.hide();
                this.layDuLieu();
            } else {
                this.$toast.error(res.data.message);
            }
        }).finally(() => {
            this.dangLuu = false;
        });
    },

  }
};
</script>

<style scoped>
.text-accent { color: #db2777; }
.bg-pink-subtle { background: #fdf2f8; }
.bg-pink-light { background: #fdf2f8; }

.icon-box {
  width: 48px; height: 48px; border-radius: 12px;
  display: flex; align-items: center; justify-content: center; font-size: 1.25rem;
}

.stat-card { transition: all 0.3s; border: 1px solid #fce7f3 !important; }
.stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px -5px rgba(219,39,119,0.15) !important; }

.btn-light-pink {
  background: #fdf2f8; color: #db2777; border-radius: 12px;
  padding: 8px 16px; border: 1px solid #fce7f3; font-weight: 600;
}
.btn-pink {
  background: #db2777; color: white; border-radius: 14px;
  padding: 10px 20px; border: none; font-weight: 700; transition: all 0.3s;
}
.btn-pink:hover { background: #be185d; transform: translateY(-2px); }
.bg-pink-gradient { background: linear-gradient(135deg, #db2777 0%, #be185d 100%); }
.bg-pink-light { background: #fdf2f8; }
.shadow-pink { box-shadow: 0 10px 15px -3px rgba(219,39,119,0.25); }
.btn-pink-outline {
  color: #db2777; border: 1.5px solid #db2777; transition: all 0.2s;
}
.btn-pink-outline:hover {
  background: #db2777; color: white;
}

.fw-800 { font-weight: 800; }
.fw-700 { font-weight: 700; }
.fw-600 { font-weight: 600; }

.overall-mark { letter-spacing: -0.5px; }

/* Table styling override */
:deep(.flux-table) thead th {
    background: #fdf2f8;
    color: #db2777;
    font-weight: 800;
    text-transform: uppercase;
    font-size: 12px;
    letter-spacing: 0.5px;
    padding: 16px;
}
</style>
