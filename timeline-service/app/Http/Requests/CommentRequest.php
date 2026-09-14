<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CommentRequest extends FormRequest
{
    /**
     * Xác định quyền thực hiện request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Quy tắc kiểm tra dữ liệu bình luận bài viết.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'post_id' => ['required', 'integer', 'exists:posts,id'],
            'content' => ['required', 'string', 'max:1000'],
        ];
    }

    /**
     * Thông báo lỗi tiếng Việt.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'post_id.required' => 'Mã bài viết không được để trống.',
            'post_id.exists'   => 'Bài viết không tồn tại trên hệ thống.',
            'content.required' => 'Nội dung bình luận không được để trống.',
            'content.max'      => 'Bình luận không vượt quá 1000 ký tự.',
        ];
    }

    /**
     * Xử lý lỗi validation thất bại.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'code'    => 422,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422)
        );
    }
}
