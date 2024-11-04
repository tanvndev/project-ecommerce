<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
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
        $rules = [];
        if ($this->has('order_status')) {
            $rules['order_status'] = 'required';
        }

        if ($this->has('payment_status')) {
            $rules['payment_status'] = 'required';
        }

        return $rules;
    }

    public function attributes()
    {
        return [
            'order_status'      => 'Trạng thái đơn hàng',
            'payment_status'    => 'Trạng thái thanh toán',
        ];
    }

    public function messages()
    {
        return __('request.messages');
    }
}
