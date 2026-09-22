<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    /**
     * Standard success response structure.
     */
    public static function success(
        mixed $data = null,
        string $message = 'Success.',
        int $statusCode = 200,
        array $extraMeta = []
    ): JsonResponse {
        $response = [
            'success' => true,
            'status_code' => $statusCode,
            'message' => $message,
            'data' => $data,
            'meta' => array_merge([
                'timestamp' => now()->toISOString(),
                'api_version' => 'v1',
            ], $extraMeta),
        ];

        return response()->json($response, $statusCode);
    }

    /**
     * Standard error response structure.
     */
    public static function error(
        string $message,
        string $errorCode,
        int $statusCode = 422,
        mixed $errors = null,
        array $extraMeta = []
    ): JsonResponse {
        $response = [
            'success' => false,
            'status_code' => $statusCode,
            'error_code' => $errorCode,
            'message' => $message,
            'errors' => $errors,
            'meta' => array_merge([
                'timestamp' => now()->toISOString(),
                'api_version' => 'v1',
            ], $extraMeta),
        ];

        return response()->json($response, $statusCode);
    }
}
