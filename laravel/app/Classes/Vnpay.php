<?php

namespace App\Classes;

class Vnpay
{
    public static function payment($order)
    {
        $vnpConfig = config('apps.payment-config')['vnpay'];
        //Config
        $vnp_Url = $vnpConfig['vnp_Url'];
        $vnp_TmnCode = $vnpConfig['vnp_TmnCode'];
        $vnp_HashSecret = $vnpConfig['vnp_HashSecret'];
        $vnp_ReturnUrl = $vnpConfig['vnp_ReturnUrl'];

        $locale = 'vn';

        // Tạo một mảng chứa thông tin cần thiết
        $final_amount = $order->final_price;

        $inputData = [
            'vnp_Version'    => '2.1.0',
            'vnp_TmnCode'    => $vnp_TmnCode,
            'vnp_Amount'     => $final_amount * 100,
            'vnp_Command'    => 'pay',
            'vnp_CreateDate' => date('YmdHis'),
            'vnp_CurrCode'   => 'VND',
            'vnp_IpAddr'     => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '10.240.239.133',
            'vnp_Locale'     => $locale,
            'vnp_OrderInfo'  => $order->note ?? 'Thanh toan don hang ' . $order->code . ' qua VNPAY.',
            'vnp_OrderType'  => 'billpayment',
            'vnp_ReturnUrl'  => $vnp_ReturnUrl,
            'vnp_TxnRef'     => $order->code,
        ];

        // Thêm thông tin nhưng không bắt buộc
        if (isset($order->vnp_BankCode) && $order->vnp_BankCode != '') {
            $inputData['vnp_BankCode'] = $order->vnp_BankCode;
        }

        if (isset($order->vnp_Bill_State) && $order->vnp_Bill_State != '') {
            $inputData['vnp_Bill_State'] = $order->vnp_Bill_State;
        }

        ksort($inputData);

        // Xây dựng chuỗi hash
        $hashdata = http_build_query($inputData, '', '&');

        $vnp_Url = $vnp_Url . '?' . $hashdata;

        // Thêm vnp_SecureHash vào URL nếu có vnp_HashSecret
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= '&vnp_SecureHash=' . $vnpSecureHash;
        }

        $returnData = [
            'status'   => 'success',
            'messages' => 'Tạo liên kết thanh toán thành công.',
            'url'      => $vnp_Url,
        ];

        return $returnData;
    }
}
