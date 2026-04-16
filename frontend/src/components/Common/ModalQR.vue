<template>
    <div class="modal fade" id="modalQR" tabindex="-1" ref="modalQREle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 24px;">
                <div class="modal-header border-0 bg-rose text-white p-4" style="border-radius: 24px 24px 0 0">
                    <h5 class="modal-title fw-800"><i class="bi bi-qr-code-scan me-2"></i>Mã QR Xác thực Văn bằng</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-5 text-center bg-light-subtle">
                    <div v-if="item" class="qr-wrapper shadow-lg p-3 bg-white d-inline-block rounded-4 mb-4">
                        <qrcode-vue 
                            :value="getVerifyUrl(item)" 
                            :size="250" 
                            level="H" 
                            render-as="canvas"
                            :image-settings="{
                                src: '/main_logo.jpg',
                                width: 55,
                                height: 55,
                                excavate: true
                            }"
                        />
                    </div>
                    
                    <div v-if="item" class="info-box text-start p-3 border rounded-3 bg-white mb-4">
                        <h6 class="fw-800 text-uppercase small text-muted mb-2">Thông tin văn bằng</h6>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Token ID:</span>
                            <span class="fw-800 text-dark">#{{ item.token_id }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Sinh viên:</span>
                            <span class="fw-800 text-dark text-truncate ms-2" style="max-width: 200px;">{{ getTenSinhVien(item) }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Loại:</span>
                            <span class="badge bg-secondary-subtle text-secondary">{{ getTenLoai(item) }}</span>
                        </div>
                    </div>

                    <div class="action-hint alert alert-info border-0 rounded-4 py-3 shadow-sm">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-info-circle-fill me-2 fs-4"></i>
                            <div class="text-start small">
                                <strong>Dành cho Nhà tuyển dụng:</strong> Quét mã này để xem bằng chứng xác thực trực tiếp trên Blockchain.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0 bg-light-subtle" style="border-radius: 0 0 24px 24px">
                    <button type="button" class="btn btn-secondary flex-fill py-2 fw-700 rounded-pill" data-bs-dismiss="modal">Đóng</button>
                    <a :href="getVerifyUrl(item)" target="_blank" class="btn btn-rose flex-fill py-2 fw-700 rounded-pill shadow-sm">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Truy cập Web
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import QrcodeVue from 'qrcode.vue';

export default {
    name: "ModalQR",
    components: {
        QrcodeVue
    },
    props: {
        item: {
            type: Object,
            default: null
        }
    },
    data() {
        return {
            instance: null
        };
    },
    mounted() {
        if (window.bootstrap) {
            this.instance = new window.bootstrap.Modal(this.$refs.modalQREle);
        }
    },
    methods: {
        show() {
            if (this.instance) this.instance.show();
        },
        hide() {
            if (this.instance) this.instance.hide();
        },
        getVerifyUrl(item) {
            if (!item || (item.token_id === null && item.token_id !== 0)) return "";
            // Sử dụng URL tuyệt đối
            const origin = window.location.origin;
            return `${origin}/xac-minh-van-bang/${item.token_id}`;
        },
        getTenSinhVien(item) {
            if (!item) return "N/A";
            // Check polymorphic relation structure
            return item.nftable?.sinh_vien?.ho_ten || item.sinh_vien?.ho_ten || item.ho_ten_sinh_vien || "Sinh viên";
        },
        getTenLoai(item) {
            if (!item) return "N/A";
            const type = item.nftable_type || item.type || "";
            if (type.includes('BangDiem')) return 'Bảng điểm';
            if (type.includes('ChungChi')) return 'Chứng chỉ';
            if (type.includes('DuAn')) return 'Dự án';
            return 'Văn bằng';
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
.bg-rose {
    background-color: var(--primary-rose, #e11d48) !important;
}

.qr-wrapper {
    border: 1px solid #eee;
    transition: transform 0.3s;
}
.qr-wrapper:hover {
    transform: scale(1.02);
}
.fw-800 { font-weight: 800; }
.fw-700 { font-weight: 700; }
.bg-light-subtle { background-color: #f8fafc; }
.shadow-lg { box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1) !important; }
</style>
