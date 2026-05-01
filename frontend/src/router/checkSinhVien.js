export default function (to, from, next) {
    const token = localStorage.getItem("sinh_vien_token");
    if (token) {
        next();
    } else {
        next("/sinh-vien/login");
    }
}
