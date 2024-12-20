<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Hóa Đơn Bán Hàng</title>
    <style>
        body {
            font-family: "Arial", sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            width: 100%;
        }

        table {
            font-size: 14px;
        }

        .invoice {
            width: 100%;
            height: 100vh;
            margin: 0;
            background: #fff;
            border-radius: 0;
            box-shadow: none;
            overflow: hidden;
            padding: 20px;
        }

        .header {
            background: #1677ff;
            color: #fff;
            text-align: center;
            padding: 15px;
            font-size: 18px;
            font-weight: bold;
        }

        .invoice-body {
            padding: 20px;
        }

        .shop-info {
            margin-bottom: 20px;
            text-align: center;
        }

        .shop-info h2 {
            margin: 0;
            font-size: 20px;
        }

        .shop-info p {
            margin: 5px 0;
            font-size: 14px;
            color: #555;
        }

        .order-info {
            margin-bottom: 20px;
            font-size: 14px;
            color: #333;
        }

        .info-line {
            margin-top: 15px;
        }

        .info-line .title {
            font-weight: bold;
            color: #555;
            flex: 1;
        }

        .info-line .value {
            margin-right: 40px;
            text-align: right;
            flex: 2;
            color: #333;
        }

        .product-list {
            width: 100%;
            border-collapse: collapse;
        }

        .product-list th,
        .product-list td {
            text-align: left;
            padding: 8px 10px;
            border-bottom: 1px solid #eee;
        }

        .product-list th {
            background: #f3f3f3;
            font-weight: bold;
        }

        .product-list tr:last-child td {
            border-bottom: none;
        }

        .summary {
            margin-top: 20px;
            font-size: 14px;
        }

        .summary .line {
            display: flex;
            justify-content: space-between;
            margin: 15px 0;
            padding-right: 170px;
        }

        .summary .total {
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }

        .footer {
            text-align: center;
            padding: 15px;
            font-size: 12px;
            color: #888;
            background: #f3f3f3;
        }

        .footer a {
            color: #333;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="invoice">
        <div class="header">Hóa Đơn</div>
        <div class="invoice-body">
            <!-- Thông tin cửa hàng -->
            <div class="shop-info">
                <h2>Cửa Hàng Wolmart</h2>
                <p>Số 5 Trịnh Văn Bô, Nam Từ Niêm, Hà Nội</p>
                <p>Số điện thoại: 0367400518</p>
            </div>

            <!-- Thông tin đơn hàng -->
            <div class="order-info">
                <div class="info-line">
                    <span class="title">Mã đơn hàng:</span>
                    <span class="value">{{ $dataOrder['order_code'] }}</span>
                </div>
                <div class="info-line">
                    <span class="title">Người mua:</span>
                    <span class="value">{{ $dataOrder['customer_name'] }}</span>
                </div>
                <div class="info-line">
                    <span class="title">SDT:</span>
                    <span class="value">{{ $dataOrder['customer_phone'] }}</span>
                </div>
                <div class="info-line">
                    <span class="title">Địa chỉ giao hàng:</span>
                    <span class="value">{{ $dataOrder['shipping_address'] }}</span>
                </div>
                <div class="info-line">
                    <span class="title">Ngày đặt:</span>
                    <span class="value">{{ $dataOrder['ordered_at'] }}</span>
                </div>
            </div>

            <!-- Danh sách sản phẩm -->
            <table class="product-list">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Số lượng</th>
                        <th>Giá</th>
                        <th>Tổng</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($dataOrder['items'] as $item)
                        <tr>
                            <td>{{ $item['name'] }}</td>
                            <td>{{ $item['quantity'] }}</td>
                            <td>{{ $item['price'] }}</td>
                            <td>{{ $item['total'] }}</td>

                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Tóm tắt thanh toán -->
            <div class="summary">
                <div class="line">
                    <span style="color: #555"><strong>Tổng tiền hàng:</strong></span>
                    <span>{{ $dataOrder['total_price'] }}</span>
                </div>
                <div class="line">
                    <span style="color: #555"><strong>Phí giao hàng:</strong></span>
                    <span>{{ $dataOrder['shipping_fee'] }}</span>
                </div>
                <div class="line">
                    <span style="color: #555"><strong>Voucher áp dụng:</strong></span>
                    <span>{{ $dataOrder['discount'] != 0 ? '-' . $dataOrder['discount'] : '0' }}</span>
                </div>
                <div class="line total">
                    <span>Tổng thanh toán:</span>
                    <span>{{ $dataOrder['final_price'] }}</span>
                </div>
            </div>
        </div>
        <!-- Footer -->
        <div class="footer">
            Cảm ơn bạn đã mua hàng! <br />
            <a href="#">Wolmart Shop</a>
        </div>
    </div>
</body>

</html>
