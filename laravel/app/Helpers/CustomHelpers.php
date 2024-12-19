<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

if (! function_exists('getServiceInstance')) {
    function getServiceInstance($modelName)
    {
        $folderName = str_replace(['Catalogue', 'Variant', 'Review'], '', $modelName);

        $serviceInterfaceNameSpace = 'App\Services\Interfaces\\' . ucfirst($folderName) . '\\' . ucfirst($modelName) . 'ServiceInterface';
        if (interface_exists($serviceInterfaceNameSpace)) {
            // hàm app() giúp truy cập các đối tượng đã đăng ký trong container
            return app($serviceInterfaceNameSpace);
        }

        return null;
    }
}
if (! function_exists('generateStrongPassword')) {
    function generateStrongPassword(int $length = 12)
    {
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $specialChars = '!@#$%^&*()_+-=[]{}|';

        $allChars = $uppercase . $lowercase . $numbers . $specialChars;
        $password = '';

        for ($i = 0; $i < $length; $i++) {
            $password .= $allChars[random_int(0, strlen($allChars) - 1)];
        }

        return $password;
    }
}
if (! function_exists('getRepositoryInstance')) {
    function getRepositoryInstance(string $modelName)
    {
        $folderName = str_replace(['Catalogue', 'Variant'], '', $modelName);

        $repositoryInterfaceNameSpace = 'App\Repositories\Interfaces\\' . ucfirst($folderName) . '\\' . ucfirst($modelName) . 'RepositoryInterface';
        if (interface_exists($repositoryInterfaceNameSpace)) {
            // hàm app() giúp truy cập các đối tượng đã đăng ký trong container
            return app($repositoryInterfaceNameSpace);
        }

        return null;
    }
}

if (! function_exists('formatToCommas')) {
    function formatToCommas($nStr)
    {
        $nStr = strval($nStr);
        $nStr = str_replace('.', '', $nStr);
        $str = '';
        for ($i = strlen($nStr); $i > 0; $i -= 3) {
            $a = $i - 3 < 0 ? 0 : $i - 3;
            $str = substr($nStr, $a, $i - $a) . '.' . $str;
        }
        $str = substr($str, 0, -1);

        return $str;
    }
}

if (! function_exists('getColorForStock')) {
    function getColorForStock($stock): string
    {
        if ($stock <= 0) {
            return 'red';
        }

        if ($stock <= 5) {
            return 'orange';
        }

        return 'green';
    }
}

if (! function_exists('formatIso8601ToDatetime')) {
    function formatIso8601ToDatetime($isoDate)
    {
        if (empty($isoDate)) {
            return null;
        }
        // Parse the ISO 8601 date-time string to a Carbon instance
        $carbonDate = \Carbon\Carbon::parse($isoDate);

        // Format the Carbon instance to 'YYYY-mm-dd H:i:s'
        return $carbonDate->format('Y-m-d H:i:s');
    }
}

if (! function_exists('convertPrice')) {

    function convertPrice($priceString)
    {
        $priceWithoutDots = str_replace('.', '', $priceString);
        // Chuyển đổi chuỗi thành số nguyên
        $price = intval($priceWithoutDots);

        return $price;
    }
}

if (! function_exists('generateOrderCode')) {

    function generateOrderCode($prefix = 'ORD')
    {
        $prefix = strtoupper($prefix);
        $date = date('Ymd');
        $uniqueId = uniqid();
        $uniqueHash = substr(md5($uniqueId), 0, 6);
        $orderCode = $prefix . $date . strtoupper($uniqueHash);

        return $orderCode;
    }
}

if (! function_exists('formatCurrency')) {

    function formatCurrency($amount, string $currencyCode = 'vn')
    {
        switch (strtoupper($currencyCode)) {
            case 'VN':
                // Định dạng cho tiền tệ Việt Nam (VND)
                return number_format($amount, 0, ',', '.') . ' ₫';
            case 'CN':
                // Định dạng cho tiền tệ Trung Quốc (CNY)
                return '¥' . number_format($amount, 2, '.', ',');
            case 'EN':
                // Định dạng cho tiền tệ Hoa Kỳ (USD)
                return '$' . number_format($amount, 2, '.', ',');
            default:
                // Nếu mã tiền tệ không được hỗ trợ, trả về số tiền gốc
                return $amount;
        }
    }
}

if (! function_exists('recursive')) {

    function recursive($data, $parent_id = 0)
    {
        $result = [];
        if (! is_null($data) && count($data) > 0) {
            foreach ($data as $key => $value) {
                if ($value->parent_id == $parent_id) {
                    $result[] = [
                        'item'     => $value,
                        'children' => recursive($data, $value->id),
                    ];
                }
            }
        }

        return $result;
    }
}

if (! function_exists('convertDateTime')) {
    function convertDateTime($dateTime = '', $format = 'd/m/Y H:i')
    {
        return date($format, strtotime($dateTime));
    }
}

if (! function_exists('cut_string_and_decode')) {
    function cut_string_and_decode($str = null, $n = 200)
    {
        $str = html_entity_decode($str);
        $str = strip_tags($str);
        if (mb_strlen($str) > $n) {
            $str = mb_substr($str, 0, $n) . '...';
        }

        return $str;
    }
}
if (! function_exists('sortString')) {
    function sortString($string)
    {
        if ($string == '') {
            return '';
        }

        $array = explode(',', $string);
        sort($array, SORT_NUMERIC);
        $sortedNumbers = implode(',', $array);

        return $sortedNumbers;
    }
}

if (! function_exists('generateStar')) {
    function generateStar($rate)
    {
        $filledStars = round($rate, 0);
        $starArray = [];

        for ($index = 0; $index < 5; $index++) {
            if ($index < $filledStars) {
                $starArray[] = '<i class="fas fa-star"></i>';
            } else {
                $starArray[] = '<i class="far fa-star"></i>';
            }
        }

        return implode(' ', $starArray);
    }
}

if (! function_exists('generateStarPercent')) {
    function generateStarPercent($rate = 100)
    {
        $percent = round(100 - ($rate / 5 * 100));

        // Start building the HTML string
        $html = '
        <div class="stars-percent lh-1">
        ';

        // Generate 5 stars
        for ($i = 0; $i < 5; $i++) {
            $html .= '
            <svg viewBox="0 0 940.688 940.688">
                <path d="M885.344,319.071l-258-3.8l-102.7-264.399c-19.8-48.801-88.899-48.801-108.6,0l-102.7,264.399l-258,3.8 c-53.4,3.101-75.1,70.2-33.7,103.9l209.2,181.4l-71.3,247.7c-14,50.899,41.1,92.899,86.5,65.899l224.3-122.7l224.3,122.601 c45.4,27,100.5-15,86.5-65.9l-71.3-247.7l209.2-181.399C960.443,389.172,938.744,322.071,885.344,319.071z"/>
            </svg>
            ';
        }

        // Add overlay with dynamic width
        $html .= '
            <div class="overlay" style="width: ' . htmlspecialchars($percent) . '%;"></div>
        </div>
        ';

        return $html;
    }
}

if (! function_exists('renderProress')) {
    function renderProress($rate = 100)
    {
        $percent = round(100 - ($rate / 5 * 100));

        // Start building the HTML string
        $html = '
        <div class="stars-percent">
        ';

        // Generate 5 stars
        for ($i = 0; $i < 5; $i++) {
            $html .= '
            <svg width="auto" height="auto" viewBox="0 0 940.688 940.688">
                <path d="M885.344,319.071l-258-3.8l-102.7-264.399c-19.8-48.801-88.899-48.801-108.6,0l-102.7,264.399l-258,3.8 c-53.4,3.101-75.1,70.2-33.7,103.9l209.2,181.4l-71.3,247.7c-14,50.899,41.1,92.899,86.5,65.899l224.3-122.7l224.3,122.601 c45.4,27,100.5-15,86.5-65.9l-71.3-247.7l209.2-181.399C960.443,389.172,938.744,322.071,885.344,319.071z"/>
            </svg>
            ';
        }

        // Add overlay with dynamic width
        $html .= '
            <div class="overlay" style="width: ' . htmlspecialchars($percent) . '%;"></div>
        </div>
        ';

        return $html;
    }
}

if (! function_exists('sortAttributeId')) {
    function sortAttributeId($attributeId)
    {
        sort($attributeId, SORT_NUMERIC);
        $attributeId = implode(', ', $attributeId);

        return $attributeId;
    }
}

if (! function_exists('starsToPercent')) {
    function starsToPercent($stars, $maxStars = 5)
    {
        if ($maxStars <= 0) {
            throw new \InvalidArgumentException('Max stars must be greater than zero.');
        }

        return round(($stars / $maxStars) * 100, 2);
    }
}

if (! function_exists('convertVndTo')) {

    function convertVndTo($amountVnd, $currency = 'USD')
    {
        // Example exchange rates; in a real application, you would get these from an API or a config file.
        $exchangeRates = [
            'USD' => 0.000043,
            'EUR' => 0.000038,
            // Add more currencies as needed
        ];

        if (! isset($exchangeRates[$currency])) {
            throw new Exception("Exchange rate for currency '{$currency}' not found.");
        }

        $result = number_format($amountVnd * $exchangeRates[$currency], 2, '.', '');

        return $result;
    }
}

if (! function_exists('abbreviateName')) {
    function abbreviateName($fullName)
    {
        $parts = explode(' ', $fullName);
        $abbreviation = '';
        foreach ($parts as $part) {
            $abbreviation .= strtoupper(substr($part, 0, 1));
        }

        return $abbreviation;
    }
}

if (! function_exists('errorResponse')) {
    function errorResponse(string $message, bool $isResponse = false)
    {
        $response = [
            'status'   => 'error',
            'messages' => $message,
            'data'     => null,
        ];

        return $isResponse ? response()->json($response, 500) : $response;
    }
}

if (! function_exists('successResponse')) {
    function successResponse(string $message, $data = null, bool $isResponse = false)
    {
        $response = [
            'status'   => 'success',
            'messages' => $message,
            'data'     => $data,
        ];

        return $isResponse ? response()->json($response) : $response;
    }
}

if (! function_exists('handleResponse')) {
    function handleResponse(array $response, int $successCode = 200)
    {
        $status = $response['status'] ?? '';

        $statusCode = $status === 'success' ? $successCode : 500;

        return response()->json($response, $statusCode);
    }
}

if (! function_exists('renderRatingFilter')) {
    function renderRatingFilter()
    {
        $html = '';
        for ($i = 1; $i <= 5; $i++) {
            $html .= '<div class="mb-3 ps-0 form-check filter-star">';
            $html .= '<input type="checkbox" class="form-check-input filtering" name="rate[]" value="' . $i . '" id="rate_' . $i . '">';
            $html .= '<label class="form-check-label" for="rate_' . $i . '">';
            for ($j = 0; $j < 5; $j++) {
                $html .= '<i class="flaticon-star me-1 ' . ($i > $j ? 'active' : '') . '"></i>';
            }
            $html .= '</label>';
            $html .= '</div>';
        }

        return $html;
    }
}
if (! function_exists('generateSKU')) {

    function generateSKU(string $productName, int $length = 3, array $options = []): string
    {
        $cleanProductName = preg_replace('/[^a-z0-9]+/i', '', strtolower($productName));
        $skuProductName = strtoupper(substr($cleanProductName, 0, $length));
        $cleanOptions = array_map(function ($option) {
            return strtoupper(preg_replace('/[^a-z0-9]+/i', '', strtolower($option)));
        }, $options);

        $sku = $skuProductName . '-' . implode('-', $cleanOptions);

        return $sku . '-' . time();
    }
}
if (! function_exists('removeEmptyValues')) {
    function removeEmptyValues(array $array): array
    {
        if (empty($array)) {
            return [];
        }

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = removeEmptyValues($value);
                if (empty($array[$key])) {
                    unset($array[$key]);
                }
            } elseif (is_null($value) || $value === '' || (is_array($value) && empty($value))) {
                unset($array[$key]);
            }
        }

        return $array;
    }
}
if (! function_exists('convertToFriDdMonYyyyHhMmSs')) {

    function convertToFriDdMonYyyyHhMmSs($dateStr)
    {
        $inputFormat = 'Y-m-d H:i:s';
        $outputFormat = 'D, d M Y H:i:s \G\M\T';

        $date = Carbon::createFromFormat($inputFormat, $dateStr);

        return $date->format($outputFormat);
    }
}
if (! function_exists('convertToYyyyMmDdHhMmSs')) {

    function convertToYyyyMmDdHhMmSs($dateStr)
    {
        $inputFormat = 'D, d M Y H:i:s \G\M\T';
        $outputFormat = 'Y-m-d H:i:s';

        $date = Carbon::createFromFormat($inputFormat, $dateStr);

        return $date->format($outputFormat);
    }
}

if (! function_exists('getError')) {
    function getError($e)
    {
        echo 'Error: ' . $e->getMessage() . '<br>';
        echo 'Line: ' . $e->getLine() . '<br>';
        echo 'File: ' . $e->getFile() . '<br>';

        exit();
    }
}

if (! function_exists('convertToAcronym')) {
    function convertToAcronym(string $input, string $delimiter = '-'): string
    {
        $parts = explode($delimiter, $input);

        $acronym = '';
        foreach ($parts as $part) {
            $acronym .= strtoupper(substr($part, 0, 1));
        }

        return $acronym;
    }
}
if (! function_exists('truncate')) {
    function truncate($text, $length = 60, $appendEllipsis = false)
    {
        if (mb_strlen($text) <= $length) {
            return $text;
        }

        $truncated = mb_substr($text, 0, $length);

        if ($appendEllipsis) {
            return $truncated . '...';
        }

        return $truncated;
    }
}

if (! function_exists('haversineGreatCircleDistance')) {

    function haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371)
    {
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos($latFrom) * cos($latTo) *
            sin($lonDelta / 2) * sin($lonDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}

if (! function_exists('hintPhoneNumber')) {

    function hintPhoneNumber($phoneNumber): string
    {
        if (!$phoneNumber) {
            return '';
        }
        $cleaned = preg_replace('/\D/', '', $phoneNumber);

        if (strlen($cleaned) < 4) {
            return 'Invalid phone number';
        }

        $hinted = substr($cleaned, 0, 2) . str_repeat('*', strlen($cleaned) - 4) . substr($cleaned, -2);

        return $hinted;
    }
}
if (! function_exists('hintEmail')) {

    function hintEmail(string $email): string
    {

        if (!$email) {
            return '';
        }

        $parts = explode('@', $email);
        if (count($parts) != 2) {
            return 'Invalid email address';
        }

        $localPart = $parts[0];
        $domain = $parts[1];

        $hintedLocalPart = $localPart[0] . str_repeat('*', strlen($localPart) - 2) . $localPart[-1];
        $hintedDomain = $domain[0] . str_repeat('.', strlen($domain) - 2) . $domain[-1];

        return $hintedLocalPart . '@' . $hintedDomain;
    }
}

if (! function_exists('convertPhoneNumber')) {

    function convertPhoneNumber($phoneNumber, $countryCode = '84')
    {
        if (strlen($phoneNumber) === 10 && strpos($phoneNumber, '0') === 0) {
            return $countryCode . substr($phoneNumber, 1);
        }

        return false;
    }
}

if (! function_exists('numberToWords')) {

    function numberToWords($number)
    {
        $units = [
            'không', // 0
            'một',   // 1
            'hai',    // 2
            'ba',     // 3
            'bốn',    // 4
            'năm',    // 5
            'sáu',    // 6
            'bảy',    // 7
            'tám',    // 8
            'chín',   // 9
        ];

        $numberStr = (string) $number;
        $words = [];

        for ($i = 0; $i < strlen($numberStr); $i++) {
            $digit = (int) $numberStr[$i];
            $words[] = $units[$digit];
        }

        return implode('. ', $words);
    }
}

if (! function_exists('_log')) {

    function _log($value)
    {
        if ($value instanceof \Illuminate\Support\Collection) {
            $value = $value->toArray();
        }
        if (is_array($value)) {
            $value = json_encode($value);  // Chuyển Array thành JSON string để dễ nhìn và ghi log
        }

        Log::info('>>>> BỐ MÀY ĐANG KIỂM TRA GIÁ TRỊ TẠI ĐÂY ->>>>>> ' . $value);
    }
}

if (! function_exists('formatTimeOrder')) {

    function formatTimeOrder($date)
    {
        $datetime = new DateTime($date);

        // Tạo mảng để chuyển đổi tháng từ số sang dạng "Tháng" bằng tiếng Việt
        $months = [
            '01' => 'Th01',
            '02' => 'Th02',
            '03' => 'Th03',
            '04' => 'Th04',
            '05' => 'Th05',
            '06' => 'Th06',
            '07' => 'Th07',
            '08' => 'Th08',
            '09' => 'Th09',
            '10' => 'Th10',
            '11' => 'Th11',
            '12' => 'Th12',
        ];

        // Lấy tháng từ ngày và chuyển đổi theo mảng
        $month = $datetime->format('m');
        $formatted_date = $months[$month] . ' ' . $datetime->format('d, Y h:i A');

        return $formatted_date;
    }
}
