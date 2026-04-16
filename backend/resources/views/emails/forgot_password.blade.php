@extends('emails.master')

@section('content')
    <div style="text-align: center;">
        <h2 style="color: #1e293b; font-size: 24px; margin-bottom: 20px;">Xác nhận khôi phục mật khẩu</h2>
        <p style="font-size: 16px; color: #64748b;">Chào bạn,</p>
        <p style="font-size: 16px; color: #64748b;">Chúng tôi nhận được yêu cầu khôi phục mật khẩu cho tài khoản của bạn trên hệ thống <strong>DAR Portal</strong>. Vui lòng nhấn vào nút bên dưới để tiến hành thiết lập lại mật khẩu mới:</p>
        
        <!-- Action Button -->
        <div style="margin: 40px 0;">
            <a href="{{ $url ?? '#' }}" class="btn-gradient" style="font-size: 18px; letter-spacing: 0.5px;">
                Cập nhật mật khẩu mới
            </a>
        </div>

        <p style="font-size: 14px; color: #94a3b8; margin-bottom: 30px; padding: 0 20px;">
            <i class="bi bi-clock"></i> Đường link này có hiệu lực trong vòng <strong>5 phút</strong>. 
            Để đảm bảo an toàn, tuyệt đối không chia sẻ email này cho bất kỳ ai.
        </p>

        <div style="background-color: #f8fafc; border-radius: 12px; padding: 15px; margin-top: 20px;">
            <p style="font-size: 12px; color: #94a3b8; margin: 0;">Nếu nút bấm trên không hoạt động, bạn có thể sao chép và dán đường link dưới đây vào trình duyệt:</p>
            <p style="font-size: 11px; color: #BE123C; word-break: break-all; margin-top: 10px;">
                {{ $url ?? '#' }}
            </p>
        </div>

        <div style="margin-top: 50px; padding-top: 25px; border-top: 1px solid #e2e8f0;">
            <p style="font-size: 13px; color: #94a3b8;">Nếu bạn không yêu cầu thay đổi này, hãy bỏ qua email này. Tài khoản của bạn vẫn sẽ được an toàn.</p>
        </div>
    </div>
@endsection
