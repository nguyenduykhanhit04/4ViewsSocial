<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_name' => ['required', 'string', 'min:3', 'max:50', 'unique:users,user_name'],
            'full_name' => ['required', 'string', 'max:100'],
            'email'     => ['required', 'email', 'max:150', 'unique:users,email'],
            'password'  => ['required', 'string', 'min:6'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_name.required' => 'Tên đăng nhập không được để trống.',
            'user_name.unique'   => 'Tên đăng nhập đã tồn tại trên hệ thống.',
            'full_name.required' => 'Họ và tên không được để trống.',
            'email.required'     => 'Email không được để trống.',
            'email.email'        => 'Định dạng email không hợp lệ.',
            'email.unique'       => 'Email đã được đăng ký tài khoản.',
            'password.required'  => 'Mật khẩu không được để trống.',
            'password.min'       => 'Mật khẩu phải chứa ít nhất 6 ký tự.',
        ];
    }

    protected function failedValidation(Validator $validator)
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
