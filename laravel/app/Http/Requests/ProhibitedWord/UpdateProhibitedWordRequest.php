<?php

namespace App\Http\Requests\ProhibitedWord;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProhibitedWordRequest extends FormRequest
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
            'keyword' => 'required|string|unique:prohibited_words,keyword,' . $this->id,
        ];
    }

    public function attributes()
    {
        return [
            'keyword' => 'Từ khóa tìm kiếm',
        ];
    }

    public function messages()
    {
        return __('request.messages');
    }
}
