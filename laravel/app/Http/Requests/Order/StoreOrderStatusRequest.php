<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderStatusRequest extends FormRequest
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
            'requested_status' => 'required|string|in:pending,processing,delivering,completed,cancelled,returned',
            'reason'           => 'required|string|max:100',
        ];
    }

    public function attributes()
    {
        return [
            'requested_status'      => 'Trạng thái ',
            'reason'                => 'Lý do',
        ];
    }

    public function messages()
    {
        return __('request.messages');
    }
}
