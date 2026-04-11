<template>
    <div class="student-portal px-2 py-0">
        <!-- Hero Section -->
        <div class="portal-hero rounded-5 p-5 mb-5 overflow-hidden position-relative shadow-sm border-0">
            <div class="row align-items-center g-5">
                <div class="col-lg-7 position-relative" style="z-index: 2;">
                    <span
                        class="badge rounded-pill bg-pink-subtle text-pink-dark fw-800 px-3 py-2 mb-4 font-10 text-uppercase letter-spacing-1">BLOSSOM
                        ACADEMY PORTAL</span>
                    <h1 class="display-4 fw-800 text-brown mb-4 font-heading">Tương Lai Của <br> <span
                            class="text-pink">Hồ Sơ Học Thuật</span></h1>
                    <p class="text-muted-brown fw-500 lead mb-5 pe-lg-5">Chào mừng bạn đến với Blossom Academy. Hệ thống
                        hồ sơ học tập số phi tập trung của chúng tôi đảm bảo tính minh bạch tuyệt đối, bảo mật đa lớp và
                        quyền sở hữu dữ liệu thuộc về chính sinh viên.</p>
                    <div class="d-flex flex-wrap gap-3">
                        <router-link to="/sinh-vien/ket-qua"
                            class="btn btn-pink-action rounded-pill px-4 py-3 fw-700 shadow-pink-sm text-decoration-none">
                            Khám phá hồ sơ của tôi
                        </router-link>
                        <button class="btn btn-outline-pink-action rounded-pill px-4 py-3 fw-700">Tìm hiểu về Web3
                            Edu</button>
                    </div>
                </div>
                <div class="col-lg-5 text-center position-relative">
                    <div class="hero-image-wrapper">
                        <img src="https://img.freepik.com/free-vector/abstract-technology-particle-background_23-2148426649.jpg"
                            alt="Abstract Tech"
                            class="img-fluid rounded-5 shadow-lg floating-anim border border-pink-subtle border-4">
                    </div>
                </div>
            </div>
            <!-- Decorative elements -->
            <div class="hero-deco deco-1"></div>
            <div class="hero-deco deco-2"></div>
        </div>

        <!-- Features Section -->
        <div class="row g-4 mb-5">
            <div class="col-lg-4" v-for="(feature, idx) in features" :key="idx">
                <div class="feature-card p-4 rounded-5 bg-white shadow-soft h-100 border-0">
                    <div class="icon-box-lg mb-4 text-pink fs-3 rounded-4 bg-pink-subtle d-flex align-items-center justify-content-center shadow-sm"
                        style="width: 60px; height: 60px;">
                        <i :class="feature.icon"></i>
                    </div>
                    <h5 class="fw-800 text-brown mb-3">{{ feature.title }}</h5>
                    <p class="text-muted-brown small fw-500 mb-0" style="line-height: 1.6;">{{ feature.desc }}</p>
                </div>
            </div>
        </div>

        <!-- Guidelines & Violations -->
        <div class="row g-5 mb-5">
            <div class="col-lg-6">
                <h3 class="fw-800 text-brown mb-4 pt-3 font-heading">Nội Quy & <br> Quy Định Hệ Thống</h3>
                <div class="guideline-list mt-4 mt-lg-5">
                    <div class="guideline-item d-flex gap-4 mb-4" v-for="(rule, idx) in guidelines" :key="idx">
                        <div class="rule-num fw-800 text-brown bg-pink-subtle d-flex align-items-center justify-content-center rounded-circle flex-shrink-0"
                            style="width: 44px; height: 44px;">
                            {{ idx + 1 }}
                        </div>
                        <div class="pt-1">
                            <h5 class="fw-800 text-brown mb-2">{{ rule.title }}</h5>
                            <p class="text-muted-brown fw-500 mb-0" style="line-height: 1.6; font-size: 14px;">{{
                                rule.desc }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="violation-card p-5 rounded-5 shadow-soft border-0 h-100">
                    <h4 class="fw-800 text-brown mb-5 font-18 d-flex align-items-center gap-2">
                        <i class="bi bi-shield-lock-fill text-pink"></i> Xử Phạt Vi Phạm
                    </h4>
                    <div class="violation-list d-flex flex-column gap-4">
                        <div class="violation-item p-4 bg-white rounded-4 shadow-sm d-flex align-items-center gap-4 border-0"
                            v-for="(v, idx) in violations" :key="idx">
                            <div class="v-icon flex-shrink-0 text-pink fs-2 d-flex align-items-center justify-content-center"
                                style="width: 40px;">
                                <i :class="v.icon"></i>
                            </div>
                            <div class="flex-fill">
                                <h6 class="fw-800 text-brown mb-2 font-14">{{ v.title }}</h6>
                                <p class="text-muted-brown font-12 fw-500 mb-0" style="line-height: 1.5;">{{ v.desc }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Support Section -->
        <div class="support-banner rounded-5 mb-5 overflow-hidden shadow-sm border-0">
            <div class="row g-0 h-100">
                <div class="col-lg-6 p-5 d-flex flex-column justify-content-center" style="background: #5d3a3a;">
                    <h2 class="fw-800 text-white mb-4 line-height-1-4">Bạn cần hỗ trợ kỹ thuật?</h2>
                    <p class="text-white-50 mb-5 fw-500">Đội ngũ kỹ thuật viên của Ethereal Academy luôn sẵn sàng giải
                        đáp mọi thắc mắc về hồ sơ số và công nghệ blockchain.</p>
                    <div class="contact-info d-flex flex-column gap-3">
                        <div class="d-flex align-items-center gap-3 text-white">
                            <i class="bi bi-envelope text-pink"></i>
                            <span class="fw-600">support@blossom.edu.vn</span>
                        </div>
                        <div class="d-flex align-items-center gap-3 text-white">
                            <i class="bi bi-telephone text-pink"></i>
                            <span class="fw-600">+84 (024) 3852 1234</span>
                        </div>
                        <div class="d-flex align-items-center gap-3 text-white">
                            <i class="bi bi-geo-alt text-pink"></i>
                            <span class="fw-600">Khu Công nghệ cao, Hòa Lạc, Hà Nội</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 d-none d-lg-block position-relative">
                    <div class="support-bg-image"
                        style="background: url('https://img.freepik.com/free-vector/human-resources-it-company-concept_52683-42407.jpg') center/cover;">
                    </div>
                    <div class="support-overlay"></div>
                </div>
            </div>
        </div>

        <!-- Portal Footer -->
        <footer class="pt-5 pb-4 border-top border-light mt-5">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <div>
                    <h6 class="fw-800 text-brown m-0 font-heading">Ethereal Academy</h6>
                    <p class="text-muted-brown font-11 mb-0 mt-1 fw-500">© 2026 Ethereal Academy. Decentralized Academic
                        Integrity.</p>
                </div>
                <div class="footer-links d-flex gap-4">
                    <a href="#" class="text-muted-brown font-11 fw-700 text-decoration-none hover-pink">Contact
                        Support</a>
                    <a href="#" class="text-muted-brown font-11 fw-700 text-decoration-none hover-pink">Privacy
                        Policy</a>
                    <a href="#" class="text-muted-brown font-11 fw-700 text-decoration-none hover-pink">Institutional
                        Rules</a>
                </div>
            </div>
        </footer>
    </div>
</template>

<script>
export default {
    name: 'SinhVienDashboard',
    data() {
        return {
            features: [
                { icon: 'bi bi-shield-fill-check', title: 'Minh bạch tuyệt đối', desc: 'Mọi bằng cấp và chứng chỉ được mã hóa trên Blockchain, loại bỏ hoàn toàn khả năng làm giả hồ sơ học thuật.' },
                { icon: 'bi bi-shield-fill-lock', title: 'Quyền sở hữu dữ liệu', desc: 'Bạn là chủ sở hữu duy nhất của hồ sơ. Không ai có quyền truy cập nếu chưa có sự đồng ý của chính bạn thông qua mã DID.' },
                { icon: 'bi bi-globe', title: 'Liên thông toàn cầu', desc: 'Hồ sơ số của bạn được công nhận bởi các đối tác học thuật và nhà tuyển dụng uy tín trên toàn thế giới.' },
            ],
            guidelines: [
                { title: 'Sử dụng tài khoản', desc: 'Mỗi sinh viên chỉ được cấp một mã định danh duy nhất (DID). Việc chia sẻ thông tin đăng nhập hoặc mã khóa bí mật cho bên thứ ba là bị nghiêm cấm.' },
                { title: 'Cập nhật hồ sơ', desc: 'Thông tin cá nhân và kết quả học tập sẽ được hệ thống đồng bộ tự động từ phòng đào tạo định kỳ vào cuối mỗi học kỳ.' },
                { title: 'Lưu trữ dữ liệu', desc: 'Hồ sơ học thuật được lưu trữ an toàn trên mạng lưới phi tập trung IPFS, đảm bảo tính bền vững và không thể bị thất lạc.' },
            ],
            violations: [
                { icon: 'bi bi-exclamation-triangle-fill', title: 'Gian lận thông tin', desc: 'Mọi hành vi can thiệp trái phép vào dữ liệu đầu vào hoặc cố ý cung cấp thông tin sai lệch sẽ dẫn đến vĩnh viễn đình chỉ tài khoản.' },
                { icon: 'bi bi-info-circle-fill', title: 'Vi phạm bản quyền', desc: 'Sử dụng tài liệu hoặc dữ liệu của Học viện cho mục đích thương mại phi pháp sẽ bị xử lý nghiêm theo luật sở hữu trí tuệ.' },
                { icon: 'bi bi-shield-fill-x', title: 'Truy cập trái phép', desc: 'Cố gắng truy cập vào hồ sơ của người khác hoặc tấn công hạ tầng ứng dụng sẽ bị truy cứu trách nhiệm trước pháp luật.' },
            ]
        }
    }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

.student-portal {
    font-family: 'Plus Jakarta Sans', sans-serif;
    background: transparent;
    min-height: 100vh;
}

.text-pink {
    color: #db2777;
}

.text-pink-dark {
    color: #831843;
}

.text-brown {
    color: #5d3a3a;
}

.text-muted-brown {
    color: #a58a8a;
}

.bg-pink-subtle {
    background: #fdf2f8;
}

.fw-800 {
    font-weight: 800;
}

.fw-700 {
    font-weight: 700;
}

.fw-500 {
    font-weight: 500;
}

.font-10 {
    font-size: 10px;
}

.font-11 {
    font-size: 11px;
}

.font-14 {
    font-size: 14px;
}

.font-18 {
    font-size: 18px;
}

.letter-spacing-1 {
    letter-spacing: 0.1em;
}

.line-height-1-4 {
    line-height: 1.4;
}

.rounded-5 {
    border-radius: 32px !important;
}

.rounded-4 {
    border-radius: 20px !important;
}

.shadow-soft {
    box-shadow: 0 15px 35px rgba(126, 78, 78, 0.04);
}

.portal-hero {
    background: #fff9fa;
    border: 1px solid #fce7f3;
}

.hero-deco {
    position: absolute;
    border-radius: 50%;
    background: rgba(219, 39, 119, 0.03);
    z-index: 1;
}

.deco-1 {
    width: 400px;
    height: 400px;
    top: -150px;
    right: -100px;
}

.deco-2 {
    width: 200px;
    height: 200px;
    bottom: -50px;
    left: -50px;
}

.btn-pink-action {
    background: #db2777;
    border-color: #db2777;
    color: #fff;
}

.btn-pink-action:hover {
    background: #be185d;
    color: white;
    transform: translateY(-2px);
}

.btn-outline-pink-action {
    border-color: #db2777;
    color: #db2777;
}

.btn-outline-pink-action:hover {
    background: #fdf2f8;
    color: #db2777;
}

.shadow-pink-sm {
    box-shadow: 0 4px 14px 0 rgba(219, 39, 119, 0.3);
}

.floating-anim {
    animation: floating 3s ease-in-out infinite;
}

@keyframes floating {
    0% {
        transform: translateY(0px);
    }

    50% {
        transform: translateY(-15px);
    }

    100% {
        transform: translateY(0px);
    }
}

.feature-card {
    transition: all 0.3s ease;
}

.feature-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(219, 39, 119, 0.06) !important;
}

.guideline-item {
    padding: 10px 0;
}

.rule-num {
    font-size: 16px;
    min-width: 44px;
    height: 44px;
    transition: all 0.3s ease;
}

.guideline-item:hover .rule-num {
    background: #db2777 !important;
    color: #fff !important;
}

.violation-card {
    background: #fffbfc !important;
    border: 1px solid #fdf2f2 !important;
}

.violation-item {
    transition: all 0.2s ease;
    border: 1px solid transparent;
}

.violation-item:hover {
    border-color: #fdf2f2;
    transform: translateX(5px);
    background: #fff !important;
}

.support-bg-image {
    height: 100%;
    width: 100%;
}

.support-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, #5d3a3a 0%, transparent 100%);
}

.hover-pink:hover {
    color: #db2777 !important;
}

.font-heading {
    letter-spacing: -0.02em;
}
</style>
