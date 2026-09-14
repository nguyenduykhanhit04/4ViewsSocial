<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
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
     * Định nghĩa các quy tắc kiểm tra tính hợp lệ của dữ liệu đăng ký.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'user_name' => ['required', 'string', 'min:3', 'max:50', 'unique:users,user_name'],
            'full_name' => ['required', 'string', 'max:100'],
            'email'     => ['required', 'email', 'max:150', 'unique:users,email'],
            'password'  => ['required', 'string', 'min:6'],
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
            'user_name.required' => 'Tên đăng nhập không được để trống.',
            'user_name.min'      => 'Tên đăng nhập phải có ít nhất 3 ký tự.',
            'user_name.max'      => 'Tên đăng nhập không vượt quá 50 ký tự.',
            'user_name.unique'   => 'Tên đăng nhập đã tồn tại trên hệ thống.',
            'full_name.required' => 'Họ và tên không được để trống.',
            'full_name.max'      => 'Họ và tên không vượt quá 100 ký tự.',
            'email.required'     => 'Email không được để trống.',
            'email.email'        => 'Định dạng email không hợp lệ.',
            'email.max'          => 'Email không vượt quá 150 ký tự.',
            'email.unique'       => 'Email đã được đăng ký tài khoản.',
            'password.required'  => 'Mật khẩu không được để trống.',
            'password.min'       => 'Mật khẩu phải chứa ít nhất 6 ký tự.',
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
