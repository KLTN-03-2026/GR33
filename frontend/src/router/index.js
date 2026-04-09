import { createRouter, createWebHistory } from "vue-router"; // cài vue-router: npm install vue-router@next --save

const routes = [
    {
        path : '/trang-chu',
        component: ()=>import('../components/Home/index.vue')
    },
    {
        path : '/danh-muc',
        component: ()=>import('../components/DanhMuc/index.vue')
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes: routes
})

export default router