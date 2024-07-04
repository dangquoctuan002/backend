<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'code' => 'required|string|max:10|unique:products,code',
            'name' => 'required|string|max:225',
            'title' => 'required|string|max:225',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'frame' => [
                'required',
                Rule::in(['S', 'M', 'L'])
            ],
            'wheel' => [
                'required',
                Rule::in(['24', '26', '28'])
            ],
            'color' => [
                'required',
                Rule::in(['#FF0000', '#00FF00', '#0000FF', '#FFFF00', '#000000', '#ffffff'])
            ],
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ];
    }

    public function messages()
    {
        return [
            'code.required' => 'Mã sản phẩm là bắt buộc.',
            'code.unique' => 'Mã sản phẩm đã tồn tại.',
            'code.max' => 'Mã sản phẩm không được vượt quá 10 ký tự.',
            'name.required' => 'Tên sản phẩm là bắt buộc.',
            'name.max' => 'Tên sản phẩm không được vượt quá 225 ký tự.',
            'title.required' => 'Tiêu đề sản phẩm là bắt buộc.',
            'title.max' => 'Tiêu đề sản phẩm không được vượt quá 225 ký tự.',
            'price.required' => 'Giá sản phẩm là bắt buộc.',
            'price.numeric' => 'Giá sản phẩm phải là số.',
            'price.min' => 'Giá sản phẩm không được nhỏ hơn 0.',
            'quantity.required' => 'Số lượng sản phẩm là bắt buộc.',
            'quantity.integer' => 'Số lượng sản phẩm phải là số nguyên.',
            'quantity.min' => 'Số lượng sản phẩm không được nhỏ hơn 0.',
            'frame.required' => 'Khung sản phẩm là bắt buộc.',
            'frame.in' => 'Khung sản phẩm chỉ được chọn từ các giá trị: S, M, L.',
            'wheel.required' => 'Bánh xe sản phẩm là bắt buộc.',
            'wheel.in' => 'Bánh xe sản phẩm chỉ được chọn từ các giá trị: 24, 26, 28.',
            'color.required' => 'Màu sắc sản phẩm là bắt buộc.',
            'color.in' => 'Màu sắc sản phẩm chỉ được chọn từ các giá trị: #FF0000, #00FF00, #0000FF, #FFFF00, #000000, #ffffff.',
            'category_id.required' => 'Danh mục là bắt buộc.',
            'category_id.exists' => 'Danh mục không tồn tại.',
            'brand_id.required' => 'Thương hiệu là bắt buộc.',
            'brand_id.exists' => 'Thương hiệu không tồn tại.',
            'image.image' => 'File ảnh không hợp lệ.',
            'image.mimes' => 'Ảnh phải có định dạng: jpeg, png, jpg, gif, svg.',
            'image.max' => 'Ảnh không được vượt quá 2MB.'
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = response()->json([
            'status' => 0,
            'errors' => $validator->errors()
        ], 422);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
