<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ChangePasswordRequest extends FormRequest
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
     * Quy tắc đổi mật khẩu tài khoản.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string'],
            'new_password'     => ['required', 'string', 'min:6', 'different:current_password'],
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
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'new_password.required'     => 'Vui lòng nhập mật khẩu mới.',
            'new_password.min'          => 'Mật khẩu mới phải có tối thiểu 6 ký tự.',
            'new_password.different'    => 'Mật khẩu mới không được trùng với mật khẩu cũ.',
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
