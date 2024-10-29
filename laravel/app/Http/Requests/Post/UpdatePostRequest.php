<?php

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
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
            'canonical' => 'unique:posts, canonical,' . $this->post,
            'description' => 'required',
            'content' => 'required',
            'icon' => 'required',
            'order' => 'required|integer',
            'meta_title' => 'required',
            'meta_keyword' => 'required',
            'meta_description' => 'required',
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Tên bài viết',
            'canonical' => 'Đường dẫn',
            'image' => 'Hình ảnh',
            'description' => 'Mô tả',
            'content' => 'Nội dung',
            'icon' => 'Biểu tượng',
            'order' => 'Sắp xếp',
            'meta_title' => 'Tiêu đề',
            'meta_keyword' => 'Từ khóa',
            'meta_description' => 'Mô tả',
        ];
    }

    public function messages()
    {
        return __('request.messages');
    }
}