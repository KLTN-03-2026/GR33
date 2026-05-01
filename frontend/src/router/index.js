import { createRouter, createWebHistory } from "vue-router";
import checkAdmin from "./checkAdmin";
import checkSinhVien from "./checkSinhVien";

const routes = [
    // ADMIN (Staff)
    {
        path: "/admin/login",
        component: () => import("../components/Admin/Login/index.vue"),
        meta: { layout: "blank" }
    },
    {
        path: "/admin/forgot-password",
        component: () => import("../components/Admin/QuenMatKhau/index.vue"),
        meta: { layout: "blank" }
    },
    {
        path: "/admin/reset-password",
        component: () => import("../components/Admin/DatLaiMatKhau/index.vue"),
        meta: { layout: "blank" }
    },
    {
        path: "/admin",
        component: () => import("../layout/wrapper/Admin/index.vue"),
        children: [
            {
                path: "",
                name: "admin-dashboard",
                component: () => import("../components/Admin/Dashboard/index.vue"),
                beforeEnter: checkAdmin
            },
            {
                path: 'phong-ban',
                name: 'admin-phong-ban',
                component: () => import("../components/Admin/PhongBan/index.vue"),
                beforeEnter: checkAdmin
            },
            {
                path: 'phan-quyen',
                name: 'admin-phan-quyen',
                component: () => import("../components/Admin/PhanQuyen/index.vue"),
                beforeEnter: checkAdmin
            },
            {
                path: 'nhan-vien',
                name: 'admin-nhan-vien',
                component: () => import("../components/Admin/NhanVien/index.vue"),
                beforeEnter: checkAdmin
            },
            {
                path: 'sinh-vien',
                name: 'admin-sinh-vien',
                component: () => import("../components/Admin/SinhVien/index.vue"),
                beforeEnter: checkAdmin
            },
            {
                path: 'mon-hoc',
                name: 'admin-mon-hoc',
                component: () => import("../components/Admin/MonHoc/index.vue"),
                beforeEnter: checkAdmin
            },
            {
                path: 'lop-hoc',
                name: 'admin-lop-hoc',
                component: () => import("../components/Admin/LopHoc/index.vue"),
                beforeEnter: checkAdmin
            },
            {
                path: 'chung-chi',
                name: 'admin-chung-chi',
                component: () => import("../components/Admin/ChungChi/index.vue"),
                beforeEnter: checkAdmin
            },
            {
                path: 'bang-diem',
                name: 'admin-bang-diem',
                component: () => import("../components/Admin/BangDiem/index.vue"),
                beforeEnter: checkAdmin
            },
            {
                path: 'du-an',
                name: 'admin-du-an',
                component: () => import("../components/Admin/DuAn/index.vue"),
                beforeEnter: checkAdmin
            },
            {
                path: 'don-vi-cap',
                name: 'admin-don-vi-cap',
                component: () => import("../components/Admin/DonViCap/index.vue"),
                beforeEnter: checkAdmin
            },
            {
                path: 'thong-ke',
                name: 'admin-thong-ke',
                component: () => import("../components/Admin/ThongKe/index.vue"),
                beforeEnter: checkAdmin
            },
            {
                path: 'phe-duyet',
                name: 'admin-phe-duyet',
                component: () => import("../components/Admin/PheDuyet/index.vue"),
                beforeEnter: checkAdmin
            },
            {
                path: 'nft',
                name: 'admin-nft',
                component: () => import("../components/Admin/Nft/index.vue"),
                beforeEnter: checkAdmin
            },
            {
                path: 'setting',
                name: 'admin-setting',
                component: () => import("../components/Admin/Setting/index.vue"),
                beforeEnter: checkAdmin
            },
        ]
    },

    // SINH VIEN (Student)
    {
        path: "/sinh-vien/login",
        component: () => import("../components/SinhVien/Login/index.vue"),
        meta: { layout: "blank" }
    },
    {
        path: "/sinh-vien/forgot-password",
        component: () => import("../components/SinhVien/QuenMatKhau/index.vue"),
        meta: { layout: "blank" }
    },
    {
        path: "/sinh-vien/reset-password",
        component: () => import("../components/SinhVien/DatLaiMatKhau/index.vue"),
        meta: { layout: "blank" }
    },
    {
        path: "/sinh-vien",
        component: () => import("../layout/wrapper/SinhVien/index.vue"),
        beforeEnter: checkSinhVien,
        children: [
            {
                path: "",
                name: "SinhVienDashboard",
                component: () => import("../components/SinhVien/Dashboard/index.vue"),
                beforeEnter: checkSinhVien
            },
            {
                path: "ket-qua",
                name: "SinhVienKetQua",
                component: () => import("../components/SinhVien/BangDiem/index.vue"),
                beforeEnter: checkSinhVien
            },
            {
                path: "chung-chi",
                name: "SinhVienChungChi",
                component: () => import("../components/SinhVien/ChungChi/index.vue"),
                beforeEnter: checkSinhVien
            },
            {
                path: "du-an",
                name: "SinhVienDuAn",
                component: () => import("../components/SinhVien/DuAn/index.vue"),
                beforeEnter: checkSinhVien
            },
            {
                path: "mon-hoc",
                name: "SinhVienMonHoc",
                component: () => import("../components/SinhVien/MonHoc/index.vue"),
                beforeEnter: checkSinhVien
            },
            {
                path: "lop-hoc",
                name: "SinhVienLopHoc",
                component: () => import("../components/SinhVien/LopHoc/index.vue"),
                beforeEnter: checkSinhVien
            },
            {
                path: "setting",
                name: "SinhVienSetting",
                component: () => import("../components/SinhVien/Setting/index.vue"),
                beforeEnter: checkSinhVien
            },
        ]
    },
    {
        path: "/xac-minh-van-bang/:tokenId",
        name: "PublicXacMinh",
        component: () => import("../components/Public/XacMinh/index.vue"),
        meta: { layout: "blank" }
    },
    // Default redirect
    {
        path: "/",
        redirect: "/sinh-vien"
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;