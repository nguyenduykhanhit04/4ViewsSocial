<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    /**
     * Trả về phản hồi thành công chuẩn (HTTP 200 OK).
     *
     * @param  mixed|null  $data     Dữ liệu trả về
     * @param  string      $message  Thông báo
     * @param  int         $code     Mã trạng thái HTTP
     * @return \Illuminate\Http\JsonResponse
     */
    public static function success($data = null, string $message = 'Success', int $code = 200): JsonResponse
    {
        return new JsonResponse([
            'code'    => $code,
            'message' => $message,
            'data'    => $data,
        ], $code);
    }

    /**
     * Trả về phản hồi tạo mới thành công (HTTP 201 Created).
     *
     * @param  mixed|null  $data     Dữ liệu thực thể vừa tạo
     * @param  string      $message  Thông báo
     * @return \Illuminate\Http\JsonResponse
     */
    public static function created($data = null, string $message = 'Resource created successfully'): JsonResponse
    {
        return self::success($data, $message, 201);
    }

    /**
     * Trả về phản hồi yêu cầu không hợp lệ (HTTP 400 Bad Request).
     *
     * @param  string      $message  Mô tả lỗi
     * @param  mixed|null  $data     Dữ liệu chi tiết lỗi kèm theo (nếu có)
     * @return \Illuminate\Http\JsonResponse
     */
    public static function badRequest(string $message = 'Bad request', $data = null): JsonResponse
    {
        return new JsonResponse([
            'code'    => 400,
            'message' => $message,
            'data'    => $data,
        ], 400);
    }

    /**
     * Trả về phản hồi chưa được xác thực (HTTP 401 Unauthorized).
     *
     * @param  string  $message  Mô tả lỗi
     * @return \Illuminate\Http\JsonResponse
     */
    public static function unauthorized(string $message = 'Unauthorized'): JsonResponse
    {
        return new JsonResponse([
            'code'    => 401,
            'message' => $message,
            'data'    => null,
        ], 401);
    }

    /**
     * Trả về phản hồi bị cấm truy cập (HTTP 403 Forbidden).
     *
     * @param  string  $message  Mô tả lỗi
     * @return \Illuminate\Http\JsonResponse
     */
    public static function forbidden(string $message = 'Forbidden'): JsonResponse
    {
        return new JsonResponse([
            'code'    => 403,
            'message' => $message,
            'data'    => null,
        ], 403);
    }

    /**
     * Trả về phản hồi tài nguyên không tìm thấy (HTTP 404 Not Found).
     *
     * @param  string  $message  Mô tả lỗi
     * @return \Illuminate\Http\JsonResponse
     */
    public static function notFound(string $message = 'Resource not found'): JsonResponse
    {
        return new JsonResponse([
            'code'    => 404,
            'message' => $message,
            'data'    => null,
        ], 404);
    }

    /**
     * Trả về phản hồi lỗi xác thực dữ liệu đầu vào (HTTP 422 Unprocessable Entity).
     *
     * @param  string      $message  Mô tả lỗi tổng quan
     * @param  mixed|null  $errors   Danh sách lỗi chi tiết theo từng trường
     * @return \Illuminate\Http\JsonResponse
     */
    public static function unprocessable(string $message = 'Unprocessable entity', $errors = null): JsonResponse
    {
        return new JsonResponse([
            'code'    => 422,
            'message' => $message,
            'errors'  => $errors,
        ], 422);
    }

    /**
     * Trả về phản hồi lỗi hệ thống máy chủ (HTTP 500 Internal Server Error).
     *
     * @param  string  $message  Mô tả lỗi hệ thống
     * @param  int     $code     Mã trạng thái HTTP
     * @return \Illuminate\Http\JsonResponse
     */
    public static function error(string $message = 'Internal server error', int $code = 500): JsonResponse
    {
        return new JsonResponse([
            'code'    => $code,
            'message' => $message,
            'data'    => null,
        ], $code);
    }
}
