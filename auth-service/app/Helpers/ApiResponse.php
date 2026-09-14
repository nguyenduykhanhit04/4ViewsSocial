<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function success($data = null, string $message = 'Success', int $code = 200): JsonResponse
    {
        return response()->json([
            'code'    => $code,
            'message' => $message,
            'data'    => $data,
        ], $code);
    }

    public static function created($data = null, string $message = 'Resource created successfully'): JsonResponse
    {
        return self::success($data, $message, 201);
    }

    public static function badRequest(string $message = 'Bad request', $data = null): JsonResponse
    {
        return response()->json([
            'code'    => 400,
            'message' => $message,
            'data'    => $data,
        ], 400);
    }

    public static function unauthorized(string $message = 'Unauthorized'): JsonResponse
    {
        return response()->json([
            'code'    => 401,
            'message' => $message,
            'data'    => null,
        ], 401);
    }

    public static function forbidden(string $message = 'Forbidden'): JsonResponse
    {
        return response()->json([
            'code'    => 403,
            'message' => $message,
            'data'    => null,
        ], 403);
    }

    public static function notFound(string $message = 'Resource not found'): JsonResponse
    {
        return response()->json([
            'code'    => 404,
            'message' => $message,
            'data'    => null,
        ], 404);
    }

    public static function unprocessable(string $message = 'Unprocessable entity', $errors = null): JsonResponse
    {
        return response()->json([
            'code'    => 422,
            'message' => $message,
            'errors'  => $errors,
        ], 422);
    }

    public static function error(string $message = 'Internal server error', int $code = 500): JsonResponse
    {
        return response()->json([
            'code'    => $code,
            'message' => $message,
            'data'    => null,
        ], $code);
    }

    /* --- Backward compatibility instance methods --- */
    public function BadRequest(string $message = 'Bad request'): JsonResponse
    {
        return self::badRequest($message);
    }

    public function InternalServerError(string $message = 'Internal server error'): JsonResponse
    {
        return self::error($message, 500);
    }
}
