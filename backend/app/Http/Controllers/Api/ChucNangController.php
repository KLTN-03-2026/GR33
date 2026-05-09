<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChucNang;
use Illuminate\Http\Request;

class ChucNangController extends Controller
{
    public function getDataChucNang()
    {
        $data = ChucNang::all();
        return response()->json([
            'status' => true,
            'message' => 'Lấy dữ liệu chức năng thành công',
            'data' => $data,
        ]);
    }
}
