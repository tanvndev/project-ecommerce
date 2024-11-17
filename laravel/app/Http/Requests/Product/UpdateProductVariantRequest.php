<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductVariantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function rules(): array
    {
        $rules = [
            'variable_id'         => 'required|integer|min:1',
            'variable_name'       => 'required|string|max:255',
            'variable_sku'        => 'required|string|max:255',
            'variable_weight'     => 'required|numeric|min:0',
            'variable_length'     => 'required|numeric|min:0',
            'variable_width'      => 'required|numeric|min:0',
            'variable_height'     => 'required|numeric|min:0',
            'variable_stock'      => 'required|integer|min:0',
            'variable_cost_price' => 'required|numeric|min:0',
            'variable_price'      => 'required|numeric|min:0',
            'variable_sale_price' => 'nullable|numeric|lt:variable_price',
            'variable_image'      => 'required',
            'variable_album'      => 'required',
            'variable_is_used'    => 'required',
        ];

        // if ($this->variable_is_used != false) {
        //     $rules['variable_is_used'] = 'required|in:false';
        // }

        return $rules;
    }

    public function attributes()
    {
        return [
            'variable_id'         => 'ID',
            'variable_name'       => 'Tên sản phẩm',
            'variable_sku'        => 'SKU',
            'variable_weight'     => 'Cân nặng',
            'variable_length'     => 'Chiều dài',
            'variable_width'      => 'Chiều rộng',
            'variable_height'     => 'Chiều cao',
            'variable_stock'      => 'Số lượng',
            'variable_cost_price' => 'Giá nhập',
            'variable_price'      => 'Giá bán',
            'variable_sale_price' => 'Giá ưu đãi',
            'variable_image'      => 'Ảnh sản phẩm',
            'variable_album'      => 'Thư viện ảnh',
            'variable_is_used'    => 'Sử dụng',
        ];
    }

    public function messages()
    {
        return __('request.messages') + [
            'variable_is_used.in' => 'Sản phẩm đã bị khóa vui lòng chọn khác.',
        ];
    }
}
