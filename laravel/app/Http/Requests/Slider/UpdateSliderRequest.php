<?php

namespace App\Http\Requests\Slider;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSliderRequest extends FormRequest
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
        $rules = [
            'name'                => 'required',
            'code'                => 'required|unique:sliders,code,' . $this->slider,
            'items.*'             => 'required',
            'items.*.image'       => 'required|string',
            'items.*.description' => 'string',
            'items.*.url'         => 'required|string',
            // 'items.*.alt'         => 'required|string',
        ];

        return $rules;
    }

    public function attributes()
    {
        return [
            'name'    => 'Tên trình chiếu',
            'code'    => 'Mã trình chiếu',
            'items'   => 'Hình ảnh trình chiếu',
            'setting' => 'Cài đặt trình chiếu',
        ];
    }

    public function messages()
    {
        return __('request.messages');
    }
}
