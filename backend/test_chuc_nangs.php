<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$nhanVien = \App\Models\NhanVien::with('chucVu.chucNangs')->first();
echo json_encode($nhanVien->toArray(), JSON_PRETTY_PRINT);
