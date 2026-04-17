<template>
    <div class="lop-hoc-management">
        <!-- Page Header -->
        <div class="page-header d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="page-title">Quản lý Lớp Học</h3>
                <p class="page-subtitle">Hệ thống phân bổ lớp học và điều phối giảng viên chủ nhiệm nội bộ.</p>
            </div>
            <button v-if="$hasPermission(34)" class="btn-new" @click="moModalThemMoi">
                <i class="bi bi-mortarboard-fill"></i> Thêm lớp học
            </button>
        </div>

        <!-- Main Content Card -->
        <div class="data-card shadow-sm border-0">
            <!-- Table Controls -->
            <div class="table-controls p-3 border-bottom bg-light-subtle">
                <div class="row g-3 align-items-center">
                    <div class="col-md-3">
                        <label class="form-label small fw-800 text-uppercase opacity-50">Năm học</label>
                        <select class="form-select flux-input" v-model="locNamHoc">
                            <option value="">Tất cả năm học</option>
                            <option v-for="year in danhSachNamHoc" :key="year" :value="year">{{ year }}</option>
                        </select>
                    </div>
                    <div class="col-md-7">
                        <label class="form-label small fw-800 text-uppercase opacity-50">Tìm kiếm</label>
                        <div class="navbar-search m-0">
                            <i class="bi bi-search search-icon"></i>
                            <input type="text" v-model="tuKhoaTimKiem" :disabled="!locNamHoc" 
                                :placeholder="locNamHoc ? 'Tìm theo mã lớp hoặc giáo viên...' : 'Vui lòng chọn năm học trước...'" />
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end justify-content-end" style="padding-top: 28px;">
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
                            <th width="120">Mã lớp</th>
                            <th>Tên lớp học</th>
                            <th>Năm học</th>
                            <th>Môn học</th>
                            <th>Giáo viên chủ nhiệm</th>
                            <th class="text-center">Sĩ số</th>
                            <th class="text-center">Trạng thái</th>
                            <th class="text-end pe-3" width="160">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="dangTai">
                            <td colspan="9" class="text-center py-5">
                                <div class="spinner-border text-rose" role="status"></div>
                            </td>
                        </tr>
                        <tr v-else-if="danhSachPhanTrang.length === 0">
                            <td colspan="9" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="bi bi-mortarboard d-block mb-2 opacity-25" style="font-size: 3rem"></i>
                                    <span class="text-muted">Không tìm thấy lớp học nào.</span>
                                </div>
                            </td>
                        </tr>
                        <tr v-else v-for="(item, index) in danhSachPhanTrang" :key="item.id">
                            <td class="fw-700 text-muted text-center">#{{ (trangHienTai - 1) * soMucMoiTrang + index + 1 }}</td>
                            <td class="fw-700 text-rose-dark">{{ item.ma_lop_hoc }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-sm flex-shrink-0"
                                        style="background: rgba(190, 18, 60, 0.08); color: var(--primary-darker);">
                                        {{ layKyTuDau(item.ten_lop_hoc) }}
                                    </div>
                                    <div class="fw-700 text-main">{{ item.ten_lop_hoc }}</div>
                                </div>
                            </td>
                            <td class="fw-600">{{ item.nam_hoc }}</td>
                            <td>
                                <div class="fw-600 text-muted small text-truncate" style="max-width: 150px;">
                                    {{ layTenMonHoc(item.mon_hoc_id) || item.monHoc?.ten_mon_hoc || '---' }}
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-sm flex-shrink-0"
                                        style="background: rgba(190, 18, 60, 0.08); color: var(--primary-darker);">
                                        {{ layKyTuDau(layTenGiangVien(item.giang_vien_id) || item.giangVien?.ho_ten) }}
                                    </div>
                                    <div class="fw-700 text-muted small text-truncate" style="max-width: 180px;">
                                        {{ layTenGiangVien(item.giang_vien_id) || item.giangVien?.ho_ten || 'Chưa phân công' }}
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge rounded-pill px-3 py-2" :class="layLopSiSo(item.si_so)">
                                    {{ item.si_so != null ? item.si_so : 0 }} / 40
                                </span>
                            </td>
                            <td class="text-center">
                                <span v-if="item.trang_thai === 'sap_bat_dau'" class="badge bg-warning-subtle text-warning border border-warning fw-700">Sắp bắt đầu</span>
                                <span v-else-if="item.trang_thai === 'dang_mo'" class="badge bg-success-subtle text-success border border-success fw-700">Đang mở</span>
                                <span v-else class="badge bg-secondary-subtle text-secondary border border-secondary fw-700">Đã kết thúc</span>
                            </td>
                            <td class="text-end pe-3">
                                <div class="d-flex justify-content-end gap-2">
                                    <button class="btn btn-action shadow-sm" @click="moModalChiTiet(item.id)" title="Xem chi tiết">
                                        <i class="bi bi-eye-fill text-info"></i>
                                    </button>
                                    <button v-if="$hasPermission(34)" class="btn btn-action shadow-sm" @click="moModalCapNhat(item)" title="Cập nhật">
                                        <i class="bi bi-pencil-fill text-primary-darker"></i>
                                    </button>
                                    <template v-if="$hasPermission(34)">
                                        <button v-if="item.si_so <= 0" class="btn btn-action shadow-sm" @click="xuLyXoa(item)" title="Xóa">
                                            <i class="bi bi-trash-fill text-danger"></i>
                                        </button>
                                        <button v-else class="btn btn-action shadow-sm opacity-50 cursor-not-allowed" disabled title="Không thể xóa lớp học đã có sinh viên">
                                            <i class="bi bi-trash-fill text-muted"></i>
                                        </button>
                                    </template>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Footer -->
            <div class="table-footer d-flex flex-column flex-md-row justify-content-between align-items-center p-3 border-top mt-3"
                v-if="tongSoTrang > 1">
                <span class="small text-muted mb-3 mb-md-0">
                    Hiển thị <b>{{ danhSachPhanTrang.length > 0 ? (trangHienTai - 1) * soMucMoiTrang + 1 : 0 }}</b>
                    - <b>{{ Math.min(trangHienTai * soMucMoiTrang, danhSachLoc.length) }}</b>
                    trong tổng số <b>{{ danhSachLoc.length }}</b> lớp học
                </span>
                <nav>
                    <ul class="pagination pagination-sm m-0 gap-1">
                        <li class="page-item" :class="{ disabled: trangHienTai === 1 }">
                            <a class="page-link border-0 rounded-circle" href="#" @click.prevent="trangHienTai--">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                        <li class="page-item" v-for="p in cacTrangHienThi" :key="p"
                            :class="{ active: trangHienTai === p }">
                            <a class="page-link border-0 rounded-circle shadow-sm" href="#"
                                @click.prevent="trangHienTai = p">{{ p }}</a>
                        </li>
                        <li class="page-item" :class="{ disabled: trangHienTai === tongSoTrang }">
                            <a class="page-link border-0 rounded-circle" href="#" @click.prevent="trangHienTai++">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <!-- Modals -->
        <!-- Create/Edit Modal -->
        <div class="modal fade" id="lopHocModal" tabindex="-1" aria-hidden="true" ref="modalEle">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden">
                    <div class="modal-header border-0 bg-rose-v2 text-white p-4">
                        <h5 class="modal-title fw-800">{{ laCapNhat ? 'Cập nhật thông tin lớp' : 'Tạo mới lớp học' }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <form @submit.prevent="xuLyLuu">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-800 small text-uppercase text-muted">Mã lớp học</label>
                                    <input type="text" class="form-control flux-input"
                                        v-model="duLieuBieuMau.ma_lop_hoc" placeholder="Ví dụ: LHCSDLA1">
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label fw-800 small text-uppercase text-muted">Tên lớp học</label>
                                    <input type="text" class="form-control flux-input"
                                        v-model="duLieuBieuMau.ten_lop_hoc"
                                        placeholder="Ví dụ: Lớp Cơ Sở Dữ Liệu">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-800 small text-uppercase text-muted">Môn học</label>
                                    <div class="navbar-search mb-2 w-100" style="max-width: 100%;">
                                        <i class="bi bi-search search-icon"></i>
                                        <input type="text" v-model="tuKhoaTimKiemMonHoc" placeholder="Tìm theo tên hoặc mã môn học..." />
                                    </div>
                                    <select class="form-select flux-input" v-model="duLieuBieuMau.mon_hoc_id">
                                        <option value="" disabled>Chọn môn học...</option>
                                        <option v-for="m in danhSachMonHocLoc" :key="m.id" :value="m.id">{{ m.ten_mon_hoc
                                            }}</option>
                                    </select>
                                    <div class="small text-muted mt-1" v-if="tuKhoaTimKiemMonHoc && danhSachMonHocLoc.length === 0">
                                        Không tìm thấy môn học nào khớp.
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-800 small text-uppercase text-muted">Giảng viên chủ nhiệm</label>
                                    <select class="form-select flux-input" v-model="duLieuBieuMau.giang_vien_id">
                                        <option value="" disabled>Chọn giảng viên...</option>
                                        <option v-for="g in danhSachGiangVien" :key="g.id" :value="g.id">{{ g.ho_ten }}</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-800 small text-uppercase text-muted">Năm học</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="number" class="form-control flux-input" v-model="namBatDau" placeholder="Năm bắt đầu">
                                        <span class="fw-bold">-</span>
                                        <input type="number" class="form-control flux-input" v-model="namKetThuc" placeholder="Năm kết thúc">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-800 small text-uppercase text-muted">Học kỳ</label>
                                    <select class="form-select flux-input" v-model="duLieuBieuMau.hoc_ky">
                                        <option value="1">Học kỳ 1</option>
                                        <option value="2">Học kỳ 2</option>
                                        <option value="3">Học kỳ hè</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-800 small text-uppercase text-muted">Trạng thái</label>
                                    <select class="form-select flux-input" v-model="duLieuBieuMau.trang_thai">
                                        <option value="sap_bat_dau">Sắp bắt đầu</option>
                                        <option value="dang_mo">Đang mở</option>
                                        <option value="da_ket_thuc">Đã kết thúc</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mt-4 pt-2 d-flex gap-3">
                                <button type="button" class="btn btn-light border px-4 flex-fill fw-600"
                                    data-bs-dismiss="modal">Hủy bỏ</button>
                                <button type="submit" class="btn btn-rose-v2 px-4 flex-fill fw-700 shadow-sm"
                                    :disabled="dangLuu">
                                    <span v-if="dangLuu" class="spinner-border spinner-border-sm me-1"></span>
                                    {{ laCapNhat ? 'Cập nhật ngay' : 'Xác nhận tạo' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div class="modal fade" id="chiTietLopHocModal" tabindex="-1" aria-hidden="true" ref="modalChiTietEle">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden">
          <div class="modal-header border-0 bg-rose-v2 text-white p-4">
            <h5 class="modal-title fw-800">Chi tiết lớp học</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-0">
            <div v-if="duLieuChiTiet">
              <div class="p-4 border-bottom bg-light-subtle">
                <div class="d-flex align-items-center gap-3">
                  <div class="bg-rose-v2 text-white rounded-4 d-flex align-items-center justify-content-center shadow-sm" style="width: 56px; height: 56px;">
                    <i class="bi bi-mortarboard fs-3"></i>
                  </div>
                  <div>
                    <h4 class="fw-800 text-dark mb-1">{{ duLieuChiTiet.ten_lop_hoc }}</h4>
                    <div class="d-flex gap-2">
                      <span class="badge bg-rose-subtle text-dark rounded-pill px-3">{{ duLieuChiTiet.ma_lop_hoc }}</span>
                      <span class="badge bg-light text-muted border rounded-pill px-3"><i class="bi bi-calendar3 me-1"></i> {{ duLieuChiTiet.nam_hoc }} - Học kỳ {{ duLieuChiTiet.hoc_ky }}</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="p-4">
                <div class="row g-3">
                  <div class="col-sm-6">
                    <div class="d-flex align-items-center p-3 border rounded-3 bg-white h-100 shadow-sm">
                      <div class="flex-shrink-0 me-3 bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-book fs-5"></i>
                      </div>
                      <div class="overflow-hidden">
                        <div class="small text-muted text-uppercase fw-bold mb-1" style="font-size: 0.75rem;">Môn học</div>
                        <div class="fw-bold text-dark mb-0 fs-6 text-truncate" :title="layTenMonHoc(duLieuChiTiet.mon_hoc_id) || duLieuChiTiet.monHoc?.ten_mon_hoc || '---'">
                            {{ layTenMonHoc(duLieuChiTiet.mon_hoc_id) || duLieuChiTiet.monHoc?.ten_mon_hoc || '---' }}
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="d-flex align-items-center p-3 border rounded-3 bg-white h-100 shadow-sm">
                      <div class="flex-shrink-0 me-3 bg-info-subtle text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-person-video3 fs-5"></i>
                      </div>
                      <div class="overflow-hidden">
                        <div class="small text-muted text-uppercase fw-bold mb-1" style="font-size: 0.75rem;">Giảng viên</div>
                        <div class="fw-bold text-dark mb-0 fs-6 text-truncate" :title="layTenGiangVien(duLieuChiTiet.giang_vien_id) || duLieuChiTiet.giangVien?.ho_ten || '---'">
                            {{ layTenGiangVien(duLieuChiTiet.giang_vien_id) || duLieuChiTiet.giangVien?.ho_ten || '---' }}
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="d-flex align-items-center p-3 border rounded-3 bg-white h-100 shadow-sm">
                      <div class="flex-shrink-0 me-3 bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-people fs-5"></i>
                      </div>
                      <div>
                        <div class="small text-muted text-uppercase fw-bold mb-1" style="font-size: 0.75rem;">Sĩ số</div>
                        <div class="fw-bold text-dark mb-0 fs-6">{{ duLieuChiTiet.si_so != null ? duLieuChiTiet.si_so : 0 }} / 40 sinh viên</div>
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="d-flex align-items-center p-3 border rounded-3 bg-white h-100 shadow-sm">
                      <div class="flex-shrink-0 me-3 bg-warning-subtle text-warning-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-activity fs-5"></i>
                      </div>
                      <div>
                        <div class="small text-muted text-uppercase fw-bold mb-1" style="font-size: 0.75rem;">Trạng thái</div>
                        <div class="fw-bold text-dark mb-0 fs-6">
                          <span v-if="duLieuChiTiet.trang_thai === 'sap_bat_dau'" class="badge bg-warning">Sắp bắt đầu</span>
                          <span v-else-if="duLieuChiTiet.trang_thai === 'dang_mo'" class="badge bg-success">Đang mở</span>
                          <span v-else class="badge bg-secondary">Đã kết thúc</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div v-else class="text-center py-5">
              <div class="spinner-border text-info" role="status"></div>
            </div>
          </div>
          <div class="modal-footer border-0 p-4 pt-0">
            <button type="button" class="btn btn-light border px-4 pb-2 pt-2 fw-600" data-bs-dismiss="modal">Đóng</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteLopHocModal" tabindex="-1" aria-hidden="true" ref="modalElementXoa">
        <div class="modal-dialog modal-dialog-centered modal-sm" style="max-width: 400px;">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-body p-4 text-center">
                    <div class="mb-3">
                        <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 3rem;"></i>
                    </div>
                    <h4 class="fw-800 text-dark mb-2">Xác nhận xóa?</h4>
                    <p class="text-muted mb-4">Bạn có chắc chắn muốn xóa lớp học <b>{{ itemXoa?.ten_lop_hoc }}</b> ({{ itemXoa?.ma_lop_hoc }})? Hành động này không thể hoàn tác.</p>
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
</template>

<script>
import baseRequestAdmin from "../../../core/baseRequestAdmin";

export default {
    name: "AdminLopHoc",
    data() {
        return {
            danhSach: [],
            danhSachMonHoc: [],
            danhSachGiangVien: [],
            dangTai: false,
            dangLuu: false,
            tuKhoaTimKiem: "",
            tuKhoaTimKiemMonHoc: "",
            locNamHoc: "",
            namBatDau: 2025,
            namKetThuc: 2026,
            trangHienTai: 1,
            soMucMoiTrang: 10,
            laCapNhat: false,
            duLieuBieuMau: {
                id: null,
                ma_lop_hoc: "",
                ten_lop_hoc: "",
                mon_hoc_id: "",
                giang_vien_id: "",
                nam_hoc: "",
                hoc_ky: 1,
                trang_thai: "sap_bat_dau"
            },
            duLieuChiTiet: null,
            itemXoa: null,
            modal: null,
            modalChiTiet: null,
            instanceModalXoa: null
        };
    },
    computed: {
        danhSachMonHocLoc() {
            if (!this.tuKhoaTimKiemMonHoc) return this.danhSachMonHoc;
            const kw = this.tuKhoaTimKiemMonHoc.toLowerCase();
            return this.danhSachMonHoc.filter(m =>
                m.ten_mon_hoc?.toLowerCase().includes(kw) ||
                m.ma_mon_hoc?.toLowerCase().includes(kw)
            );
        },
        danhSachNamHoc() {
            const years = this.danhSach.map(lh => lh.nam_hoc).filter(y => y);
            return [...new Set(years)].sort((a, b) => b.localeCompare(a));
        },
        danhSachLoc() {
            let res = this.danhSach;
            if (this.locNamHoc) {
                res = res.filter(item => item.nam_hoc === this.locNamHoc);
                
                if (this.tuKhoaTimKiem) {
                    const kw = this.tuKhoaTimKiem.toLowerCase();
                    res = res.filter(i =>
                        i.ma_lop_hoc.toLowerCase().includes(kw) ||
                        i.ten_lop_hoc.toLowerCase().includes(kw) ||
                        i.giangVien?.ho_ten?.toLowerCase().includes(kw)
                    );
                }
            } else if (this.tuKhoaTimKiem) {
                // If keywords exist but no year selected, we follow "chọn năm học rồi mới tìm kiếm"
                // However, since the input is disabled, this case shouldn't happen much.
                // We'll return full list but not filter by keyword yet.
                return res;
            }
            return res;
        },
        danhSachPhanTrang() {
            const s = (this.trangHienTai - 1) * this.soMucMoiTrang;
            return this.danhSachLoc.slice(s, s + this.soMucMoiTrang);
        },
        tongSoTrang() {
            return Math.ceil(this.danhSachLoc.length / this.soMucMoiTrang);
        },
        cacTrangHienThi() {
            const hienTai = this.trangHienTai;
            const tong = this.tongSoTrang;
            if (tong <= 3) return Array.from({ length: tong }, (_, i) => i + 1);
            if (hienTai === 1) return [1, 2, 3];
            if (hienTai === tong) return [tong - 2, tong - 1, tong];
            return [hienTai - 1, hienTai, hienTai + 1];
        }
    },
    watch: {
        tuKhoaTimKiem() { this.trangHienTai = 1; },
        locNamHoc() { this.trangHienTai = 1; }
    },
    mounted() {
        this.layDuLieu();
        this.layMonHoc();
        this.layGiangVien();
        if (window.bootstrap) {
            this.modal = new window.bootstrap.Modal(this.$refs.modalEle);
            this.modalChiTiet = new window.bootstrap.Modal(this.$refs.modalChiTietEle);
            this.instanceModalXoa = new window.bootstrap.Modal(this.$refs.modalElementXoa);
        }
    },
    methods: {
        layDuLieu() {
            this.dangTai = true;
            baseRequestAdmin.get("lop-hocs/get-data")
                .then(res => this.danhSach = res.data.data || [])
                .catch(err => console.error(err))
                .finally(() => this.dangTai = false);
        },
        layMonHoc() {
            baseRequestAdmin.get("mon-hocs/get-data")
                .then(res => this.danhSachMonHoc = res.data.list || res.data.data || [])
                .catch(err => console.error(err));
        },
        layGiangVien() {
            baseRequestAdmin.get("nhan-viens/get-data")
                .then(res => {
                    const all = res.data.data || [];
                    this.danhSachGiangVien = all.filter(n => n.chuc_vu_id == 6);
                })
                .catch(err => console.error(err));
        },
        moModalThemMoi() {
            this.laCapNhat = false;
            this.tuKhoaTimKiemMonHoc = "";
            this.namBatDau = new Date().getFullYear();
            this.namKetThuc = this.namBatDau + 1;
            this.duLieuBieuMau = { ma_lop_hoc: "", ten_lop_hoc: "", mon_hoc_id: "", giang_vien_id: "", nam_hoc: "", hoc_ky: 1, trang_thai: "sap_bat_dau" };
            this.modal.show();
        },
        moModalCapNhat(item) {
            this.laCapNhat = true;
            this.tuKhoaTimKiemMonHoc = "";
            this.duLieuBieuMau = { ...item };
            if (item.nam_hoc && item.nam_hoc.includes('-')) {
                const parts = item.nam_hoc.split('-');
                this.namBatDau = parts[0];
                this.namKetThuc = parts[1];
            }
            this.modal.show();
        },
        moModalChiTiet(id) {
            this.duLieuChiTiet = null;
            this.modalChiTiet.show();
            baseRequestAdmin.get(`lop-hocs/detail/${id}`)
                .then(res => {
                    if (res.data.status) {
                        this.duLieuChiTiet = res.data.data;
                    } else {
                        this.$toast.error(res.data.message);
                        this.modalChiTiet.hide();
                    }
                })
                .catch((err) => {
                    this.modalChiTiet.hide();
                    const listErr = err.response.data.errors;
                    Object.values(listErr).forEach((error) => {
                        this.$toast.error(error[0]);
                    });
                });
        },
        xuLyLuu() {
            if (Number(this.namBatDau) >= Number(this.namKetThuc)) {
                this.$toast.error("Năm bắt đầu phải nhỏ hơn năm kết thúc");
                return;
            }
            this.dangLuu = true;
            this.duLieuBieuMau.nam_hoc = `${this.namBatDau}-${this.namKetThuc}`;
            
            const processCode = this.laCapNhat
                ? baseRequestAdmin.put(`lop-hocs/update/${this.duLieuBieuMau.id}`, this.duLieuBieuMau)
                : baseRequestAdmin.post("lop-hocs/create", this.duLieuBieuMau);

            processCode
                .then((res) => {
                    if (res.data.status) {
                        this.$toast.success(res.data.message);
                        this.modal.hide();
                        this.layDuLieu();
                    } else {
                        this.$toast.error(res.data.message);
                    }
                })
                .catch((err) => {
                    const listErr = err.response.data.errors;
                    Object.values(listErr).forEach((error) => {
                        this.$toast.error(error[0]);
                    });
                })
                .finally(() => this.dangLuu = false);
        },
        xuLyXoa(item) {
            this.itemXoa = item;
            this.instanceModalXoa.show();
        },
        xacNhanXoa() {
            if (!this.itemXoa) return;
            this.dangTai = true;
            baseRequestAdmin.delete(`lop-hocs/delete/${this.itemXoa.id}`)
                .then((res) => {
                    if (res.data.status) {
                        this.$toast.success(res.data.message);
                        this.instanceModalXoa.hide();
                        this.layDuLieu();
                    } else {
                        this.$toast.error(res.data.message);
                    }
                })
                .catch((err) => {
                    console.error(err);
                    const listErr = err.response?.data?.errors;
                    if (listErr) {
                        Object.values(listErr).forEach((error) => {
                            this.$toast.error(error[0]);
                        });
                    } else {
                        this.$toast.error("Lỗi hệ thống khi xóa lớp học!");
                    }
                })
                .finally(() => this.dangTai = false);
        },
        layKyTuDau(name) {
            if (!name) return "??";
            return name.split(' ').map(n => n[0]).join('').slice(-2).toUpperCase();
        },
        layMauNgauNhien(seed) {
            const colors = ["#BE123C", "#ec4899", "#f59e0b", "#10b981", "#3b82f6"];
            if (!seed) return colors[0];
            return colors[seed.length % colors.length];
        },
        layLopSiSo(count) {
            const num = Number(count) || 0;
            if (num > 30) return "bg-danger-subtle text-danger border border-danger";
            if (num >= 0 && num <= 30) return "bg-success-subtle text-success border border-success";
            return "bg-light text-muted border";
        },
        layTenMonHoc(id) {
            const mh = this.danhSachMonHoc.find(m => m.id === id);
            return mh ? mh.ten_mon_hoc : null;
        },
        layTenGiangVien(id) {
            const gv = this.danhSachGiangVien.find(g => g.id === id);
            return gv ? gv.ho_ten : null;
        }
    }
};
</script>
