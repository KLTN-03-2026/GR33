<template>
    <div class="mon-hoc-view px-2">
        <!-- Page Header -->
        <div class="page-header d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="page-title text-accent fw-800">Tra cứu môn học</h3>
                <p class="page-subtitle text-muted">Tra cứu thông tin chi tiết về các học phần và theo dõi tiến độ học tập cá nhân.</p>
            </div>
            <button class="btn btn-light-pink shadow-sm" @click="layDuLieu">
                <i class="bi bi-arrow-clockwise me-1"></i> Làm mới
            </button>
        </div>

        <!-- Search & Filter Area -->
        <div class="row g-3 mb-4">
            <div class="col-md-8">
                <div class="filter-card p-3 bg-white shadow-sm h-100" style="border-radius: 16px;">
                    <div class="navbar-search m-0 w-100">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" v-model="tuKhoa" placeholder="Tìm kiếm theo tên môn hoặc mã môn học..." />
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="filter-card p-3 bg-white shadow-sm h-100" style="border-radius: 16px;">
                    <select class="form-select border-0 fw-600 text-muted" v-model="locTrangThai">
                        <option value="-1">Tất cả trạng thái</option>
                        <option value="0">Chưa học</option>
                        <option value="1">Đang học</option>
                        <option value="2">Đã học xong</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Subjects Grid -->
        <div class="row g-4">
            <template v-if="dangTai">
                <div class="col-12 text-center py-5">
                    <div class="spinner-border text-accent" role="status"></div>
                </div>
            </template>
            <template v-else-if="danhSachLoc.length === 0">
                <div class="col-12 text-center py-5">
                    <div class="empty-state">
                        <i class="bi bi-book-half display-1 opacity-25 text-muted"></i>
                        <p class="mt-3 text-muted fw-600">Không tìm thấy môn học phù hợp.</p>
                    </div>
                </div>
            </template>
            <template v-else v-for="item in danhSachLoc" :key="item.id">
                <div class="col-md-6 col-lg-4 col-xl-3">
                    <div class="subject-card h-100 p-4 bg-white shadow-sm border-0 position-relative d-flex flex-column" style="border-radius: 20px;">
                        <!-- Status Badge -->
                        <div class="status-badge-wrap position-absolute" style="top: 15px; right: 15px;">
                            <span v-if="item.trang_thai_hoc == 0" class="badge bg-light text-muted border fw-700">Chưa học</span>
                            <span v-else-if="item.trang_thai_hoc == 1" class="badge bg-info-subtle text-info border border-info-subtle fw-700">Đang học</span>
                            <span v-else class="badge bg-success-subtle text-success border border-success-subtle fw-700">Đã học</span>
                        </div>

                        <div class="subject-icon bg-pink-subtle text-accent mb-3">
                            <i class="bi bi-journal-text"></i>
                        </div>
                        <div class="subject-code small fw-800 text-muted text-uppercase mb-1">{{ item.ma_mon_hoc }}</div>
                        <h5 class="subject-name fw-800 text-dark mb-3 pe-5">{{ item.ten_mon_hoc }}</h5>
                        
                        <div class="subject-info d-flex align-items-center gap-3 mt-auto pt-3 border-top">
                            <div class="info-item">
                                <span class="label small text-muted d-block">Tín chỉ</span>
                                <span class="value fw-700 text-accent">{{ item.so_tin_chi }} TC</span>
                            </div>

                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>

<script>
import baseRequestSinhVien from "../../../core/baseRequestSinhVien";

export default {
    name: "SinhVienMonHoc",
    data() {
        return {
            danhSach: [],
            dangTai: false,
            tuKhoa: "",
            locTrangThai: "-1"
        };
    },
    computed: {
        danhSachLoc() {
            let res = this.danhSach;
            
            if (this.locTrangThai !== "-1") {
                res = res.filter(m => m.trang_thai_hoc == this.locTrangThai);
            }

            if (this.tuKhoa) {
                const kw = this.tuKhoa.toLowerCase();
                res = res.filter(m => 
                    m.ten_mon_hoc.toLowerCase().includes(kw) || 
                    m.ma_mon_hoc.toLowerCase().includes(kw)
                );
            }

            return res;
        }
    },
    mounted() {
        this.layDuLieu();
    },
    methods: {
        layDuLieu() {
            this.dangTai = true;
            baseRequestSinhVien.get("mon-hocs")
                .then(res => {
                    this.danhSach = res.data.data || [];
                })
                .catch(err => {
                    this.$toast.error("Lỗi lấy danh sách môn học!");
                })
                .finally(() => {
                    this.dangTai = false;
                });
        }
    }
};
</script>

<style scoped>
.text-accent { color: #db2777; }
.bg-pink-subtle { background: #fdf2f8; }
.subject-card { 
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid #fce7f3 !important;
}
.subject-card:hover { 
    transform: translateY(-8px);
    box-shadow: 0 15px 30px -5px rgba(219, 39, 119, 0.15) !important;
    border-color: #db2777 !important;
}
.subject-icon {
    width: 50px; height: 50px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.5rem;
}
.subject-name {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-height: 1.4;
    min-height: 2.8em;
}
.status-badge-wrap .badge {
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.bg-info-subtle { background-color: #e0f2fe; }
.bg-success-subtle { background-color: #dcfce7; }
.btn-light-pink {
    background: #fdf2f8; color: #db2777; border-radius: 12px;
    padding: 10px 20px; border: 1px solid #fce7f3; font-weight: 700;
}
.fw-800 { font-weight: 800; }
.fw-700 { font-weight: 700; }
.fw-600 { font-weight: 600; }
</style>
