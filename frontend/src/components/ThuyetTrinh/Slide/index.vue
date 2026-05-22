<template>
  <div class="slide-root-wrapper">
<canvas id="bg-canvas"></canvas>

<div class="presentation">
  <!-- TOP BAR -->
  <div class="topbar">
    <div class="topbar-logo">⛓ BlockChain Academic Records · Nhóm 33</div>
    <div class="slide-counter">Slide <span id="cur-num">1</span> / 13</div>
  </div>

  <!-- SLIDES -->
  <div class="slides-container">

    <!-- ═══ SLIDE 1: COVER ═══ -->
    <div class="slide active" id="slide-1">
      <div class="slide-tag">Đại học Duy Tân · Nhóm 33</div>
      <h1 class="hero-title">Xây Dựng &amp; Phát Triển<br>Hệ Thống Hồ Sơ Học Tập Số<br>Phi Tập Trung Trên Nền Tảng Blockchain</h1>
      <div class="cover-badges">
        <span class="badge">Blockchain</span>
        <span class="badge purple">Web3 + Web2</span>
        <span class="badge amber">Smart Contract</span>
        <span class="badge">NFT</span>
        <span class="badge purple">Local Server</span>
      </div>
      <p class="subtitle">
        GVHD: ThS. Nguyễn Thanh Trung &nbsp;|&nbsp; Đà Nẵng, 03/2026
      </p>
      <div style="margin-top:28px; display:flex; gap:12px; flex-wrap:wrap; justify-content:center;">
        <span style="font-size:12px; color:var(--text-muted);">Hoàng Văn Phong &bull; Lưu Văn Lâm &bull; Cao Thanh Bình &bull; Nguyễn Lê Khánh Ly &bull; Trần Lê Nhất</span>
      </div>
    </div>

    <!-- ═══ SLIDE 2: NHÓM & TIẾN ĐỘ ═══ -->
    <div class="slide" id="slide-2">
      <div class="slide-tag">Nhóm dự án</div>
      <h2 class="section-title">Thành Viên &amp; Tiến Độ</h2>
      <div style="width:100%;max-width:960px">
        <!-- Members -->
        <div style="display:grid; grid-template-columns:repeat(5,1fr); gap:12px; margin-bottom:24px">
          <div class="member-card">
            <div class="avatar" style="background:none; padding:0; overflow:hidden;">
              <img src="../phong_cui.png" alt="Hoàng Văn Phong" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
            </div>
            <div class="member-name">Hoàng Văn Phong</div>
            <div class="member-id">28211152417</div>
            <div class="member-role">Scrum Master</div>
          </div>
          <div class="member-card">
            <div class="avatar" style="background:none; padding:0; overflow:hidden;">
              <img src="../Lam.png" alt="Lưu Văn Lâm" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
            </div>
            <div class="member-name">Lưu Văn Lâm</div>
            <div class="member-id">28211154140</div>
            <div class="member-role">Team Member</div>
          </div>
          <div class="member-card">
            <div class="avatar" style="background:none; padding:0; overflow:hidden;">
              <img src="../Binh.png" alt="Cao Thanh Bình" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
            </div>
            <div class="member-name">Cao Thanh Bình</div>
            <div class="member-id">28213200641</div>
            <div class="member-role">Team Member</div>
          </div>
          <div class="member-card">
            <div class="avatar" style="background:none; padding:0; overflow:hidden;">
              <img src="../Ly.png" alt="Nguyễn Lê Khánh Ly" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
            </div>
            <div class="member-name">Nguyễn Lê Khánh Ly</div>
            <div class="member-id">28209038537</div>
            <div class="member-role">Team Member</div>
          </div>
          <div class="member-card">
            <div class="avatar" style="background:none; padding:0; overflow:hidden;">
              <img src="../Nhat.png" alt="Trần Lê Nhất" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
            </div>
            <div class="member-name">Trần Lê Nhất</div>
            <div class="member-id">28211104848</div>
            <div class="member-role">Team Member</div>
          </div>
        </div>
        <!-- Timeline compact -->
        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:10px;">
          <div class="card" style="padding:14px 16px">
            <div class="card-title">📋 Khởi tạo</div>
            <p>12/03 – 19/03/2026<br>Thu thập yêu cầu, tài liệu ban đầu, kick-off meeting</p>
          </div>
          <div class="card" style="padding:14px 16px">
            <div class="card-title">🚀 Phát triển</div>
            <p>20/03 – 30/04/2026<br>Sprint 1 (21 ngày) + Sprint 2 (21 ngày)</p>
          </div>
          <div class="card" style="padding:14px 16px">
            <div class="card-title">✅ Hoàn thiện</div>
            <p>01/05 – 10/05/2026<br>Kiểm thử tổng thể, fix bug, viết báo cáo</p>
          </div>
        </div>
        <p style="font-size:12px; color:var(--text-muted); text-align:center; margin-top:14px">
          💰 Tổng kinh phí dự kiến: <strong style="color:var(--accent)">17.328.000 VND</strong>
          &nbsp;·&nbsp; 40.000 VND/giờ × 5 thành viên
        </p>
      </div>
    </div>

    <!-- ═══ SLIDE 3: BỐI CẢNH & VẤN ĐỀ ═══ -->
    <div class="slide" id="slide-3">
      <div class="slide-tag">Lý do chọn đề tài</div>
      <h2 class="section-title">Bài Toán Đặt Ra</h2>
      <div class="card-grid col-2" style="max-width:860px">
        <div class="card">
          <div class="card-icon">🏛️</div>
          <div class="card-title">Hệ thống tập trung truyền thống</div>
          <p>Quản lý hồ sơ học tập hiện tại phụ thuộc vào hệ thống tập trung, dễ bị tấn công, thao túng dữ liệu và khó xác minh từ bên thứ ba.</p>
        </div>
        <div class="card">
          <div class="card-icon">📄</div>
          <div class="card-title">Nguy cơ giả mạo bằng cấp</div>
          <p>Việc xác minh văn bằng/chứng chỉ tốn nhiều thời gian, chi phí cao, và vẫn tiềm ẩn nguy cơ làm giả hồ sơ học thuật.</p>
        </div>
        <div class="card">
          <div class="card-icon">🔗</div>
          <div class="card-title">Giải pháp Blockchain</div>
          <p>Blockchain cung cấp cơ chế lưu trữ <strong>minh bạch, phi tập trung và bất biến</strong> — dữ liệu không thể chỉnh sửa sau khi được ghi nhận.</p>
        </div>
        <div class="card">
          <div class="card-icon">🎯</div>
          <div class="card-title">Mục tiêu hướng đến</div>
          <p>Xây dựng hệ thống quản lý hồ sơ học tập ứng dụng Blockchain để xác thực dữ liệu, chia sẻ qua QR Code, đảm bảo tính toàn vẹn.</p>
        </div>
      </div>
    </div>

    <!-- ═══ SLIDE 4: MỤC TIÊU ═══ -->
    <div class="slide" id="slide-4">
      <div class="slide-tag">Mục tiêu hệ thống</div>
      <h2 class="section-title">Những Gì Hệ Thống Cần Đạt</h2>
      <ul class="check-list">
        <li>Thiết kế và xây dựng hoàn chỉnh website Hệ thống học tập số phi tập trung trên nền tảng Blockchain.</li>
        <li>Đăng ký, đăng nhập, đăng xuất tài khoản và quản lý thông tin cá nhân.</li>
        <li>Xây dựng hệ thống quản lý hồ sơ học tập số cho sinh viên (bảng điểm, chứng chỉ, dự án).</li>
        <li>Ứng dụng Blockchain để <strong>xác thực dữ liệu học thuật</strong> thông qua Smart Contract &amp; NFT.</li>
        <li>Cung cấp cơ chế <strong>chia sẻ hồ sơ học tập thông qua QR Code</strong> cho bên thứ ba.</li>
        <li>Đảm bảo tính <strong>minh bạch, toàn vẹn và bảo mật</strong> của dữ liệu học thuật.</li>
        <li>Đánh giá hiệu quả của mô hình quản lý hồ sơ học tập phi tập trung.</li>
      </ul>
    </div>

    <!-- ═══ SLIDE 5: TỔNG QUAN HỆ THỐNG ═══ -->
    <div class="slide" id="slide-5">
      <div class="slide-tag">Tổng quan hệ thống</div>
      <h2 class="section-title">Kiến Trúc &amp; Tác Nhân</h2>
      <div style="width:100%;max-width:900px">
        <!-- 3 actors -->
        <div class="card-grid col-3" style="margin-bottom:24px">
          <div class="card" style="border-color:rgba(2,132,199,.5)">
            <div class="card-icon">👤</div>
            <div class="card-title">Khách vãng lai</div>
            <p>Tra cứu, xác minh văn bằng công khai. Quét QR Code để kiểm tra tính hợp lệ trên Blockchain.</p>
          </div>
          <div class="card" style="border-color:rgba(124,58,237,.45)">
            <div class="card-icon">🎓</div>
            <div class="card-title">Sinh viên</div>
            <p>Xem kết quả học tập, đăng ký lớp học, gửi yêu cầu cấp NFT, quản lý chứng chỉ cá nhân.</p>
          </div>
          <div class="card" style="border-color:rgba(245,158,11,.4)">
            <div class="card-icon">🛡️</div>
            <div class="card-title">Admin</div>
            <p>Phê duyệt văn bằng, quản lý nhân viên/sinh viên, ký &amp; đúc NFT, phân quyền hệ thống.</p>
          </div>
        </div>
        <!-- Architecture flow -->
        <div class="arch-row">
          <div class="arch-box">Frontend<br><small style="color:var(--text-muted)">VueJS</small></div>
          <div class="arch-arrow">→</div>
          <div class="arch-box">Backend<br><small style="color:var(--text-muted)">Laravel (PHP)</small></div>
          <div class="arch-arrow">→</div>
          <div class="arch-box">Database<br><small style="color:var(--text-muted)">MySQL</small></div>
          <div class="arch-arrow">→</div>
          <div class="arch-box">Local Server<br><small style="color:var(--text-muted)">Metadata</small></div>
          <div class="arch-arrow">→</div>
          <div class="arch-box" style="border-color:rgba(2,132,199,.7); background:rgba(2,132,199,.12); color:var(--primary)">Blockchain<br><small style="color:var(--primary)">EVM / Solidity</small></div>
        </div>
      </div>
    </div>

    <!-- ═══ SLIDE 6: CÔNG NGHỆ ═══ -->
    <div class="slide" id="slide-6">
      <div class="slide-tag">Công nghệ sử dụng</div>
      <h2 class="section-title">Stack Công Nghệ</h2>
      
      <div class="container-fluid px-0" style="max-width: 900px; margin-top: 1rem;">
        <div class="row row-cols-1 row-cols-md-2 g-4">
          <!-- Frontend -->
          <div class="col">
            <div class="card h-100 shadow-sm border-0 border-start border-4 border-success rounded-3">
              <div class="card-body p-4">
                <h5 class="card-title fw-bold text-success mb-3"><span class="fs-4 me-2">🖥️</span>Frontend</h5>
                <div class="d-flex flex-wrap gap-2">
                  <span class="badge bg-success px-3 py-2 rounded-pill">Vue.js 3</span>
                  <span class="badge bg-primary px-3 py-2 rounded-pill">JavaScript</span>
                  <span class="badge bg-dark px-3 py-2 rounded-pill">HTML5/CSS3</span>
                  <span class="badge bg-info text-dark px-3 py-2 rounded-pill">Bootstrap 5</span>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Backend -->
          <div class="col">
            <div class="card h-100 shadow-sm border-0 border-start border-4 border-danger rounded-3">
              <div class="card-body p-4">
                <h5 class="card-title fw-bold text-danger mb-3"><span class="fs-4 me-2">⚙️</span>Backend</h5>
                <div class="d-flex flex-wrap gap-2">
                  <span class="badge bg-danger px-3 py-2 rounded-pill">PHP 8</span>
                  <span class="badge bg-dark px-3 py-2 rounded-pill">Laravel</span>
                  <span class="badge bg-secondary px-3 py-2 rounded-pill">RESTful API</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Blockchain -->
          <div class="col">
            <div class="card h-100 shadow-sm border-0 border-start border-4 border-primary rounded-3">
              <div class="card-body p-4">
                <h5 class="card-title fw-bold text-primary mb-3"><span class="fs-4 me-2">🔗</span>Blockchain &amp; Web3</h5>
                <div class="d-flex flex-wrap gap-2">
                  <span class="badge bg-secondary px-3 py-2 rounded-pill">Solidity</span>
                  <span class="badge bg-primary px-3 py-2 rounded-pill">Smart Contract</span>
                  <span class="badge bg-info text-dark px-3 py-2 rounded-pill">EVM Network</span>
                  <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">NFT (ERC-721)</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Lưu trữ -->
          <div class="col">
            <div class="card h-100 shadow-sm border-0 border-start border-4 border-warning rounded-3">
              <div class="card-body p-4">
                <h5 class="card-title fw-bold text-warning mb-3" style="color: #d97706 !important;"><span class="fs-4 me-2">🗄️</span>Lưu trữ &amp; Tích hợp</h5>
                <div class="d-flex flex-wrap gap-2">
                  <span class="badge bg-info text-dark px-3 py-2 rounded-pill">MySQL</span>
                  <span class="badge bg-secondary px-3 py-2 rounded-pill">Local Server Storage</span>
                  <span class="badge bg-success px-3 py-2 rounded-pill">QR Code (qrcode.vue)</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="d-flex justify-content-center gap-3 mt-4">
          <span class="badge bg-dark px-3 py-2 fs-6 shadow-sm">Mô hình: Scrum (Agile)</span>
          <span class="badge px-3 py-2 fs-6 shadow-sm" style="background-color: #7c3aed;">Hệ thống: Web2 + Web3 Hybrid</span>
        </div>
      </div>
    </div>
        <!-- ═══ SLIDE 7: KIẾN THỨC BLOCKCHAIN ═══ -->
    <div class="slide" id="slide-7">
      <div class="slide-tag">Nền tảng công nghệ</div>
      <h2 class="section-title">Tổng Quan Về Blockchain</h2>
      <div class="card-grid col-2" style="max-width:920px; margin-bottom: 16px;">
        <div class="card" style="border-color:rgba(2,132,199,.4)">
          <div class="card-icon">⛓️</div>
          <div class="card-title">Blockchain là gì?</div>
          <p>Là một cuốn sổ cái kỹ thuật số phi tập trung, phân tán và công khai, dùng để ghi lại các giao dịch trên nhiều máy tính sao cho bản ghi đó không thể bị thay đổi mà không làm thay đổi các khối tiếp theo.</p>
        </div>
        <div class="card" style="border-color:rgba(124,58,237,.35)">
          <div class="card-icon">⚙️</div>
          <div class="card-title">Cách hoạt động cơ bản</div>
          <ul style="margin-top:8px; font-size:14px; color:var(--text-muted); list-style:none; display:flex; flex-direction:column; gap:8px;">
            <li><strong style="color:var(--text)">1. Ghi nhận:</strong> Ai đó yêu cầu lưu một thông tin (VD: cấp văn bằng).</li>
            <li><strong style="color:var(--text)">2. Kiểm tra:</strong> Tất cả máy tính trong mạng lưới cùng kiểm chứng thông tin đó.</li>
            <li><strong style="color:var(--text)">3. Đóng gói:</strong> Nếu đúng, thông tin sẽ được đưa vào một khối dữ liệu (Block).</li>
            <li><strong style="color:var(--text)">4. Khóa chặt:</strong> Khối này bị khóa lại và móc nối với các khối trước thành một chuỗi (Chain) vĩnh viễn không thể xóa.</li>
          </ul>
        </div>
      </div>
      <div class="card-grid col-3" style="max-width:920px;">
        <div class="card" style="padding:16px;">
          <div class="card-title" style="color:var(--primary); font-size: 14px;">Phi Tập Trung</div>
          <p style="font-size:13px;">Dữ liệu không bị lưu ở một máy chủ duy nhất mà phân tán trên toàn mạng lưới.</p>
        </div>
        <div class="card" style="padding:16px;">
          <div class="card-title" style="color:#059669; font-size: 14px;">Bất Biến</div>
          <p style="font-size:13px;">Một khi dữ liệu đã được ghi vào Blockchain, nó không thể bị xóa hay giả mạo.</p>
        </div>
        <div class="card" style="padding:16px;">
          <div class="card-title" style="color:#d97706; font-size: 14px;">Minh Bạch</div>
          <p style="font-size:13px;">Mọi đối tượng tham gia đều có thể xem lịch sử giao dịch và đối chiếu nguồn gốc.</p>
        </div>
      </div>
    </div>

    <!-- ═══ SLIDE 8: THUẬT NGỮ BLOCKCHAIN ═══ -->
    <div class="slide" id="slide-8">
      <div class="slide-tag">Thuật ngữ chuyên sâu</div>
      <h2 class="section-title">Các Khái Niệm Quan Trọng</h2>
      <div class="card-grid col-2" style="max-width:920px;">
        <div class="card" style="border-color:rgba(239,68,68,.35)">
          <div class="card-icon">#️⃣</div>
          <div class="card-title" style="color:#dc2626;">Mã băm (Hash)</div>
          <p style="font-size:14px; line-height: 1.6;">Giống như "dấu vân tay" của dữ liệu. Mọi dữ liệu đi qua hàm Hash đều cho ra một chuỗi ký tự cố định. Chỉ cần thay đổi 1 ký tự trong dữ liệu gốc, mã Hash sẽ thay đổi hoàn toàn (ứng dụng để phát hiện gian lận).</p>
        </div>
        <div class="card" style="border-color:rgba(16,185,129,.35)">
          <div class="card-icon">⛽</div>
          <div class="card-title" style="color:#059669;">Phí Gas</div>
          <p style="font-size:14px; line-height: 1.6;">Tương tự như tiền xăng xe. Đây là khoản phí bạn trả cho "thợ đào" (các máy tính trong mạng lưới) để họ tính toán, xử lý và lưu trữ giao dịch (như đúc văn bằng) của bạn lên Blockchain.</p>
        </div>
        <div class="card" style="border-color:rgba(245,158,11,.35)">
          <div class="card-icon">📜</div>
          <div class="card-title" style="color:#d97706;">Smart Contract</div>
          <p style="font-size:14px; line-height: 1.6;">Hợp đồng thông minh là các đoạn code tự động chạy khi đạt đủ điều kiện. Giống như máy bán nước tự động: bạn đưa đủ tiền vào thì nước tự rơi ra mà không cần người bán can thiệp.</p>
        </div>
        <div class="card" style="border-color:rgba(124,58,237,.35)">
          <div class="card-icon">🖼️</div>
          <div class="card-title" style="color:#7c3aed;">NFT (Non-Fungible Token)</div>
          <p style="font-size:14px; line-height: 1.6;">Là tài sản kỹ thuật số độc nhất không thể thay thế. Trong dự án, mỗi văn bằng của sinh viên sẽ được đúc thành một NFT duy nhất trên Blockchain để chứng minh quyền sở hữu chính chủ.</p>
        </div>
      </div>
    </div>

    <!-- ═══ SLIDE 9: KIẾN TRÚC HỆ THỐNG ═══ -->
    <div class="slide" id="slide-9" style="justify-content:flex-start; padding:18px 36px 14px;">
      <div class="slide-tag" style="margin-top:2px">Kiến trúc phần mềm</div>
      <h2 class="section-title" style="margin-bottom:14px">Mô Hình Kiến Trúc Hệ Thống</h2>
      <div style="flex:1; min-height:0; width:100%; display:flex; align-items:center; justify-content:center; overflow:hidden;">
        <img src="../Gemini_Generated_Image_l1nk8fl1nk8fl1nk.png" alt="Mô hình kiến trúc phần mềm"
          class="zoomable"
          onclick="openLightbox(this)"
          style="max-width:100%; max-height:100%; object-fit:contain;
                 border-radius:14px;
                 box-shadow:0 6px 32px rgba(2,132,199,0.18);
                 border:1.5px solid #e2e8f0;">
      </div>
    </div>

    <!-- ═══ SLIDE 10: CƠ SỞ DỮ LIỆU ═══ -->
    <div class="slide" id="slide-10">
      <div class="slide-tag">Thiết kế Database</div>
      <h2 class="section-title">Lược Đồ Cơ Sở Dữ Liệu</h2>
      <div style="width:100%;max-width:920px; display:flex; flex-direction:column; gap:14px">
        <div class="card-grid col-2">
          <div class="card" style="padding:16px">
            <div class="card-title">👥 Quản lý người dùng</div>
            <div style="margin-top:8px; line-height:2">
              <span class="db-table-name">chuc_vu</span>
              <span class="db-table-name">phan_quyen</span>
              <span class="db-table-name">chuc_nang</span>
              <span class="db-table-name">nhan_vien</span>
              <span class="db-table-name">vi_nhan_vien</span>
              <span class="db-table-name">sinh_vien</span>
              <span class="db-table-name">vi_sinh_vien</span>
              <span class="db-table-name">phong_ban</span>
            </div>
          </div>
          <div class="card" style="padding:16px">
            <div class="card-title">📚 Học tập & Đào tạo</div>
            <div style="margin-top:8px; line-height:2">
              <span class="db-table-name">mon_hoc</span>
              <span class="db-table-name">lop_hoc</span>
              <span class="db-table-name">dang_ky_lop</span>
              <span class="db-table-name">bang_diem</span>
              <span class="db-table-name">du_an</span>
            </div>
          </div>
        </div>
        <div class="card-grid col-2">
          <div class="card" style="padding:16px">
            <div class="card-title">🏅 Văn bằng & Chứng chỉ</div>
            <div style="margin-top:8px; line-height:2">
              <span class="db-table-name">don_vi_cap</span>
              <span class="db-table-name">chung_chi</span>
              <span class="db-table-name">lich_su_phe_duyet</span>
            </div>
          </div>
          <div class="card" style="padding:16px">
            <div class="card-title">⛓️ Blockchain &amp; NFT</div>
            <div style="margin-top:8px; line-height:2">
              <span class="db-table-name">smart_contracts</span>
              <span class="db-table-name">nft_van_bang</span>
              <span class="db-table-name">lich_su_giao_dich</span>
              <span class="db-table-name">thong_bao</span>
            </div>
          </div>
        </div>
        <p style="font-size:12px; color:var(--text-muted); text-align:center">
          Tổng cộng <strong style="color:var(--primary)">19 bảng</strong> — MySQL (off-chain) kết hợp Local Server &amp; EVM Blockchain (on-chain)
        </p>
      </div>
    </div>

    <!-- ═══ SLIDE 11: PHÁT TRIỂN SPRINT ═══ -->
    <div class="slide" id="slide-11">
      <div class="slide-tag">Quá trình phát triển</div>
      <h2 class="section-title">Sprint 1 &amp; Sprint 2</h2>
      <div class="card-grid col-2" style="max-width:900px">
        <div class="card" style="border-color:rgba(2,132,199,.5)">
          <div class="card-title">🏃 Sprint 1 &nbsp;<span class="badge" style="font-size:10px">20/03 – 09/04</span></div>
          <ul style="margin-top:10px; font-size:12px; color:var(--text-muted); list-style:none; display:flex; flex-direction:column; gap:5px;">
            <li>✦ Phân quyền hệ thống</li>
            <li>✦ Phê duyệt văn bằng (Duyệt dữ liệu, Ký NFT, Đúc NFT)</li>
            <li>✦ Quản lý Môn Học, Lớp Học, Chức Vụ</li>
            <li>✦ Quản lý Đơn Vị Cấp, Phòng Ban, Chứng Chỉ</li>
            <li>✦ Quản lý Dự Án, Bảng Điểm, NFT</li>
            <li>✦ Gửi yêu cầu Chứng Chỉ &amp; Dự Án</li>
          </ul>
        </div>
        <div class="card" style="border-color:rgba(124,58,237,.4)">
          <div class="card-title">🚀 Sprint 2 &nbsp;<span class="badge purple" style="font-size:10px">10/04 – 30/04</span></div>
          <ul style="margin-top:10px; font-size:12px; color:var(--text-muted); list-style:none; display:flex; flex-direction:column; gap:5px;">
            <li>✦ Đăng nhập, quản lý tài khoản, quên mật khẩu</li>
            <li>✦ Xem danh sách môn học, đăng ký lớp học</li>
            <li>✦ Tra cứu văn bằng, xem kết quả học tập</li>
            <li>✦ Thống kê &amp; Báo cáo</li>
            <li>✦ Quản lý Sinh Viên, Nhân Viên (CRUD)</li>
            <li>✦ Gửi yêu cầu cấp NFT</li>
          </ul>
        </div>
      </div>
      <div style="margin-top:18px; width:100%; max-width:900px">
        <div class="card" style="padding:14px 20px">
          <div class="card-title">📅 Các buổi Meeting chính</div>
          <div style="display:flex; gap:20px; flex-wrap:wrap; margin-top:8px; font-size:12px; color:var(--text-muted)">
            <span><strong style="color:var(--primary)">14/03</strong> — Kick-off meeting, thống nhất phạm vi</span>
            <span><strong style="color:var(--primary)">20/03</strong> — Sprint 1 Planning, phân công FE/BE/SC</span>
            <span><strong style="color:var(--primary)">11/04</strong> — Sprint 2 Planning, rà soát góp ý GVHD</span>
            <span><strong style="color:var(--primary)">01/05</strong> — Đóng code, kiểm thử tổng thể, log bug</span>
          </div>
        </div>
      </div>
    </div>

    <!-- ═══ SLIDE 12: DEMO ═══ -->
    <div class="slide" id="slide-12">
      <div class="slide-tag">Demo hệ thống</div>
      <h2 class="section-title">Các Giao Diện Chính</h2>
      <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:12px; width:100%; max-width:940px">
        <div class="card" style="padding:14px 16px; border-color:rgba(2,132,199,.4)">
          <div style="font-size:20px; margin-bottom:6px">🔐</div>
          <div class="card-title">Đăng nhập</div>
          <p>Xác thực tài khoản người dùng với vai trò Admin / Sinh viên</p>
        </div>
        <div class="card" style="padding:14px 16px; border-color:rgba(2,132,199,.4)">
          <div style="font-size:20px; margin-bottom:6px">✅</div>
          <div class="card-title">Phê Duyệt</div>
          <p>Admin xem &amp; duyệt văn bằng, chứng chỉ, dự án của sinh viên</p>
        </div>
        <div class="card" style="padding:14px 16px; border-color:rgba(124,58,237,.35)">
          <div style="font-size:20px; margin-bottom:6px">🏷️</div>
          <div class="card-title">Quản lý NFT</div>
          <p>Admin quản lý NFT đã phát hành, thực hiện ký &amp; đúc NFT on-chain</p>
        </div>
        <div class="card" style="padding:14px 16px; border-color:rgba(2,132,199,.4)">
          <div style="font-size:20px; margin-bottom:6px">👥</div>
          <div class="card-title">Quản lý Sinh viên</div>
          <p>Xem danh sách, thêm/xóa/sửa thông tin sinh viên</p>
        </div>
        <div class="card" style="padding:14px 16px; border-color:rgba(245,158,11,.3)">
          <div style="font-size:20px; margin-bottom:6px">🔍</div>
          <div class="card-title">Tra cứu Văn bằng</div>
          <p>Khách vãng lai tra cứu, quét QR và đối chiếu văn bằng trên Blockchain</p>
        </div>
        <div class="card" style="padding:14px 16px; border-color:rgba(124,58,237,.35)">
          <div style="font-size:20px; margin-bottom:6px">📊</div>
          <div class="card-title">Kết quả Học tập</div>
          <p>Sinh viên xem bảng điểm và gửi yêu cầu cấp NFT cho bảng điểm</p>
        </div>
        <div class="card" style="padding:14px 16px; border-color:rgba(2,132,199,.4)">
          <div style="font-size:20px; margin-bottom:6px">🏅</div>
          <div class="card-title">Chứng chỉ của tôi</div>
          <p>Sinh viên thêm và quản lý chứng chỉ cá nhân trong hồ sơ số</p>
        </div>
        <div class="card" style="padding:14px 16px; border-color:rgba(2,132,199,.4)">
          <div style="font-size:20px; margin-bottom:6px">🏢</div>
          <div class="card-title">Quản lý Đơn vị Cấp</div>
          <p>Admin CRUD danh sách đơn vị có thẩm quyền cấp văn bằng</p>
        </div>
        <div class="card" style="padding:14px 16px; border-color:rgba(124,58,237,.35)">
          <div style="font-size:20px; margin-bottom:6px">🔑</div>
          <div class="card-title">Phân quyền Chức vụ</div>
          <p>Admin quản lý chức vụ và phân quyền chức năng cho từng vai trò</p>
        </div>
      </div>
    </div>

    <!-- ═══ SLIDE 13: KẾT LUẬN ═══ -->
    <div class="slide" id="slide-13">
      <div class="slide-tag">Kết luận</div>
      <h2 class="section-title">Kết Luận &amp; Hướng Phát Triển</h2>
      <div style="width:100%; max-width:900px; display:flex; flex-direction:column; gap:14px">
        <div class="card-grid col-3">
          <div class="stat-box">
            <div class="stat-num">2</div>
            <div class="stat-label">Sprint phát triển</div>
          </div>
          <div class="stat-box">
            <div class="stat-num">19</div>
            <div class="stat-label">Bảng cơ sở dữ liệu</div>
          </div>
          <div class="stat-box">
            <div class="stat-num">3</div>
            <div class="stat-label">Tác nhân hệ thống</div>
          </div>
        </div>
        <div class="card-grid col-3">
          <div class="card" style="border-color:rgba(16,185,129,.4)">
            <div class="card-title" style="color:#059669">✅ Đã làm được</div>
            <ul style="margin-top:8px; font-size:12px; color:var(--text-muted); list-style:none; display:flex; flex-direction:column; gap:5px">
              <li>✦ Hoàn thiện các chức năng cốt lõi của hệ thống</li>
              <li>✦ Deploy lên server, ứng dụng Blockchain xác thực dữ liệu học thuật</li>
              <li>✦ Đảm bảo tính công khai, minh bạch và bảo mật dữ liệu</li>
              <li>✦ Khách vãng lai quét QR để xem văn bằng đã được xác thực trên chain</li>
            </ul>
          </div>
          <div class="card" style="border-color:rgba(239,68,68,.4)">
            <div class="card-title" style="color:#dc2626">⚠️ Hạn chế</div>
            <ul style="margin-top:8px; font-size:12px; color:var(--text-muted); list-style:none; display:flex; flex-direction:column; gap:5px">
              <li>✦ Chưa hoàn thiện hết các chức năng quan trọng và cốt lõi</li>
              <li>✦ Chưa đọc được file Excel để đăng ký tài khoản tự động</li>
              <li>✦ Việc lưu trữ Metadata vẫn còn phụ thuộc vào server</li>
              <li>✦ Chưa tối ưu mobile</li>
            </ul>
          </div>
          <div class="card" style="border-color:rgba(249,115,22,.35)">
            <div class="card-title" style="color:#d97706">🚧 Hướng phát triển</div>
            <ul style="margin-top:8px; font-size:12px; color:var(--text-muted); list-style:none; display:flex; flex-direction:column; gap:5px">
              <li>✦ Hoàn thiện tính minh bạch và phi tập trung hoàn toàn</li>
              <li>✦ Nhập tài khoản sinh viên tự động từ file Excel</li>
              <li>✦ Phát triển hệ thống mobile app</li>
              <li>✦ Lưu Metadata hoàn toàn trên IPFS, độc lập server</li>
              <li>✦ Mở rộng hệ sinh thái &amp; nâng cao UX</li>
            </ul>
          </div>
        </div>
        <div style="text-align:center; margin-top:8px">
          <p style="font-size:18px; font-weight:700; background:linear-gradient(135deg,var(--primary),var(--secondary)); -webkit-background-clip:text; -webkit-text-fill-color:transparent;">
            Trân trọng cảm ơn thầy cô và các bạn đã lắng nghe!
          </p>
          <p style="font-size:12px; color:var(--text-muted); margin-top:6px">Nhóm 33 · Đại học Duy Tân · Đà Nẵng 2026</p>
        </div>
      </div>
    </div>

  </div><!-- /slides-container -->

  <!-- CONTROLS -->
  <div class="controls">
    <button class="btn" id="btn-prev" onclick="changeSlide(-1)" disabled>← Trước</button>
    <div class="dots" id="dots"></div>
    <button class="btn" id="btn-next" onclick="changeSlide(1)">Tiếp →</button>
  </div>
</div>

<!-- LIGHTBOX OVERLAY -->
<div id="lightbox" onclick="closeLightbox()">
  <img id="lightbox-img" alt="" />
  <div id="lightbox-hint">Click bất kỳ để đóng &nbsp;·&nbsp; ESC</div>
</div>
  </div>
</template>

<script setup>
import { onMounted, onUnmounted } from 'vue'

onMounted(() => {
  // ── PARTICLE CANVAS ──
  const canvas = document.getElementById('bg-canvas');
  const ctx = canvas.getContext('2d');
  let W, H, particles = [];

  function resize() {
    W = canvas.width  = window.innerWidth;
    H = canvas.height = window.innerHeight;
  }

  function Particle() {
    this.reset = function() {
      this.x  = Math.random() * W;
      this.y  = Math.random() * H;
      this.vx = (Math.random() - 0.5) * 0.4;
      this.vy = (Math.random() - 0.5) * 0.4;
      this.r  = Math.random() * 1.5 + 0.5;
      this.alpha = Math.random() * 0.5 + 0.2;
    };
    this.reset();
    this.update = function() {
      this.x += this.vx; this.y += this.vy;
      if (this.x < 0 || this.x > W || this.y < 0 || this.y > H) this.reset();
    };
    this.draw = function() {
      ctx.beginPath();
      ctx.arc(this.x, this.y, this.r, 0, Math.PI * 2);
      ctx.fillStyle = `rgba(2,132,199,${this.alpha * 0.5})`;
      ctx.fill();
    };
  }

  function initParticles() {
    particles = [];
    for (let i = 0; i < 90; i++) particles.push(new Particle());
  }

  function drawLines() {
    const maxDist = 130;
    for (let i = 0; i < particles.length; i++) {
      for (let j = i + 1; j < particles.length; j++) {
        const dx = particles[i].x - particles[j].x;
        const dy = particles[i].y - particles[j].y;
        const dist = Math.sqrt(dx*dx + dy*dy);
        if (dist < maxDist) {
          ctx.beginPath();
          ctx.moveTo(particles[i].x, particles[i].y);
          ctx.lineTo(particles[j].x, particles[j].y);
          ctx.strokeStyle = `rgba(2,132,199,${0.18*(1-dist/maxDist)})`;
          ctx.lineWidth = 0.8;
          ctx.stroke();
        }
      }
    }
  }

  function animate() {
    ctx.clearRect(0, 0, W, H);
    particles.forEach(p => { p.update(); p.draw(); });
    drawLines();
    requestAnimationFrame(animate);
  }

  resize(); initParticles(); animate();
  window.addEventListener('resize', () => { resize(); initParticles(); });

  // ── SLIDE LOGIC ──
  let current = 1;
  const total  = 13;

  // Build dots
  const dotsEl = document.getElementById('dots');
  for (let i = 1; i <= total; i++) {
    const d = document.createElement('div');
    d.className = 'dot' + (i === 1 ? ' active' : '');
    d.setAttribute('data-i', i);
    d.onclick = () => goTo(i);
    dotsEl.appendChild(d);
  }

  window.goTo = function(n) {
    if (n < 1 || n > total || n === current) return;
    const prev = document.getElementById('slide-' + current);
    prev.classList.remove('active');
    prev.classList.add('exit');
    setTimeout(() => prev.classList.remove('exit'), 500);

    current = n;
    const next = document.getElementById('slide-' + current);
    next.classList.add('active');

    document.getElementById('cur-num').textContent = current;
    document.getElementById('btn-prev').disabled = current === 1;
    document.getElementById('btn-next').disabled = current === total;

    document.querySelectorAll('.dot').forEach(d => {
      d.classList.toggle('active', +d.dataset.i === current);
    });
  }

  window.changeSlide = function(dir) { goTo(current + dir); }

  // Keyboard navigation
  /* document.addEventListener('keydown', e => {
    if (e.key === 'Escape') { closeLightbox(); return; }
    if (document.getElementById('lightbox').classList.contains('open')) return;
    if (e.key === 'ArrowRight' || e.key === 'ArrowDown') changeSlide(1);
    if (e.key === 'ArrowLeft'  || e.key === 'ArrowUp')   changeSlide(-1);
  }); */

  // ── LIGHTBOX LOGIC ──
  window.openLightbox = function(img) {
    const lb = document.getElementById('lightbox');
    const lbImg = document.getElementById('lightbox-img');
    lbImg.src = img.src;
    lbImg.alt = img.alt;
    lb.classList.add('open');
  }
  window.closeLightbox = function() {
    document.getElementById('lightbox').classList.remove('open');
  }
  
  // Clean up keyboard listener to prevent multiple instances if component is re-mounted
  window._slideKeydown = (e) => {
    if (e.key === 'Escape') { window.closeLightbox(); return; }
    if (document.getElementById('lightbox') && document.getElementById('lightbox').classList.contains('open')) return;
    if (e.key === 'ArrowRight' || e.key === 'ArrowDown') window.changeSlide(1);
    if (e.key === 'ArrowLeft'  || e.key === 'ArrowUp')   window.changeSlide(-1);
  };
  document.addEventListener('keydown', window._slideKeydown);
})

onUnmounted(() => {
  if (window._slideKeydown) {
    document.removeEventListener('keydown', window._slideKeydown);
  }
  // Optional: clear canvas to save memory
  const canvas = document.getElementById('bg-canvas');
  if (canvas) {
    const ctx = canvas.getContext('2d');
    ctx.clearRect(0, 0, canvas.width, canvas.height);
  }
})
</script>

<style>
@import url('https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css');
    @import url('https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;600;700;800&display=swap');

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --primary: #0284c7;
      --secondary: #7c3aed;
      --accent: #d97706;
      --bg-light: #f0f7ff;
      --bg-card: #ffffff;
      --text: #0f172a;
      --text-muted: #475569;
    }

    body {
      font-family: 'Be Vietnam Pro', sans-serif;
      background: linear-gradient(135deg, #e8f4fd 0%, #ffffff 50%, #f5f0ff 100%);
      color: var(--text);
      overflow: hidden;
      height: 100vh;
      width: 100vw;
    }

    /* ── CANVAS BACKGROUND ── */
    #bg-canvas {
      position: fixed; inset: 0; z-index: 0;
      pointer-events: none;
    }

    /* ── SLIDE WRAPPER ── */
    .presentation {
      position: relative; z-index: 1;
      width: calc(100vw / 1.5);
      height: calc(100vh / 1.5);
      transform: scale(1.5);
      transform-origin: top left;
      display: flex; flex-direction: column;
    }

    /* ── TOP NAV BAR ── */
    .topbar {
      display: flex; align-items: center; justify-content: space-between;
      padding: 12px 36px;
      background: rgba(255,255,255,0.95);
      border-bottom: 2px solid rgba(2,132,199,0.2);
      backdrop-filter: blur(12px);
      flex-shrink: 0;
      box-shadow: 0 1px 6px rgba(0,0,0,0.07);
    }
    .topbar-logo {
      font-size: 15px; font-weight: 700; color: var(--primary);
      letter-spacing: 1px; text-transform: uppercase;
    }
    .slide-counter {
      font-size: 15px; color: var(--text-muted);
    }
    .slide-counter span { color: var(--primary); font-weight: 700; }

    /* ── SLIDE AREA ── */
    .slides-container {
      flex: 1;
      position: relative;
      overflow: hidden;
    }

    .slide {
      position: absolute; inset: 0;
      display: flex; flex-direction: column;
      align-items: center; justify-content: center;
      padding: 32px 48px;
      opacity: 0;
      transform: translateX(60px);
      transition: opacity .5s ease, transform .5s ease;
      pointer-events: none;
      overflow-y: auto;
    }
    .slide.active {
      opacity: 1; transform: translateX(0);
      pointer-events: all;
    }
    .slide.exit {
      opacity: 0; transform: translateX(-60px);
    }

    /* ── BOTTOM CONTROLS ── */
    .controls {
      display: flex; align-items: center; justify-content: center; gap: 16px;
      padding: 14px 32px;
      background: rgba(255,255,255,0.95);
      border-top: 2px solid rgba(2,132,199,0.15);
      backdrop-filter: blur(12px);
      flex-shrink: 0;
      box-shadow: 0 -1px 6px rgba(0,0,0,0.06);
    }
    .btn {
      background: var(--primary);
      border: none;
      color: #fff;
      padding: 10px 28px;
      border-radius: 50px;
      cursor: pointer;
      font-family: inherit; font-size: 16px; font-weight: 700;
      transition: all .25s;
      box-shadow: 0 3px 10px rgba(2,132,199,0.35);
    }
    .btn:hover { background: #0369a1; transform: scale(1.04); }
    .btn:disabled { opacity: .35; cursor: default; transform: none; box-shadow: none; }

    /* ── DOT INDICATORS ── */
    .dots {
      display: flex; gap: 8px; align-items: center;
    }
    .dot {
      width: 8px; height: 8px; border-radius: 50%;
      background: #cbd5e1;
      cursor: pointer;
      transition: all .3s;
    }
    .dot.active {
      background: var(--primary);
      width: 24px; border-radius: 4px;
    }

    /* ══════════════════════════════════
       SLIDE COMPONENTS
    ══════════════════════════════════ */

    /* Slide title */
    .slide-tag {
      font-size: 14px; font-weight: 700; letter-spacing: 3px;
      text-transform: uppercase; color: var(--primary);
      margin-bottom: 14px;
    }
    h1.hero-title {
      font-size: clamp(32px,4.5vw,60px); font-weight: 800;
      line-height: 1.2; text-align: center; margin-bottom: 22px;
      background: linear-gradient(135deg, #0f172a 0%, var(--primary) 55%, var(--secondary) 100%);
      -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    }
    h2.section-title {
      font-size: clamp(26px,3.2vw,42px); font-weight: 800;
      margin-bottom: 28px; text-align: center;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    }
    .subtitle {
      font-size: 18px; color: var(--text-muted); text-align: center;
      max-width: 720px; line-height: 1.75;
    }

    /* Card grid */
    .card-grid {
      display: grid; gap: 16px;
      width: 100%; max-width: 960px; margin: 0 auto;
    }
    .card-grid.col-2 { grid-template-columns: 1fr 1fr; }
    .card-grid.col-3 { grid-template-columns: 1fr 1fr 1fr; }

    .card {
      background: #ffffff;
      border: 1.5px solid #e2e8f0;
      border-radius: 16px;
      padding: 22px 26px;
      box-shadow: 0 2px 12px rgba(0,0,0,0.06);
      transition: border-color .3s, transform .3s, box-shadow .3s;
    }
    .card:hover { border-color: rgba(2,132,199,0.5); transform: translateY(-3px); box-shadow: 0 6px 20px rgba(2,132,199,0.12); }
    .card-icon { font-size: 32px; margin-bottom: 12px; }
    .card-title {
      font-size: 16px; font-weight: 700; color: var(--primary);
      margin-bottom: 8px; text-transform: uppercase; letter-spacing: .5px;
    }
    .card p { font-size: 15px; color: #334155; line-height: 1.7; }

    /* List items */
    .check-list {
      list-style: none; display: flex; flex-direction: column; gap: 13px;
      width: 100%; max-width: 820px;
    }
    .check-list li {
      display: flex; align-items: flex-start; gap: 12px;
      font-size: 17px; color: var(--text); line-height: 1.65;
    }
    .check-list li::before {
      content: '✦'; color: var(--primary); flex-shrink: 0; margin-top: 2px;
    }

    /* Highlight badge */
    .badge {
      display: inline-block;
      padding: 6px 18px;
      border-radius: 50px;
      font-size: 14px; font-weight: 700;
      background: rgba(2,132,199,0.1);
      border: 1.5px solid rgba(2,132,199,0.45);
      color: var(--primary);
    }
    .badge.purple {
      background: rgba(124,58,237,0.1);
      border-color: rgba(124,58,237,0.45);
      color: #6d28d9;
    }
    .badge.amber {
      background: rgba(217,119,6,0.1);
      border-color: rgba(217,119,6,0.45);
      color: var(--accent);
    }
    #slide-6 .badge {
      color: #fff !important;
    }

    /* Tech stack pill */
    .tech-pill {
      display: inline-flex; align-items: center; gap: 6px;
      padding: 8px 20px;
      border-radius: 50px;
      font-size: 15px; font-weight: 600;
      margin: 5px;
    }
    .tech-pill.blue { background: rgba(59,130,246,.1); border: 1.5px solid rgba(59,130,246,.5); color:#1d4ed8; }
    .tech-pill.green { background: rgba(16,185,129,.1); border: 1.5px solid rgba(16,185,129,.5); color:#065f46; }
    .tech-pill.purple { background: rgba(139,92,246,.1); border: 1.5px solid rgba(139,92,246,.5); color:#5b21b6; }
    .tech-pill.orange { background: rgba(249,115,22,.1); border: 1.5px solid rgba(249,115,22,.5); color:#9a3412; }
    .tech-pill.cyan { background: rgba(2,132,199,.1); border: 1.5px solid rgba(2,132,199,.5); color:#075985; }

    /* Timeline */
    .timeline {
      display: flex; flex-direction: column; gap: 0;
      width: 100%; max-width: 720px;
    }
    .tl-item {
      display: flex; gap: 16px; align-items: flex-start;
      position: relative;
    }
    .tl-item::before {
      content: '';
      position: absolute; left: 19px; top: 36px;
      width: 2px; height: calc(100% + 0px);
      background: rgba(0,212,255,0.2);
    }
    .tl-item:last-child::before { display: none; }
    .tl-dot {
      width: 40px; height: 40px; border-radius: 50%;
      background: rgba(0,212,255,0.15);
      border: 2px solid var(--primary);
      display: flex; align-items: center; justify-content: center;
      font-size: 15px; flex-shrink: 0;
    }
    .tl-body { padding-bottom: 20px; }
    .tl-title { font-size: 14px; font-weight: 700; color: var(--primary); }
    .tl-date { font-size: 12px; color: var(--text-muted); margin-bottom: 4px; }
    .tl-desc { font-size: 13px; color: var(--text-muted); line-height: 1.55; }

    /* Member card */
    .member-card {
      display: flex; flex-direction: column; align-items: center; gap: 10px;
      background: #ffffff;
      border: 1.5px solid #e2e8f0;
      border-radius: 16px; padding: 20px 16px;
      text-align: center;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
      transition: border-color .3s, transform .3s, box-shadow .3s;
    }
    .member-card:hover { border-color: rgba(2,132,199,0.5); transform: translateY(-4px); box-shadow: 0 6px 18px rgba(2,132,199,0.1); }
    .avatar {
      width: 56px; height: 56px; border-radius: 50%;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      display: flex; align-items: center; justify-content: center;
      font-size: 22px; font-weight: 800; color: #fff;
    }
    .member-name { font-size: 15px; font-weight: 700; }
    .member-id { font-size: 13px; color: var(--text-muted); }
    .member-role { font-size: 12px; padding: 4px 12px; border-radius: 50px;
      background: rgba(2,132,199,.1); border: 1.5px solid rgba(2,132,199,.4); color: var(--primary); }

    /* Sprint table */
    .sprint-table {
      width: 100%; max-width: 900px; border-collapse: collapse;
      font-size: 13px;
    }
    .sprint-table th {
      background: rgba(2,132,199,.08);
      color: var(--primary); padding: 10px 14px;
      text-align: left; font-size: 12px; letter-spacing: .5px;
      text-transform: uppercase;
    }
    .sprint-table td {
      padding: 9px 14px; border-bottom: 1px solid #e2e8f0;
      color: var(--text-muted); vertical-align: top;
    }
    .sprint-table tr:hover td { background: #f8fafc; }

    /* Stat block */
    .stat-grid {
      display: grid; grid-template-columns: repeat(3,1fr);
      gap: 16px; width: 100%; max-width: 860px;
    }
    .stat-box {
      background: #ffffff;
      border: 1.5px solid #e2e8f0;
      border-radius: 16px; padding: 24px 20px;
      text-align: center;
      box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    }
    .stat-num {
      font-size: clamp(36px,4vw,56px); font-weight: 800;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    }
    .stat-label { font-size: 15px; color: var(--text-muted); margin-top: 6px; }

    /* Cover deco */
    .cover-badges { display: flex; gap: 10px; flex-wrap: wrap; justify-content: center; margin: 18px 0; }

    /* Responsive adjustments */
    @media (max-width: 700px) {
      .card-grid.col-2, .card-grid.col-3 { grid-template-columns: 1fr; }
      .stat-grid { grid-template-columns: 1fr 1fr; }
      h1.hero-title { font-size: 24px; }
      .slide { padding: 20px 20px; }
    }

    /* DB schema pill */
    .db-table-name {
      font-size: 14px; font-weight: 700; color: #5b21b6;
      background: rgba(139,92,246,.1); border: 1.5px solid rgba(139,92,246,.5);
      border-radius: 6px; padding: 4px 12px; margin: 4px 3px;
      display: inline-block;
    }

    /* Architecture diagram */
    .arch-row {
      display: flex; align-items: center; justify-content: center;
      gap: 0; flex-wrap: wrap;
    }
    .arch-box {
      background: rgba(2,132,199,.07);
      border: 1.5px solid rgba(2,132,199,.35);
      border-radius: 12px; padding: 14px 24px;
      font-size: 15px; font-weight: 700; text-align: center;
      white-space: nowrap; color: #0f172a;
    }
    .arch-arrow {
      font-size: 24px; color: #94a3b8; margin: 0 10px;
    }

    /* scrollbar */
    .slide::-webkit-scrollbar { width: 4px; }
    .slide::-webkit-scrollbar-thumb { background: rgba(2,132,199,.3); border-radius: 4px; }

    /* ── LIGHTBOX ── */
    #lightbox {
      position: fixed; inset: 0; z-index: 9999;
      background: rgba(0,0,0,0.82);
      display: flex; align-items: center; justify-content: center;
      opacity: 0; pointer-events: none;
      transition: opacity .3s ease;
      cursor: zoom-out;
      backdrop-filter: blur(6px);
    }
    #lightbox.open { opacity: 1; pointer-events: all; }
    #lightbox img {
      max-width: 95vw; max-height: 95vh;
      object-fit: contain;
      border-radius: 14px;
      box-shadow: 0 24px 80px rgba(0,0,0,0.6);
      transform: scale(0.88);
      transition: transform .35s cubic-bezier(.34,1.56,.64,1);
    }
    #lightbox.open img { transform: scale(1); }
    #lightbox-hint {
      position: absolute; bottom: 22px;
      font-size: 13px; color: rgba(255,255,255,0.55);
      pointer-events: none; letter-spacing: 1px;
    }
    .zoomable {
      cursor: zoom-in;
      transition: transform .25s, box-shadow .25s;
    }
    .zoomable:hover {
      transform: scale(1.015);
      box-shadow: 0 10px 40px rgba(2,132,199,0.28) !important;
    }
  </style>