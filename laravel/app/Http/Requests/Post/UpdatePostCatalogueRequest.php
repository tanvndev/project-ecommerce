<?php

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostCatalogueRequest extends FormRequest
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
            'name' => 'required',
            'canonical' => 'unique:Post_catalogues,canonical,' . $this->catalogue,
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Tên nhóm post',
            'canonical' => 'Đường dẫn',
        ];
    }

    public function messages()
    {
        return __('request.messages');
    }
}
