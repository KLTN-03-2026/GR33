<template>
  <div class="bang-diem-management">
    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
      <div>
        <h3 class="page-title">Quản lý Bảng Điểm</h3>
        <p class="page-subtitle">Hệ thống quản lý điểm số và đúc NFT bảng điểm.</p>
      </div>
      <button class="btn-new" @click="moModalThem">
        <i class="bi bi-plus-circle-fill"></i> Nhập điểm mới
      </button>
    </div>

    <!-- Main Content Card -->
    <div class="data-card shadow-sm border-0">
      <!-- Table Controls -->
      <div class="table-controls p-3 border-bottom bg-light-subtle">
        <div class="row g-3 align-items-center">
          <div class="col-md-3">
            <label class="form-label small fw-800 text-uppercase opacity-50">Năm học</label>
            <select class="form-select flux-input" v-model="namHocDaChon" @change="idLopDaChon = ''">
              <option value="">Tất cả năm học</option>
              <option v-for="year in cacNamHocDuyNhat" :key="year" :value="year">{{ year }}</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-800 text-uppercase opacity-50">Lớp học</label>
            <select class="form-select flux-input" v-model="idLopDaChon">
              <option value="">Tất cả lớp học</option>
              <option v-for="lh in cacLopDaLoc" :key="lh.id" :value="lh.id">
                {{ lh.ma_lop_hoc }} - {{ lh.ten_lop_hoc }}
              </option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label small fw-800 text-uppercase opacity-50">Tìm kiếm sinh viên</label>
            <div class="navbar-search m-0">
              <i class="bi bi-search search-icon"></i>
              <input type="text" v-model="tuKhoaTimKiem" placeholder="Tên hoặc mã sinh viên..." />
            </div>
          </div>
          <div class="col-md-2 d-flex align-items-end justify-content-end gap-2" style="padding-top: 28px;">
            <button class="btn btn-light border" @click="layDuLieu" title="Làm mới">
              <i class="bi bi-arrow-clockwise"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Table Content -->
      <div class="table-responsive">
        <table class="flux-table text-nowrap">
          <thead>
            <tr>
              <th width="60" class="text-center">STT</th>
              <th>Sinh viên</th>
              <th>Lớp học</th>
              <th class="text-center">Quá trình</th>
              <th class="text-center">Cuối kỳ</th>
              <th class="text-center">Tổng kết</th>
              <th class="text-center">NFT</th>
              <th class="text-center">Khóa</th>
              <th class="text-center">Ngày nhập</th>
              <th class="text-end" width="160">Hành động</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="dangTai">
              <td colspan="10" class="text-center py-5">
                <div class="spinner-border text-rose" role="status"></div>
              </td>
            </tr>
            <tr v-else-if="danhSachPhanTrang.length === 0">
              <td colspan="10" class="text-center py-5">
                <div class="empty-state">
                  <i class="bi bi-clipboard2-data d-block mb-2 opacity-25" style="font-size: 3rem"></i>
                  <span class="text-muted">Không tìm thấy dữ liệu điểm phù hợp.</span>
                </div>
              </td>
            </tr>
            <tr v-else v-for="(item, index) in danhSachPhanTrang" :key="item.id">
              <td class="text-center text-muted fw-600">#{{ (trangHienTai - 1) * soBanGhiTrenTrang + index + 1 }}</td>
              <td>
                <div class="fw-700 text-main">{{ item.sinh_vien?.ho_ten || '---' }}</div>
                <div class="small text-muted">{{ item.sinh_vien?.ma_sinh_vien }}</div>
              </td>
              <td>
                <div class="small fw-600 text-muted">
                  <i class="bi bi-mortarboard me-1"></i> {{ item.lop_hoc?.ten_lop_hoc || '---' }}
                </div>
              </td>
              <td class="text-center fw-600">{{ item.diem_qua_trinh }}</td>
              <td class="text-center fw-600">{{ item.diem_cuoi_ky }}</td>
              <td class="text-center">
                <div class="score-badge" :class="layLopDiem(item.diem_tong_ket)">
                  {{ item.diem_tong_ket }}
                </div>
              </td>
              <td class="text-center">
                <span class="badge" :class="layLopTrangThai(item.trang_thai)">
                  {{ layTenTrangThai(item.trang_thai) }}
                </span>
                <div v-if="item.trang_thai === 1 && item.nft_van_bang?.tx_hash_thanh_cong" class="mt-1">
                  <a :href="'https://sepolia.etherscan.io/tx/' + item.nft_van_bang.tx_hash_thanh_cong" 
                     target="_blank" class="text-info small text-decoration-none fw-600">
                    <i class="bi bi-box-arrow-up-right me-1"></i>Etherscan
                  </a>
                </div>
                <div v-if="item.trang_thai === 0 && item.nft_van_bang?.trang_thai === 4" class="mt-1">
                  <a v-if="item.nft_van_bang?.tx_hash_burn" 
                     :href="'https://sepolia.etherscan.io/tx/' + item.nft_van_bang.tx_hash_burn" 
                     target="_blank" class="text-danger small text-decoration-none fw-600">
                    <i class="bi bi-fire me-1"></i>Bằng chứng Hủy
                  </a>
                  <span v-else class="text-muted small fw-600">
                    <i class="bi bi-slash-circle me-1"></i>Đã thu hồi (Legacy)
                  </span>
                </div>
              </td>
              <td class="text-center">
                <i v-if="item.is_locked" class="bi bi-lock-fill text-danger" title="Đã khóa"></i>
                <i v-else class="bi bi-unlock-fill text-success" title="Không khóa"></i>
              </td>
              <td class="text-center text-muted small fw-600">
                {{ item.ngay_vao_diem || '---' }}
              </td>
              <td class="text-end">
                <div class="d-flex justify-content-end gap-2">
                  <button v-if="item.nft_van_bang?.token_id !== null" class="btn btn-action shadow-sm" title="Xác thực (Truy vết)" @click="moModalTrace(item)">
                    <i class="bi bi-patch-check-fill text-rose"></i>
                  </button>
                  <button class="btn btn-action shadow-sm" title="Xem chi tiết" @click="moModalChiTiet(item)">
                    <i class="bi bi-eye-fill text-info"></i>
                  </button>
                  <button v-if="!item.is_locked" class="btn btn-action shadow-sm" @click="moModalSua(item)" title="Sửa">
                    <i class="bi bi-pencil-fill text-primary-darker"></i>
                  </button>
                  <button v-else class="btn btn-action shadow-sm opacity-50 cursor-not-allowed" disabled title="Hồ sơ đang bị khóa để xử lý Blockchain">
                    <i class="bi bi-pencil-fill text-muted"></i>
                  </button>
                  <button v-if="item.trang_thai !== 1 && !item.is_locked" class="btn btn-action shadow-sm" @click="xuLyXoa(item)" title="Xóa">
                    <i class="bi bi-trash-fill text-danger"></i>
                  </button>
                  <button v-else class="btn btn-action shadow-sm opacity-50 cursor-not-allowed" disabled title="Không thể xóa điểm đã đúc NFT hoặc đã khóa">
                    <i class="bi bi-trash-fill text-muted"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination Footer -->
      <div class="table-footer d-flex flex-column flex-md-row justify-content-between align-items-center p-3 border-top mt-1"
        v-if="tongSoTrang > 1">
        <span class="small text-muted mb-3 mb-md-0">
          Hiển thị <b>{{ danhSachPhanTrang.length > 0 ? (trangHienTai - 1) * soBanGhiTrenTrang + 1 : 0 }}</b>
          - <b>{{ Math.min(trangHienTai * soBanGhiTrenTrang, danhSachHienThi.length) }}</b>
          trong tổng số <b>{{ danhSachHienThi.length }}</b> bản ghi
        </span>
        <nav>
          <ul class="pagination pagination-sm m-0 gap-1">
            <li class="page-item" :class="{ disabled: trangHienTai === 1 }">
              <a class="page-link border-0 rounded-circle shadow-sm" href="#" @click.prevent="trangHienTai--">
                <i class="bi bi-chevron-left"></i>
              </a>
            </li>
            <li class="page-item" v-for="p in cacTrangHienThi" :key="p" :class="{ active: trangHienTai === p }">
              <a class="page-link border-0 rounded-circle shadow-sm" href="#" @click.prevent="trangHienTai = p">{{ p
                }}</a>
            </li>
            <li class="page-item" :class="{ disabled: trangHienTai === tongSoTrang }">
              <a class="page-link border-0 rounded-circle shadow-sm" href="#" @click.prevent="trangHienTai++">
                <i class="bi bi-chevron-right"></i>
              </a>
            </li>
          </ul>
        </nav>
      </div>
    </div>

    <!-- Edit/Create Modal -->
    <div class="modal fade" id="bangDiemModal" tabindex="-1" aria-hidden="true" ref="refsModal">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden">
          <div class="modal-header border-0 bg-rose-v2 text-white p-4">
            <h5 class="modal-title fw-800">{{ laCapNhat ? 'Cập nhật Bảng Điểm' : 'Nhập Điểm Mới' }}</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-4">
            <form @submit.prevent="xuLyLuu">
              <div class="row g-3">
                <div class="col-md-12">
                  <label class="form-label fw-700 small text-uppercase opacity-75">Môn học</label>
                  <div class="navbar-search mb-2 w-100" style="max-width: 100%;" v-if="!laCapNhat">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" v-model="tuKhoaMonHoc" placeholder="Tìm theo tên hoặc mã môn học..." />
                  </div>
                  <select class="form-select flux-input" v-model="idMonHocTam" :disabled="laCapNhat" required>
                    <option value="" disabled>Chọn môn học...</option>
                    <option v-for="mh in danhSachMonHocLoc" :key="mh.id" :value="mh.id">
                      {{ mh.ma_mon_hoc }} - {{ mh.ten_mon_hoc }}
                    </option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-700 small text-uppercase opacity-75">Lớp học (Đã kết thúc)</label>
                  <div class="navbar-search mb-2 w-100" style="max-width: 100%;" v-if="!laCapNhat && idMonHocTam">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" v-model="tuKhoaLopHoc" placeholder="Tìm tên hoặc mã lớp..." />
                  </div>
                  <select class="form-select flux-input" v-model="duLieuForm.lop_hoc_id" :disabled="laCapNhat || !idMonHocTam" required>
                    <option value="" disabled>{{ !idMonHocTam ? 'Vui lòng chọn môn học trước' : 'Chọn lớp học...' }}</option>
                    <option v-for="lh in danhSachLopHocLoc" :key="lh.id" :value="lh.id">
                      {{ lh.ma_lop_hoc }} - {{ lh.ten_lop_hoc }}
                    </option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-700 small text-uppercase opacity-75">Sinh viên</label>
                  <div class="navbar-search mb-2 w-100" style="max-width: 100%;" v-if="!laCapNhat && duLieuForm.lop_hoc_id">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" v-model="tuKhoaSinhVien" placeholder="Tìm tên hoặc MSSV..." />
                  </div>
                  <select class="form-select flux-input" v-model="duLieuForm.sinh_vien_id" :disabled="laCapNhat || !duLieuForm.lop_hoc_id" required>
                    <option value="" disabled>Chọn sinh viên...</option>
                    <option v-for="sv in danhSachSinhVienLoc" :key="sv.id" :value="sv.id">
                      {{ sv.ma_sinh_vien }} - {{ sv.ho_ten }}
                    </option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-700 small text-uppercase opacity-75">Điểm quá trình</label>
                  <input type="number" step="0.1" min="0" max="10" class="form-control flux-input"
                    v-model="duLieuForm.diem_qua_trinh" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-700 small text-uppercase opacity-75">Điểm cuối kỳ</label>
                  <input type="number" step="0.1" min="0" max="10" class="form-control flux-input"
                    v-model="duLieuForm.diem_cuoi_ky" required>
                </div>
              </div>

              <div class="mt-4 pt-2 d-flex gap-3">
                <button type="button" class="btn btn-light border px-4 flex-fill fw-600" data-bs-dismiss="modal">Hủy
                  bỏ</button>
                <button type="submit" class="btn btn-rose-v2 px-4 flex-fill fw-700 shadow-sm" :disabled="dangLuu">
                  <span v-if="dangLuu" class="spinner-border spinner-border-sm me-1"></span>
                  {{ laCapNhat ? 'Cập nhật' : 'Lưu bảng điểm' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Detail Modal -->
    <div class="modal fade" id="detailBangDiemModal" tabindex="-1" aria-hidden="true" ref="refsModalChiTiet">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden">
          <div class="modal-header border-0 bg-rose-v2 text-white p-4">
            <h5 class="modal-title fw-800">Chi tiết Bảng Điểm</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-4" v-if="itemDuocChon">
            <div class="row g-4">
              <div class="col-md-6">
                <div class="detail-group">
                  <label class="small text-uppercase fw-800 text-muted opacity-50">Sinh viên</label>
                  <div class="mt-2">
                    <div class="fw-800 fs-5 text-rose-dark">{{ itemDuocChon.sinh_vien?.ho_ten }}</div>
                    <div class="fw-600">MSSV: {{ itemDuocChon.sinh_vien?.ma_sinh_vien }}</div>
                    <div class="small text-muted">Lớp: {{ itemDuocChon.lop_hoc?.ten_lop_hoc }}</div>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="detail-group">
                  <label class="small text-uppercase fw-800 text-muted opacity-50">Kết quả học tập</label>
                  <div class="mt-2 row">
                    <div class="col-6">Quá trình: <b>{{ itemDuocChon.diem_qua_trinh }}</b></div>
                    <div class="col-6">Cuối kỳ: <b>{{ itemDuocChon.diem_cuoi_ky }}</b></div>
                    <div class="col-12 mt-2">
                      <span class="fs-4 fw-800 me-2" :class="layLopChuDiem(itemDuocChon.diem_tong_ket)">
                        {{ itemDuocChon.diem_tong_ket }}
                      </span>
                      <span class="badge bg-light text-dark border">Điểm chữ: {{ itemDuocChon.diem_chu }}</span>
                      <span class="badge bg-light text-dark border ms-2">Hệ 4: {{ itemDuocChon.diem_he_4 }}</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-12 text-center">
                <div class="detail-group">
                   <div class="d-flex justify-content-center gap-4">
                      <div>
                        <label class="small text-uppercase fw-800 text-muted d-block">NFT Status</label>
                        <span class="badge mt-1" :class="layLopTrangThai(itemDuocChon.trang_thai)">{{ layTenTrangThai(itemDuocChon.trang_thai) }}</span>
                      </div>
                      <div>
                        <label class="small text-uppercase fw-800 text-muted d-block">Data Lock</label>
                        <span v-if="itemDuocChon.is_locked" class="badge mt-1 bg-danger-subtle text-danger border border-danger"><i class="bi bi-lock-fill"></i> Locked</span>
                        <span v-else class="badge mt-1 bg-success-subtle text-success border border-success"><i class="bi bi-unlock-fill"></i> Unlocked</span>
                      </div>
                      <div>
                        <label class="small text-uppercase fw-800 text-muted d-block">Ngày vào điểm</label>
                        <span class="fw-600">{{ itemDuocChon.ngay_vao_diem || '---' }}</span>
                      </div>
                   </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer border-0 p-4 pt-0">
            <button type="button" class="btn btn-light border px-4 fw-600" data-bs-dismiss="modal">Đóng</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteBangDiemModal" tabindex="-1" aria-hidden="true" ref="modalElementXoa">
        <div class="modal-dialog modal-dialog-centered modal-sm" style="max-width: 400px;">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-body p-4 text-center">
                    <div class="mb-3">
                        <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 3rem;"></i>
                    </div>
                    <h4 class="fw-800 text-dark mb-2">Xác nhận xóa?</h4>
                    <p class="text-muted mb-4">Bạn có chắc chắn muốn xóa bảng điểm của sinh viên <b>{{ itemXoa?.sinh_vien?.ho_ten }}</b>? Hành động này không thể hoàn tác.</p>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light border flex-fill fw-600" data-bs-dismiss="modal">Hủy bỏ</button>
                        <button type="button" class="btn btn-danger flex-fill fw-700" @click="xacNhanXoa" :disabled="dangTai">
                            <span v-if="dangTai" class="spinner-border spinner-border-sm me-1"></span>
                            Xác nhận xóa
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Traceability Modal -->
    <div class="modal fade" id="modalTrace" tabindex="-1" ref="refsModalTrace">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg overflow-hidden" style="border-radius: 20px;">
          <div class="modal-header border-0 bg-rose text-white p-4">
            <h5 class="modal-title fw-800">Xác thực hồ sơ Blockchain</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-4">
            <div v-if="dangTaiTrace" class="text-center py-5">
              <div class="spinner-border text-rose" role="status"></div>
              <p class="mt-3 text-muted fw-600">Đang truy vấn dữ liệu từ nút Blockchain...</p>
            </div>
            <div v-else-if="traceData" class="row g-4">
              <!-- Left: Signer Info -->
              <div class="col-md-5">
                <h6 class="text-rose fw-800 small text-uppercase mb-3">Người ký duyệt</h6>
                <div class="p-3 border rounded-4 text-center bg-light mb-3">
                  <i class="bi bi-person-badge text-rose display-4 d-block mb-2"></i>
                  <h5 class="fw-800 text-dark mb-1">{{ traceData.signer_info.name }}</h5>
                  <div class="badge bg-rose-subtle text-rose px-3 rounded-pill mb-2">{{ traceData.signer_info.position }}</div>
                  <p class="small text-muted mb-0">{{ traceData.signer_info.department }}</p>
                </div>
                <div class="mb-3 text-start">
                  <label class="small text-muted fw-700 text-uppercase d-block mb-1">Địa chỉ Ví</label>
                  <div class="text-truncate px-2 py-1 bg-dark text-white-50 rounded-2 small font-monospace">{{ traceData.signer_info.wallet }}</div>
                </div>
              </div>

              <!-- Right: Content -->
              <div class="col-md-7">
                <h6 class="text-primary fw-800 small text-uppercase mb-3">Bằng chứng Xác thực</h6>
                <div class="p-3 border rounded-4 bg-white">
                  <div class="d-flex justify-content-between mb-2 border-bottom pb-2">
                    <span class="text-muted fw-600">Trạng thái NFT</span>
                    <span :class="traceData.nft_info.is_revoked ? 'text-danger fw-800' : 'text-success fw-800'">
                      {{ traceData.nft_info.status }}
                    </span>
                  </div>
                  <div class="d-flex justify-content-between mb-2 border-bottom pb-2">
                    <span class="text-muted fw-600">Token ID</span>
                    <span class="badge bg-info-subtle text-info fw-800">#{{ traceData.nft_info.token_id }}</span>
                  </div>
                  <div class="d-flex justify-content-between mb-2 border-bottom pb-2">
                    <span class="text-muted fw-600">Sinh viên</span>
                    <span class="fw-800 text-dark">{{ traceData.diploma_info.student }}</span>
                  </div>
                  
                  <div v-if="traceData.nft_info.is_revoked && traceData.revocation_info" class="mt-3 alert alert-danger border-0 rounded-3 small py-2">
                    <strong>LÝ DO THU HỒI:</strong> {{ traceData.revocation_info.reason }}
                    <div class="mt-1 small opacity-75">Thời gian: {{ traceData.revocation_info.revoked_at }}</div>
                  </div>

                  <div v-if="!traceData.nft_info.is_revoked" class="mt-3">
                    <div v-if="traceData.integrity.is_tampered" class="alert alert-warning border-0 rounded-3 small py-2">
                      <i class="bi bi-exclamation-triangle-fill me-1"></i> CẢNH BÁO: Dữ liệu hiện tại có sự sai lệch so với bản gốc On-chain!
                    </div>
                    <div v-else class="alert alert-success border-0 rounded-3 small py-2">
                      <i class="bi bi-shield-check-fill me-1"></i> Dữ liệu khớp hoàn toàn với Blockchain.
                    </div>
                  </div>
                </div>
                <div class="mt-4 d-flex gap-2">
                  <a :href="traceData.blockchain_proof.etherscan" target="_blank" class="btn btn-dark flex-fill fw-700 rounded-pill py-2">
                    <i class="bi bi-search me-1"></i> Etherscan
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import baseRequestAdmin from "../../../core/baseRequestAdmin";

export default {
  name: "QuanLyBangDiem",
  data() {
    return {
      danhSach: [],
      danhSachSinhVien: [],
      danhSachLopHoc: [],
      dangTai: false,
      dangLuu: false,
      laCapNhat: false,
      tuKhoaTimKiem: "",
      namHocDaChon: "",
      idLopDaChon: "",
      danhSachMonHoc: [],
      idMonHocTam: "",
      tuKhoaMonHoc: "",
      tuKhoaLopHoc: "",
      tuKhoaSinhVien: "",
      duLieuForm: {
        id: null,
        sinh_vien_id: "",
        lop_hoc_id: "",
        diem_qua_trinh: 0,
        diem_cuoi_ky: 0,
        diem_tong_ket: 0,
        diem_he_4: 0,
        diem_chu: "",
        ngay_vao_diem: "",
        trang_thai: 0,
        is_locked: false
      },
      itemDuocChon: null,
      itemXoa: null,
      instanceModal: null,
      instanceModalChiTiet: null,
      instanceModalXoa: null,
      instanceModalTrace: null,
      traceData: null,
      dangTaiTrace: false,
      trangHienTai: 1,
      soBanGhiTrenTrang: 10
    };
  },
  watch: {
    namHocDaChon() { this.trangHienTai = 1; },
    idLopDaChon() { this.trangHienTai = 1; },
    tuKhoaTimKiem() { this.trangHienTai = 1; },
    idMonHocTam() { this.tuKhoaLopHoc = ""; }
  },
  computed: {
    cacNamHocDuyNhat() {
      const years = this.danhSachLopHoc.map(lh => lh.nam_hoc).filter(y => y);
      return [...new Set(years)].sort((a, b) => b.localeCompare(a));
    },
    cacLopDaLoc() {
      if (!this.namHocDaChon) return this.danhSachLopHoc;
      return this.danhSachLopHoc.filter(lh => lh.nam_hoc === this.namHocDaChon);
    },
    danhSachHienThi() {
      let res = this.danhSach;
      if (this.idLopDaChon) {
        res = res.filter(item => item.lop_hoc_id == this.idLopDaChon);
      } else if (this.namHocDaChon) {
        const classIdsInYear = this.cacLopDaLoc.map(c => c.id);
        res = res.filter(item => classIdsInYear.includes(item.lop_hoc_id));
      }
      if (this.tuKhoaTimKiem) {
        const kw = this.tuKhoaTimKiem.toLowerCase();
        res = res.filter(item => 
          item.sinh_vien?.ho_ten?.toLowerCase().includes(kw) ||
          item.sinh_vien?.ma_sinh_vien?.toLowerCase().includes(kw)
        );
      }
      return res;
    },
    danhSachPhanTrang() {
      const start = (this.trangHienTai - 1) * this.soBanGhiTrenTrang;
      return this.danhSachHienThi.slice(start, start + this.soBanGhiTrenTrang);
    },
    tongSoTrang() {
      return Math.ceil(this.danhSachHienThi.length / this.soBanGhiTrenTrang);
    },
    cacTrangHienThi() {
      const current = this.trangHienTai;
      const total = this.tongSoTrang;
      if (total <= 3) return Array.from({ length: total }, (_, i) => i + 1);
      if (current === 1) return [1, 2, 3];
      if (current === total) return [total - 2, total - 1, total];
      return [current - 1, current, current + 1];
    },
    cacLopTheoMon() {
      if (!this.idMonHocTam) return [];
      return this.danhSachLopHoc.filter(lh => 
          lh.mon_hoc_id == this.idMonHocTam && 
          lh.trang_thai === 'da_ket_thuc'
      );
    },
    danhSachMonHocLoc() {
      if (!this.tuKhoaMonHoc) return this.danhSachMonHoc;
      const kw = this.tuKhoaMonHoc.toLowerCase();
      return this.danhSachMonHoc.filter(m => 
          m.ten_mon_hoc?.toLowerCase().includes(kw) || 
          m.ma_mon_hoc?.toLowerCase().includes(kw)
      );
    },
    danhSachLopHocLoc() {
      const base = this.laCapNhat ? this.danhSachLopHoc : this.cacLopTheoMon;
      if (!this.tuKhoaLopHoc) return base;
      const kw = this.tuKhoaLopHoc.toLowerCase();
      return base.filter(lh => 
          lh.ten_lop_hoc?.toLowerCase().includes(kw) || 
          lh.ma_lop_hoc?.toLowerCase().includes(kw)
      );
    },
    danhSachSinhVienLoc() {
      if (!this.tuKhoaSinhVien) return this.danhSachSinhVien;
      const kw = this.tuKhoaSinhVien.toLowerCase();
      return this.danhSachSinhVien.filter(sv => 
          sv.ho_ten?.toLowerCase().includes(kw) || 
          sv.ma_sinh_vien?.toLowerCase().includes(kw)
      );
    }
  },
  mounted() {
    this.layDuLieu();
    this.laySinhVien();
    this.layLopHoc();
    this.layMonHoc();
    if (window.bootstrap) {
      this.instanceModal = new window.bootstrap.Modal(this.$refs.refsModal);
      this.instanceModalChiTiet = new window.bootstrap.Modal(this.$refs.refsModalChiTiet);
      this.instanceModalXoa = new window.bootstrap.Modal(this.$refs.modalElementXoa);
      this.instanceModalTrace = new window.bootstrap.Modal(this.$refs.refsModalTrace);
    }
  },
  methods: {
    layLopDiem(diem) {
      const d = Number(diem) || 0;
      if (d >= 8.5) return 'score-excellent';
      if (d >= 7.0) return 'score-good';
      if (d >= 5.0) return 'score-average';
      return 'score-poor';
    },
    layLopChuDiem(diem) {
      const d = Number(diem) || 0;
      if (d >= 8.5) return 'text-success';
      if (d >= 7.0) return 'text-primary';
      if (d >= 5.0) return 'text-warning';
      return 'text-danger';
    },
    layTenTrangThai(trangThai) {
      if (trangThai == 1) return 'Đã Đúc NFT';
      if (trangThai == 2) return 'Chờ Duyệt';
      return 'Chưa Đúc NFT';
    },
    layLopTrangThai(trangThai) {
      if (trangThai == 1) return 'bg-success-subtle text-success border border-success';
      if (trangThai == 2) return 'bg-warning-subtle text-warning border border-warning';
      return 'bg-secondary-subtle text-secondary border border-secondary';
    },
    layDuLieu() {
      this.dangTai = true;
      baseRequestAdmin.get("bang-diems/get-data")
        .then(res => {
          this.danhSach = res.data.list || res.data.data || res.data;
        })
        .catch(err => {
          console.error("Lỗi lấy data bảng điểm:", err);
          this.$toast.error("Không thể tải danh sách bảng điểm!");
        })
        .finally(() => {
          this.dangTai = false;
        });
    },
    laySinhVien() {
      baseRequestAdmin.get("sinh-viens/get-data")
        .then(res => {
          this.danhSachSinhVien = res.data.data || [];
        })
        .catch(err => console.error(err));
    },
    layLopHoc() {
      baseRequestAdmin.get("lop-hocs/get-data")
        .then(res => {
          this.danhSachLopHoc = res.data.data || [];
        })
        .catch(err => console.error(err));
    },
    layMonHoc() {
      baseRequestAdmin.get("mon-hocs/get-data")
        .then(res => {
          this.danhSachMonHoc = res.data.data || [];
        })
        .catch(err => console.error(err));
    },
    moModalThem() {
      this.laCapNhat = false;
      this.idMonHocTam = "";
      this.tuKhoaMonHoc = "";
      this.tuKhoaLopHoc = "";
      this.tuKhoaSinhVien = "";
      this.duLieuForm = {
        id: null, sinh_vien_id: "", lop_hoc_id: "",
        diem_qua_trinh: 0, diem_cuoi_ky: 0,
        trang_thai: 0, is_locked: false
      };
      this.instanceModal.show();
    },
    moModalSua(item) {
      this.laCapNhat = true;
      this.duLieuForm = { ...item };
      this.idMonHocTam = item.lop_hoc?.mon_hoc_id || "";
      this.instanceModal.show();
    },
    moModalChiTiet(item) {
      this.itemDuocChon = item;
      this.instanceModalChiTiet.show();
    },
    xuLyLuu() {
      this.dangLuu = true;
      const request = this.laCapNhat
        ? baseRequestAdmin.put(`bang-diems/update/${this.duLieuForm.id}`, this.duLieuForm)
        : baseRequestAdmin.post("bang-diems/create", this.duLieuForm);

      request
        .then(res => {
          if (res.data.status) {
            this.$toast.success(res.data.message);
            this.instanceModal.hide();
            this.layDuLieu();
          } else {
            this.$toast.error(res.data.message);
          }
        })
        .catch(err => {
          console.error(err);
          const listErr = err.response?.data?.errors;
          if (listErr) {
            Object.values(listErr).forEach((error) => {
              this.$toast.error(error[0]);
            });
          } else {
            this.$toast.error("Lỗi hệ thống khi lưu bảng điểm!");
          }
        })
        .finally(() => {
          this.dangLuu = false;
        });
    },
    xuLyXoa(item) {
      this.itemXoa = item;
      this.instanceModalXoa.show();
    },
    xacNhanXoa() {
      if (!this.itemXoa) return;
      this.dangTai = true;
      baseRequestAdmin.delete(`bang-diems/delete/${this.itemXoa.id}`)
        .then(res => {
          if (res.data.status) {
            this.$toast.success(res.data.message);
            this.instanceModalXoa.hide();
            this.layDuLieu();
          } else {
            this.$toast.error(res.data.message);
          }
        })
        .catch(err => {
          console.error(err);
          this.$toast.error("Lỗi khi xóa bảng điểm!");
        })
        .finally(() => {
          this.dangTai = false;
        });
    },
    moModalTrace(item) {
      const tokenId = item.nft_van_bang?.token_id;
      if (!tokenId && tokenId !== 0) {
        this.$toast.warning("Hồ sơ này chưa được cấp Token ID!");
        return;
      }
      this.dangTaiTrace = true;
      this.traceData = null;
      this.instanceModalTrace.show();

      baseRequestAdmin.get(`nft/trace/${tokenId}`)
        .then(res => {
          if (res.data.status) {
            this.traceData = res.data.data;
          } else {
            this.$toast.error(res.data.message);
            this.instanceModalTrace.hide();
          }
        })
        .catch(err => {
          console.error(err);
          this.$toast.error("Lỗi khi truy vết Blockchain!");
          this.instanceModalTrace.hide();
        })
        .finally(() => {
          this.dangTaiTrace = false;
        });
    }
  }
};
</script>

<style scoped>
.btn-rose-v2 {
  background: var(--primary-darker);
  color: #fff;
  border: none;
}

.bg-rose-v2 {
  background: linear-gradient(135deg, var(--primary-darker), #BE123C);
}

.btn-rose-v2:hover {
  background: #E11D48;
  color: #fff;
}

.btn-action {
  width: 34px;
  height: 34px;
  padding: 0;
  background: #fff;
  border: 1px solid var(--border-color);
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}

.btn-action:hover {
  transform: translateY(-2px);
  background: var(--primary);
}

.flux-input {
  border-radius: 10px;
  border: 1px solid var(--border-color);
  padding: 10px 14px;
  font-size: 13.5px;
}

.flux-input:focus {
  border-color: var(--primary-darker);
  box-shadow: 0 0 0 3px rgba(244, 63, 94, 0.1);
}

.score-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  font-weight: 800;
  font-size: 14px;
}

.score-excellent {
  background: rgba(16, 185, 129, 0.15);
  color: #059669;
}

.score-good {
  background: rgba(59, 130, 246, 0.15);
  color: #2563EB;
}

.score-average {
  background: rgba(245, 158, 11, 0.15);
  color: #D97706;
}

.score-poor {
  background: rgba(239, 68, 68, 0.15);
  color: #DC2626;
}

.text-rose-dark {
  color: var(--primary-darker);
}

.fw-800 {
  font-weight: 800;
}

.fw-600 {
  font-weight: 600;
}

.fw-700 {
  font-weight: 700;
}

.detail-group {
  padding: 15px;
  background: #f8fafc;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
  height: 100%;
}
</style>
