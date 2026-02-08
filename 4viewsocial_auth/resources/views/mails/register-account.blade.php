<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Chào mừng bạn đến với Ticket</title>
</head>

<body style="font-family: Arial, sans-serif; background-color: #f6f6f6; padding: 30px;">
    <div
        style="max-width: 600px; background: #fff; border-radius: 10px; padding: 20px; margin: auto; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <h2 style="color: #2b6cb0; text-align: center;">🔐 Xác thực tài khoản</h2>
        <p>Xin chào <strong>{{ $user->full_name }}</strong>,</p>
        <p>Cảm ơn bạn đã đăng ký tài khoản tại <strong>Ticket</strong>.</p>
        <p>Dưới đây là mã xác thực của bạn:</p>

        <h3 style="text-align:center; font-size: 24px; color: #e53e3e; letter-spacing: 3px;">{{ $code }}</h3>

        <p style="text-align: center;">Vui lòng nhập mã này trong ứng dụng để hoàn tất quá trình xác minh email.</p>

        <p style="font-size: 13px; color: #666; text-align:center;">Nếu bạn không thực hiện đăng ký, vui lòng bỏ qua
            email này.</p>
    </div>
</body>

</html>
