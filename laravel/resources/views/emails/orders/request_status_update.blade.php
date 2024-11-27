<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cập nhật trạng thái yêu cầu</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background-color: #4caf50;
            color: #ffffff;
            text-align: center;
            padding: 20px;
        }
        .header h1 {
            font-size: 24px;
            margin: 0;
        }
        .content {
            padding: 20px;
            color: #333333;
            line-height: 1.6;
        }
        .status {
            font-size: 18px;
            font-weight: bold;
            color: #4caf50;
            text-transform: uppercase;
        }
        .reason {
            font-size: 16px;
            color: #d9534f;
            margin-top: 10px;
        }
        .footer {
            text-align: center;
            padding: 15px;
            font-size: 14px;
            color: #777777;
            background-color: #f1f1f1;
        }
        .cta {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #4caf50;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .cta:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Yêu cầu cập nhật trạng thái</h1>
        </div>
        <div class="content">

            <p>Xin chào <strong>{{ $data->requester->fullname }}</strong>,</p>
            <p>Yêu cầu của bạn với mã đơn hàng <strong>#{{ $data->order->code }}</strong> đã được cập nhật.</p>
            <p>Trạng thái hiện tại:</p>
            <p class="status">{{ $data->status }}</p>

            @if($data->status === 'rejected')
                <p class="reason">Lý do từ chối: {{ $data->rejection_reason }}</p>
            @endif

            @if($data->status === 'approved')
                <p>Chúc mừng! Yêu cầu của bạn đã được phê duyệt bởi quản trị viên. Bạn có thể tiến hành các bước tiếp theo theo hướng dẫn.</p>
            @else
                <p>Nếu bạn có bất kỳ câu hỏi nào hoặc cần hỗ trợ thêm, vui lòng liên hệ với chúng tôi.</p>
            @endif

        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Công ty của bạn. Mọi quyền được bảo lưu.
        </div>
    </div>
</body>
</html>
