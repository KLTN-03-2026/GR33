export default function (to, from, next) {
    const token = localStorage.getItem("nhan_vien_token");
    const userStr = localStorage.getItem("nhan_vien_user");
    
    if (token && userStr) {
        let user;
        try {
            user = JSON.parse(userStr);
        } catch (e) {
            next("/admin/login");
            return;
        }
        
        const roleId = Number(user.chuc_vu_id || (user.chucVu ? user.chucVu.id : 0));
        
        // Super Admin luôn có quyền truy cập mọi route
        if (roleId === 1) {
            next();
            return;
        }

        // Định nghĩa quyền hạn tối thiểu để truy cập vào từng trang (Permission ID)
        const routePermissions = {
            'admin-dashboard': 0,    // 0 là public cho mọi nhân viên
            'admin-thong-ke': 61,
            'admin-phe-duyet': 51,
            'admin-nhan-vien': 23,
            'admin-sinh-vien': 26,
            'admin-phong-ban': 21,
            'admin-mon-hoc': 31,
            'admin-lop-hoc': 33,
            'admin-don-vi-cap': 35,
            'admin-chung-chi': 41,
            'admin-bang-diem': 41,
            'admin-du-an': 41,
            'admin-nft': 51,
            'admin-phan-quyen': 11
        };

        const routeName = to.name;
        const requiredPermissionId = routePermissions[routeName];

        // Nếu route không nằm trong danh sách kiểm soát hoặc là public (0)
        if (requiredPermissionId === undefined || requiredPermissionId === 0) {
            next();
            return;
        }

        // Kiểm tra quyền trong list_quyens (Động)
        const listQuyens = (user.list_quyens || []).map(id => Number(id));
        
        // Hỗ trợ cả cấu trúc cũ (chuc_nangs) để đảm bảo ổn định
        const chucVuObj = user.chuc_vu || user.chucVu || {};
        const legacyPermissions = (chucVuObj.chuc_nangs || chucVuObj.chucNangs || []).map(p => Number(p.id));

        if (listQuyens.includes(requiredPermissionId) || legacyPermissions.includes(requiredPermissionId)) {
            next();
        } else {
            console.warn(`Access Denied to ${routeName}. Required Permission: ${requiredPermissionId}`);
            next("/admin"); 
        }
    } else {
        next("/admin/login"); 
    }
}
