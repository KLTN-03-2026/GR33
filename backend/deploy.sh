#!/bin/bash

# Script: deploy.sh
# Mục tiêu: Khởi chạy Laravel trên DigitalOcean App Platform
# (Foundry đã được cài đặt trong Giai đoạn Build qua composer.json)

echo "--------------------------------------------------------"
echo "--- BẮT ĐẦU KHỞI CHẠY HỆ THỐNG (RUNTIME) ---"
echo "--------------------------------------------------------"

# 1. Đảm bảo đang ở thư mục gốc của backend
cd /workspace/backend

# 2. Thiết lập đường dẫn Foundry
export FOUNDRY_DIR="/workspace/backend/.foundry"

# 3. Đảm bảo quyền thực thi cho cast (đã được cài đặt từ giai đoạn build)
if [ -f "$FOUNDRY_DIR/bin/cast" ]; then
    echo "[Foundry] Đã sẵn sàng tại: $FOUNDRY_DIR/bin/cast"
    chmod +x $FOUNDRY_DIR/bin/cast
    # Kiểm tra nhanh phiên bản để xác nhận hoạt động
    $FOUNDRY_DIR/bin/cast --version
else
    echo "[CẢNH BÁO] Không tìm thấy cast! Vui lòng kiểm tra lại Build Logs trên DigitalOcean."
fi

# 4. Khởi chạy ứng dụng với Apache (Lệnh chuẩn cho PHP Buildpack)
echo "--------------------------------------------------------"
echo "[Server] Đang khởi chạy heroku-php-apache2..."
echo "--------------------------------------------------------"
exec heroku-php-apache2 public/
