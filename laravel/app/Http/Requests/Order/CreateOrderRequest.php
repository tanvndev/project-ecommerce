<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_name'      => 'required',
            'customer_email'     => 'required|email',
            'customer_phone'     => 'required|regex:/(0)[0-9]{9}/',
            'province_id'        => 'required',
            'district_id'        => 'required',
            'ward_id'            => 'required',
            'shipping_address'   => 'required',
            'payment_method_id'  => 'required',
            'shipping_method_id' => 'required',
            'order_items'        => 'required|array',
        ];
    }

    public function attributes()
    {
        return [
            'customer_name'         => 'Họ và tên',
            'customer_email'        => 'Địa chỉ email',
            'customer_phone'        => 'Số điện thoại',
            'province_id'           => 'Tỉnh / Thành phố',
            'district_id'           => 'Quận / Huyện',
            'ward_id'               => 'Phường / Xã',
            'shipping_address'      => 'Địa chỉ giao hàng',
            'payment_method_id'     => 'Phương thức thanh toán',
            'shipping_method_id'    => 'Hình thức vận chuyển',
            'order_items'           => 'Sản phẩm',
        ];
    }

    public function messages()
    {
        return __('request.messages') + [
            'customer_phone.regex' => 'Số điện thoại không đúng dạng.',
        ];
    }
}
