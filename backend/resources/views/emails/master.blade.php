<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Hệ thống Quản lý Học thuật' }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8fafc;
            color: #334155;
            -webkit-font-smoothing: antialiased;
        }
        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #f8fafc;
            padding-bottom: 40px;
        }
        .main {
            background-color: #ffffff;
            margin: 0 auto;
            width: 100%;
            max-width: 600px;
            border-spacing: 0;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .header-banner {
            background: linear-gradient(135deg, #BE123C 0%, #DB2777 100%);
            padding: 40px 20px;
            text-align: center;
        }
        .content {
            padding: 40px 30px;
            line-height: 1.6;
        }
        .footer {
            text-align: center;
            padding: 30px;
            font-size: 13px;
            color: #94a3b8;
        }
        .btn-gradient {
            background: linear-gradient(135deg, #BE123C 0%, #DB2777 100%);
            color: #ffffff !important;
            padding: 14px 28px;
            text-decoration: none;
            border-radius: 12px;
            display: inline-block;
            font-weight: bold;
            box-shadow: 0 4px 12px rgba(190, 18, 60, 0.3);
        }
        .logo-text {
            color: #ffffff;
            font-size: 28px;
            font-weight: 800;
            letter-spacing: 1px;
            margin: 0;
            text-transform: uppercase;
        }
        .logo-sub {
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <table class="main">
            <!-- Header -->
            <tr>
                <td class="header-banner">
                    <h1 class="logo-text">DAR Portal</h1>
                    <div class="logo-sub">Decentralized Academic Records</div>
                </td>
            </tr>

            <!-- Content -->
            <tr>
                <td class="content">
                    @yield('content')
                </td>
            </tr>

            <!-- Footer -->
            <tr>
                <td class="footer">
                    <div style="margin-bottom: 15px;">
                        <a href="#" style="color: #94a3b8; text-decoration: none; margin: 0 10px;">Website</a> |
                        <a href="#" style="color: #94a3b8; text-decoration: none; margin: 0 10px;">Hỗ trợ</a> |
                        <a href="#" style="color: #94a3b8; text-decoration: none; margin: 0 10px;">Bảo mật</a>
                    </div>
                    <p style="margin: 5px 0;">© 2026 Học viện Công nghệ. All rights reserved.</p>
                    <p style="margin: 5px 0;">Địa chỉ: 123 Đường Học Thuật, Thành phố Đà Nẵng.</p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
