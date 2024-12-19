<?php

namespace App\Classes;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class Stringee
{
    private static $url = 'https://api.stringee.com/v1/call2/callout';

    private static $fromPhone = '842471098512';

    public static function sendVerificationCode($request, $user, string $key = 'verification_code_')
    {
        if (! $user->phone) {
            return errorResponse('Số điện thoại không chính xác.');
        }

        $client = new Client;
        $verificationCode = rand(100000, 999999);
        $formatVerificationCode = numberToWords($verificationCode);
        $phone = convertPhoneNumber($user->phone);

        $data = [
            'from' => [
                'type'   => 'external',
                'number' => self::$fromPhone,
                'alias'  => 'Stringee',
            ],
            'to' => [[
                'type'   => 'external',
                'number' => $phone,
                'alias'  => 'Customer',
            ]],
            'answer_url' => env('STRINGEE_ANSWER_URL'),
            'actions'    => [[
                'action' => 'talk',
                'text'   => 'Vui lòng không chia sẻ mã cho bất kì ai. Mã xác nhận của bạn là . ' . $formatVerificationCode . '. Mã sẽ được lặp lại. Mã xác nhận của bạn là . ' . $formatVerificationCode . '. Mã hết hạn sau mười năm phút.',
            ]],
        ];

        try {
            $response = $client->post(self::$url, [
                'headers' => [
                    'X-STRINGEE-AUTH' => env('STRINGEE_API_KEY'),
                    'Content-Type'    => 'application/json',
                ],
                'json' => $data,
            ]);

            $responseBody = json_decode($response->getBody(), true);
            Cache::put($key . $user->phone, $verificationCode, 15 * 60);

            return successResponse('Gửi mã xác nhận thành công.', $responseBody);
        } catch (Exception $e) {
            return errorResponse('Không thể thực hiện cuộc gọi');
        }
    }

    public static function verifyCode($request, $user, $key = 'verification_code_')
    {
        $verificationCode = Cache::get($key . $user->phone);

        if (! $verificationCode) {
            Cache::forget($key . $user->phone);

            return errorResponse(__('messages.auth.invalid_code.error'));
        }

        if ($verificationCode != $request->verification_code) {
            Cache::forget($key . $user->phone);

            return errorResponse(__('messages.auth.invalid_code.error'));
        }

        Cache::forget($key . $user->phone);

        return successResponse('Mã xác nhận chính xác.');
    }
}
