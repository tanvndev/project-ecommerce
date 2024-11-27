<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yêu Cầu Thay Đổi Trạng Thái Đơn Hàng</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #e9eff3;
            padding: 20px;
            color: #333;
            line-height: 1.6;
        }

        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 0 auto;
            transition: transform 0.2s;
        }

        .container:hover {
            transform: scale(1.02);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h2 {
            color: #27ae60;
            font-size: 26px;
            margin: 0;
            font-weight: 600;
        }

        .order-info {
            font-size: 16px;
        }

        .order-info p {
            margin: 15px 0;
            padding: 10px;
            border-left: 4px solid #3498db;
            background-color: #f9f9f9;
        }

        .order-info span {
            font-weight: bold;
            color: #2c3e50;
        }

        .button {
            display: inline-block;
            background-color: #3498db;
            color: #ffffff;
            padding: 12px 25px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            margin-top: 25px;
            transition: background-color 0.3s;
        }

        .button:hover {
            background-color: #2980b9;
        }

        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 14px;
            color: #777;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>Yêu Cầu Thay Đổi Trạng Thái Đơn Hàng</h2>
        </div>
        <div class="order-info">
            <p><span>Mã Đơn Hàng:</span> {{ $data['code'] }}</p>
            <p><span>Gửi Bởi:</span> {{ $data['name'] }} - {{ $data['requested_by'] }}</p>
            <p><span>Trạng Thái Hiện Tại:</span> {{ $data['current_status'] }}</p>
            <p><span>Trạng Thái Yêu Cầu Thay Đổi:</span> {{ $data['requested_status'] }}</p>
            <p><span>Lý Do:</span> {{ $data['reason'] }}</p>
        </div>

        <a href="{{ $data['url'] }}" class="button">Xem Đơn Hàng</a>

        <div class="footer">
            <p> Vui lòng kiểm tra và xác thực yêu cầu này.</p>
        </div>
    </div>
</body>

</html>
