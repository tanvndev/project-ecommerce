<?php

namespace App\Http\Requests\SearchHistory;

use Illuminate\Foundation\Http\FormRequest;

class StoreSearchHistoryRequest extends FormRequest
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
            'keyword' => 'nullable|string',
            'count'   => 'nullable|integer'
        ];

        return $rules;
    }

    public function attributes()
    {
        return [
            'keyword'    => 'Từ tìm kiếm',
            'count'    => 'Số lần tìm kiếm',
        ];
    }

    public function messages()
    {
        return __('request.messages');
    }
}
