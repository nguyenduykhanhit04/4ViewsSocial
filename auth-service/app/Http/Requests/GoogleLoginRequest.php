<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class GoogleLoginRequest extends FormRequest
{
    /**
     * Xác định người dùng có quyền thực hiện request này hay không.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Định nghĩa các quy tắc kiểm tra tính hợp lệ của access_token từ Google.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'access_token' => ['required', 'string'],
        ];
    }

    /**
     * Tùy biến thông báo lỗi xác thực tiếng Việt.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'access_token.required' => 'Google access token không được để trống.',
        ];
    }

    /**
     * Xử lý khi dữ liệu gửi lên không vượt qua được kiểm tra xác thực.
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
