<?php

namespace App\Support;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

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
     * Standard paginated response structure.
     */
    public static function paginated(
        mixed $data,
        string $message = 'Success.',
        int $statusCode = 200,
        array $extraMeta = []
    ): JsonResponse {
        $meta = [
            'timestamp' => now()->toISOString(),
            'api_version' => 'v1',
        ];

        if ($data instanceof AnonymousResourceCollection && $data->resource instanceof Paginator) {
            $paginator = $data->resource;
            $meta = array_merge($meta, [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator instanceof LengthAwarePaginator ? $paginator->total() : null,
                'last_page' => $paginator instanceof LengthAwarePaginator ? $paginator->lastPage() : null,
            ]);
        } elseif ($data instanceof Paginator) {
            $meta = array_merge($meta, [
                'current_page' => $data->currentPage(),
                'per_page' => $data->perPage(),
                'total' => $data instanceof LengthAwarePaginator ? $data->total() : null,
                'last_page' => $data instanceof LengthAwarePaginator ? $data->lastPage() : null,
            ]);
        }

        $response = [
            'success' => true,
            'status_code' => $statusCode,
            'message' => $message,
            'data' => $data,
            'meta' => array_merge($meta, $extraMeta),
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
