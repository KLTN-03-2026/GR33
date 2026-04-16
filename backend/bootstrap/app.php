<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'check.lock' => \App\Http\Middleware\CheckRecordLock::class,
            'check.permission' => \App\Http\Middleware\CheckPermission::class,
        ]);

        $middleware->redirectGuestsTo(fn () => null);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // 1. Xử lý lỗi Xác thực (Chưa đăng nhập)
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->is('api/admin/*')) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Phiên làm việc admin đã hết hạn hoặc bạn không có quyền.'
                ], 200);
            }
            return response()->json([
                'status'  => false,
                'message' => 'Bạn chưa đăng nhập.'
            ], 200);
        });

        // 2. Xử lý lỗi Xác thực dữ liệu (Validation)
        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, $request) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
                'errors'  => $e->errors(),
            ], 200);
        });

        // 3. Xử lý lỗi Không tìm thấy (404)
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            return response()->json([
                'status'  => false,
                'message' => 'Đường dẫn hoặc dữ liệu yêu cầu không tồn tại.'
            ], 200);
        });

        // 4. Xử lý lỗi Quyền truy cập (403)
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $e, $request) {
            return response()->json([
                'status'  => false,
                'message' => 'Bạn không có quyền thực hiện hành động này.'
            ], 200);
        });

        // 5. Xử lý các lỗi hệ thống khác (500)
        $exceptions->render(function (\Throwable $e, $request) {
            if ($request->is('api/*')) {
                // Nếu là lỗi Validation hoặc Authentication đã có xử lý riêng thì bỏ qua
                if ($e instanceof \Illuminate\Validation\ValidationException || 
                    $e instanceof \Illuminate\Auth\AuthenticationException ||
                    $e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException ||
                    $e instanceof \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException) {
                    return null;
                }

                return response()->json([
                    'status'  => false,
                    'message' => 'Lỗi hệ thống: ' . $e->getMessage(),
                ], 200);
            }
        });
    })->create();
