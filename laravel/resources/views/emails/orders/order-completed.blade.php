<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Order Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            padding: 20px;
        }

        .header {
            text-align: center;
            background-color: #ff5d5d;
            color: #ffffff;
            padding: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .greeting {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .order-info,
        .voucher-info {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .order-info p,
        .voucher-info p {
            margin: 0;
            font-size: 14px;
            color: #333;
        }

        .order-details,
        .billing-address {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .order-details th,
        .order-details td {
            padding: 10px;
            border: 1px solid #dddddd;
            text-align: left;
        }

        .order-details th {
            background-color: #f4f4f4;
            font-weight: bold;
        }

        .order-summary {
            margin-top: 20px;
        }

        .order-info p {
            margin-top: 5px;
        }

        .billing-title {
            color: #8e44ad;
            font-weight: bold;
            font-size: 16px;
            margin-top: 30px;
        }

        .billing-address td {
            padding: 10px;
            background-color: #f4f4f4;
            border: 1px solid #dddddd;
        }

        .product-list {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .product-list th,
        .product-list td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .product-list th {
            background-color: #f4f4f4;
            font-weight: bold;
        }

        .total,
        .grand-total {
            text-align: right;
            font-size: 16px;
            color: #333;
            font-weight: bold;
        }

        .shipping-info {
            font-size: 14px;
            color: #333;
        }

        .shipping-info p {
            margin-top: 5px;
        }

        .footer {
            text-align: center;
            color: #777;
            font-size: 12px;
            padding: 20px;
            border-top: 1px solid #ddd;
            margin-top: 20px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            color: #ffffff;
            background-color: #ff5d5d;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 10px;
        }

        .btn:hover {
            background-color: #ff4040;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Thông báo đơn hàng hoàn thành</h1>
        </div>

        <div class="greeting">
            <p>Xin chào <strong>{{ $order->customer_name }}</strong>,</p>
            <p>
                Cảm ơn bạn đã mua sắm tại cửa hàng của chúng tôi! Đơn hàng của bạn đã được xác nhận hoàn thành, hi vọng
                bạn ưng đơn hàng này. Dưới đây là chi tiết đơn hàng của bạn:
            </p>
        </div>

        <div class="order-info">
            <p><strong>Mã đơn hàng:</strong> {{ $order->code }}</p>
            <p><strong>Ngày đặt hàng:</strong> {{ formatTimeOrder($order->created_at) }}</p>
            <p><strong>Trạng thái đơn hàng:</strong> Hoàn thành (Đã giao tới khách hàng)</p>
            <p><strong>Thời gian thanh toán:</strong> {{ formatTimeOrder($order->updated_at) }}</p>

        </div>

        <div class="order-details">
            <h2>Chi tiết đơn hàng (#{{ $order->code }})</h2>
            <table class="order-details">
                <tbody>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Số lượng</th>
                        <th>Giá</th>
                    </tr>

                    @foreach ($order->order_items as $item)
                        <tr>
                            <td>{{ $item->product_variant_name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format($item->quantity * $item->price, 0, ',', '.') }}đ</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="2"><strong>Tổng phụ:</strong></td>
                        <td>{{ number_format($order->total_price, 0, ',', '.') }}đ</td>
                    </tr>
                    <tr>
                        <td colspan="2"><strong>Vận chuyển:</strong></td>
                        <td>

                            {{ number_format($order->shipping_fee, 0, ',', '.') ?? 0 }}đ
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"><strong>Phiếu giảm giá:</strong></td>
                        <td>
                            - {{ number_format($order->discount, 0, ',', '.') ?? 0 }}đ
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"><strong>Phương thức thanh toán:</strong></td>
                        <td>{{ $order->payment_method->name }}</td>
                    </tr>
                    <tr>
                        <td colspan="2"><strong>Tổng:</strong></td>
                        <td>{{ number_format($order->final_price, 0, ',', '.') }}đ</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="shipping-info">
            <h2>Thông tin vận chuyển</h2>
            <p><strong>Họ Tên:</strong> {{ $order->customer_name }}</p>
            <p><strong>Địa chỉ:</strong> {{ $order->shipping_address }}</p>
            <p><strong>Số điện thoại:</strong> {{ $order->customer_phone }}</p>
            <p><strong>Ghi chú:</strong> {{ $order->note ?? '' }}</p>
        </div>

        <div class="footer">
            <a href="#" class="btn">Đi tới đơn hàng của bạn</a>
            <p>Để được hỗ trợ về đơn hàng của bạn, vui lòng liên hệ với chúng tôi theo địa chỉ DATN59@gmail.com</p>
            <p>Cảm ơn bạn đã chọn chúng tôi!</p>
        </div>
    </div>
</body>

</html>
