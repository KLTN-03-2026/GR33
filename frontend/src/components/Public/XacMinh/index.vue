<template>
    <div class="verify-container">
        <div v-if="loading" class="loading-overlay">
            <div class="spinner"></div>
            <p>Đang xác thực dữ liệu trên Blockchain...</p>
        </div>

        <div v-else-if="error" class="error-card shadow-lg">
            <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
            <h2>Không tìm thấy dữ liệu</h2>
            <p>{{ error }}</p>
            <router-link to="/" class="btn btn-primary mt-3">Quay lại trang chủ</router-link>
        </div>

        <div v-else class="certificate-wrapper" id="certificate-content">
            <!-- Header Certificate -->
            <div class="cert-header">
                <div class="cert-logo">
                    <img src="/main_logo.jpg" alt="Logo">
                </div>
                <div class="cert-title">
                    <h1>CHỨNG NHẬN XÁC THỰC VĂN BẰNG</h1>
                    <p class="subtitle">Hệ thống hồ sơ học tập số phi tập trung (Decentralized Academic Records)</p>
                </div>
            </div>

            <!-- Status Badge -->
            <div class="status-section" :class="{ 'is-tampered': data.integrity.is_tampered }">
                <div v-if="!data.integrity.is_tampered" class="status-badge success animate__animated animate__pulse animate__infinite">
                    <i class="fas fa-check-circle"></i>
                    <span>DỮ LIỆU TOÀN VẸN - ĐÃ XÁC MINH ON-CHAIN</span>
                </div>
                <div v-else class="status-badge danger animate__animated animate__shakeX">
                    <i class="fas fa-times-circle"></i>
                    <span>CẢNH BÁO: DỮ LIỆU ĐÃ BỊ CHỈNH SỬA</span>
                </div>
            </div>

            <div class="cert-body row g-4">
                <!-- Left: Information -->
                <div class="col-md-7">
                    <div class="p-3 border rounded-3 bg-white h-100 shadow-sm">
                        <h3 class="section-title mb-4"><i class="fas fa-info-circle me-2"></i>Thông tin chi tiết</h3>
                        
                        <div class="mb-3">
                            <label class="small text-muted mb-1 d-block">Họ và tên sinh viên:</label>
                            <div class="fw-800 fs-5 text-dark border-bottom pb-1 mb-2">{{ data.nft_info.ho_ten_sinh_vien }}</div>
                        </div>

                        <div class="mb-3">
                            <label class="small text-muted mb-1 d-block">Loại văn bằng:</label>
                            <div class="fw-800 text-dark border-bottom pb-1 mb-2">{{ getTenLoai(data.nft_info.nftable_type) }}</div>
                        </div>

                        <div class="mb-3">
                            <label class="small text-muted mb-1 d-block">Tên hồ sơ học thuật:</label>
                            <div class="fw-800 text-dark border-bottom pb-1 mb-2 text-primary">
                                {{ data.nft_info.ten_ho_so }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="small text-muted mb-1 d-block">Đơn vị cấp phát:</label>
                            <div class="fw-800 text-dark border-bottom pb-1 mb-2">
                                {{ data.nft_info.don_vi_cap }}
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="small text-muted mb-1 d-block">Thời gian ký xác thực:</label>
                            <div class="fw-800 text-dark">{{ formatDate(data.nft_info.ngay_ky) }}</div>
                        </div>
                    </div>
                </div>

                <!-- Right: QR and Action -->
                <div class="col-md-5">
                    <div class="d-flex flex-column align-items-center justify-content-center p-4 bg-light shadow-sm rounded-3 h-100">
                        <div class="qr-container mb-3 border p-2 bg-white rounded shadow-sm">
                            <qrcode-vue 
                                :value="currentUrl" 
                                :size="180" 
                                level="H" 
                                render-as="canvas"
                                :image-settings="{
                                    src: '/main_logo.jpg',
                                    width: 40,
                                    height: 40,
                                    excavate: true
                                }"
                            />
                        </div>
                        <p class="qr-hint small text-muted text-center italic mb-4">Quét để truy cập bản gốc trực tiếp trên hệ thống DAR</p>
                        <button @click="exportPDF" class="btn w-100 no-print fw-800 py-2 btn-rose">
                            <i class="fas fa-file-pdf me-2"></i> Xuất Giấy Xác Thực (PDF)
                        </button>
                    </div>
                </div>
            </div>

            <!-- Integrity Comparison -->
            <div class="integrity-table-section mt-5">
                <h3 class="section-title"><i class="fas fa-microchip me-2"></i>Đối soát dữ liệu Blockchain</h3>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-secondary">
                            <tr class="text-center">
                                <th width="20%">Trường dữ liệu</th>
                                <th width="35%">Dữ liệu Hệ thống (Database)</th>
                                <th width="35%">Dữ liệu Blockchain (On-chain)</th>
                                <th width="10%">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="field in data.integrity.detailed_comparison" :key="field.field">
                                <td class="fw-bold px-3">{{ field.field }}</td>
                                <td class="text-break px-3 small">{{ field.off_chain }}</td>
                                <td class="text-break px-3 small">{{ field.on_chain }}</td>
                                <td class="text-center">
                                    <span v-if="field.match" class="badge bg-success-subtle text-success border border-success px-2 py-1">
                                        <i class="fas fa-check me-1"></i> Khớp
                                    </span>
                                    <span v-else class="badge bg-danger-subtle text-danger border border-danger px-2 py-1">
                                        <i class="fas fa-times me-1"></i> Sai lệch
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Blockchain Proof footer -->
            <div class="cert-footer mt-5">
                <div class="proof-box shadow-sm">
                    <p class="mb-3"><strong>Mã băm nội dung (Data Hash):</strong> <code class="small text-break text-primary">{{ data.nft_info.data_hash }}</code></p>
                    <div class="blockchain-links mt-3 border-top pt-3 d-flex justify-content-between align-items-center">
                        <a :href="'https://sepolia.etherscan.io/tx/' + data.blockchain_proof.transaction_hash" target="_blank" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-external-link-alt me-1"></i> Xem giao dịch trên Etherscan
                        </a>
                        <span class="text-muted small fw-600">Token ID: #{{ data.nft_info.token_id }} | Block: #{{ data.blockchain_proof.block_number }}</span>
                    </div>
                </div>
            </div>
            
            <div class="legal-disclaimer mt-4 text-center small text-muted italic">
                Giấy chứng nhận này được trích xuất tự động từ hệ thống DAR. Dữ liệu Blockchain là minh chứng duy nhất không thể thay đổi.
            </div>
        </div>
    </div>
</template>

<script>
import QrcodeVue from 'qrcode.vue';
import baseRequestClient from '../../../core/baseRequestClient';
import html2canvas from 'html2canvas';
import { jsPDF } from 'jspdf';

export default {
    components: {
        QrcodeVue,
    },
    data() {
        return {
            loading: true,
            data: null,
            error: null,
            currentUrl: window.location.href,
        };
    },
    mounted() {
        this.fetchData();
        if (!document.getElementById('font-awesome-css')) {
            const link = document.createElement('link');
            link.id = 'font-awesome-css';
            link.rel = 'stylesheet';
            link.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css';
            document.head.appendChild(link);
        }
    },
    methods: {
        fetchData() {
            const tokenId = this.$route.params.tokenId;
            this.loading = true;
            this.error = null;

            baseRequestClient.get(`nft/trace/${tokenId}`)
                .then(res => {
                    if (res.data.status) {
                        this.data = res.data.data;
                    } else {
                        this.error = res.data.message || "Không tìm thấy dữ liệu NFT.";
                    }
                    this.loading = false;
                })
                .catch(err => {
                    console.error("Lỗi xác thực:", err);
                    this.error = "Không thể kết nối đến hệ thống xác thực.";
                    this.loading = false;
                });
        },
        getStudentName() {
            if (!this.data || !this.data.nft_info) return 'N/A';
            const nftable = this.data.nft_info.nftable;
            return nftable?.sinh_vien?.ho_ten || nftable?.sinhVien?.ho_ten || 'N/A';
        },
        getTenLoai(type) {
            if (!type) return 'N/A';
            if (type.includes('BangDiem')) return 'Bảng điểm chính thức';
            if (type.includes('ChungChi')) return 'Chứng chỉ hành nghề/Kỹ năng';
            if (type.includes('DuAn')) return 'Dự án nghiên cứu/Thực tế';
            return 'Văn bằng số';
        },
        formatDate(dateStr) {
            if (!dateStr) return 'N/A';
            const date = new Date(dateStr);
            return isNaN(date.getTime()) ? 'N/A' : date.toLocaleString('vi-VN', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            });
        },

        async exportPDF() {
            const element = document.getElementById('certificate-content');
            const buttons = document.querySelectorAll('.no-print');
            buttons.forEach(b => b.style.display = 'none');

            try {
                const canvas = await html2canvas(element, {
                    scale: 2,
                    useCORS: true,
                    logging: false,
                    backgroundColor: '#ffffff'
                });

                const imgData = canvas.toDataURL('image/png');
                const pdf = new jsPDF('p', 'mm', 'a4');
                const imgProps = pdf.getImageProperties(imgData);
                const pdfWidth = pdf.internal.pageSize.getWidth();
                const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;

                pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
                pdf.save(`Xac-Minh-DAR-${this.data.nft_info.token_id}.pdf`);
            } catch (error) {
                console.error("Lỗi xuất PDF:", error);
            } finally {
                buttons.forEach(b => b.style.display = 'block');
            }
        }
    }
};
</script>

<style scoped>
.btn-rose {
    background-color: var(--primary-rose, #e11d48) !important;
    border-color: var(--primary-rose, #e11d48) !important;
    color: white !important;
}
.btn-rose:hover {
    background-color: #be123c !important;
    border-color: #be123c !important;
}

.verify-container {
    background: #f8f9fa;
    min-height: 100vh;
    padding: 2rem 1rem;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
}

.loading-overlay { text-align: center; margin-top: 20%; }
.spinner { width: 50px; height: 50px; border: 5px solid #f3f3f3; border-top: 5px solid #3498db; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 1rem; }
@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

.certificate-wrapper {
    background: white;
    width: 100%;
    max-width: 950px;
    padding: 3rem;
    border-radius: 4px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    border-top: 10px solid #2e3b4e;
}

.cert-header { display: flex; align-items: center; margin-bottom: 2rem; border-bottom: 1px solid #eee; padding-bottom: 2rem; }
.cert-logo img { height: 75px; margin-right: 2rem; }
.cert-title h1 { font-size: 1.7rem; font-weight: 900; color: #1a202c; margin: 0; text-transform: uppercase; }
.subtitle { color: #718096; margin-top: 0.5rem; font-size: 0.9rem; }

.status-section { margin-bottom: 2.5rem; }
.status-badge { padding: 0.75rem 1.5rem; border-radius: 4px; display: inline-flex; align-items: center; gap: 10px; font-weight: 800; }
.status-badge.success { background: #f0fff4; color: #2f855a; border: 1px solid #2f855a; }
.status-badge.danger { background: #fff5f5; color: #c53030; border: 1px solid #c53030; }

.section-title { font-size: 1.1rem; font-weight: 800; color: #2d3748; text-transform: uppercase; margin-bottom: 1.5rem; }
.fw-800 { font-weight: 800; }
.text-accent { color: #3182ce; }

.qr-container { background: #fff; padding: 10px; }
.proof-box { background: #f7fafc; border: 1px solid #e2e8f0; padding: 1.5rem; border-radius: 8px; }

.bg-success-subtle { background-color: #f0fff4; }
.bg-danger-subtle { background-color: #fff5f5; }

.error-card { background: white; padding: 3rem; border-radius: 10px; text-align: center; max-width: 500px; }

@media print { .no-print { display: none !important; } }
</style>
