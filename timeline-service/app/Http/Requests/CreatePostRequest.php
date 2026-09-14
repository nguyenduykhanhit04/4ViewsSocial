<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreatePostRequest extends FormRequest
{
    /**
     * Xác định quyền truy cập request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Quy tắc kiểm tra tính hợp lệ khi đăng bài viết mới.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'content'  => ['nullable', 'string', 'max:5000'],
            'image'    => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:20480'],
            'video'    => ['nullable', 'file', 'mimes:mp4,mov,avi,wmv,webm', 'max:102400'],
            'location' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Thông báo lỗi xác thực tiếng Việt.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'content.max' => 'Nội dung bài viết không vượt quá 5000 ký tự.',
            'image.image' => 'Tệp tải lên phải là định dạng hình ảnh hợp lệ.',
            'image.max'   => 'Dung lượng hình ảnh không được vượt quá 20MB.',
            'video.mimes' => 'Định dạng video không được hỗ trợ.',
            'video.max'   => 'Dung lượng video không được vượt quá 100MB.',
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
