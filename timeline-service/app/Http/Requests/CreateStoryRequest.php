<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateStoryRequest extends FormRequest
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
     * Quy tắc kiểm tra dữ liệu đăng story 24h.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'content'  => ['nullable', 'string', 'max:500'],
            'media'    => ['required', 'file', 'mimes:jpeg,png,jpg,gif,webp,mp4,mov,webm', 'max:51200'],
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
            'media.required' => 'Vui lòng chọn hình ảnh hoặc video cho tin của bạn.',
            'media.mimes'    => 'Định dạng tệp không được hỗ trợ (chỉ nhận ảnh hoặc video).',
            'media.max'      => 'Dung lượng tệp tin không được vượt quá 50MB.',
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
