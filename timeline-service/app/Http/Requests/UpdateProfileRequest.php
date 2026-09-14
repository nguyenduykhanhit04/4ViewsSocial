<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateProfileRequest extends FormRequest
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
     * Quy tắc cập nhật hồ sơ cá nhân.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'full_name'     => ['nullable', 'string', 'max:100'],
            'bio'           => ['nullable', 'string', 'max:500'],
            'avatar'        => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:10240'],
            'facebook_url'  => ['nullable', 'url', 'max:255'],
            'thread_url'    => ['nullable', 'url', 'max:255'],
            'instagram_url' => ['nullable', 'url', 'max:255'],
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
            'avatar.image'       => 'Ảnh đại diện phải là tệp hình ảnh hợp lệ.',
            'avatar.max'         => 'Ảnh đại diện không được vượt quá 10MB.',
            'facebook_url.url'   => 'Đường dẫn Facebook không hợp lệ.',
            'thread_url.url'     => 'Đường dẫn Threads không hợp lệ.',
            'instagram_url.url'  => 'Đường dẫn Instagram không hợp lệ.',
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
