<?php

namespace App\Http\Requests\FlashSale;

use Illuminate\Foundation\Http\FormRequest;

class FlashSaleUpdateRequest extends FormRequest
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
            'name'             => 'required|string|max:255|unique:flash_sales,name,' . $this->flash_sale,
            'start_date'       => 'required|date',
            'end_date'         => 'required|date|after:start_at',
            'max_quantities'   => 'required|array',
            'max_quantities.*' => 'required|integer|min:1',
            'sale_prices'      => 'required|array',
            'sale_prices.*'    => 'required|numeric|min:0',
        ];
    }

    public function attributes(): array
    {
        return [
            'max_quantities.*' => 'Số lượng tối đa',
            'sale_prices.*'    => 'Giá khuyến mãi',
            'name'             => 'Tên khuyến mãi',
            'start_date'       => 'Bắt đầu khuyến mãi',
            'end_date'         => 'Kết thúc khuyến mãi',
        ];
    }

    public function messages()
    {
        return __('request.messages');
    }
}
