<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Chào mừng bạn đến với 4ViewsSocial</title>
</head>

<body style="font-family: Arial, sans-serif; background-color: #f6f6f6; padding: 30px;">
    <div style="max-width: 600px; background: #fff; border-radius: 10px; padding: 25px; margin: auto; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
        <h2 style="color: #2b6cb0; text-align: center; margin-bottom: 20px;">🔐 Xác thực tài khoản</h2>
        <p>Xin chào <strong>{{ $user->full_name }}</strong>,</p>
        <p>Cảm ơn bạn đã đăng ký tài khoản tại mạng xã hội <strong>4ViewsSocial</strong>.</p>
        <p>Dưới đây là mã xác thực tài khoản của bạn:</p>

        <div style="text-align: center; margin: 25px 0;">
            <span style="font-size: 28px; font-weight: bold; color: #e53e3e; letter-spacing: 4px; padding: 10px 20px; background: #fff5f5; border: 1px dashed #feb2b2; border-radius: 6px;">
                {{ $code }}
            </span>
        </div>

        <p style="text-align: center; color: #4a5568;">Vui lòng nhập mã này vào ứng dụng để hoàn tất quá trình kích hoạt tài khoản.</p>
        <p style="font-size: 13px; color: #a0aec0; text-align: center; margin-top: 30px;">Nếu bạn không thực hiện yêu cầu đăng ký này, vui lòng bỏ qua email.</p>
    </div>
</body>

</html>
