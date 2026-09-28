<?php

namespace App\Http\Controllers\Api\V1\Legal;

use App\Http\Controllers\Controller;
use App\Models\LegalDocument;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class LegalDocumentController extends Controller
{
    /**
     * Get the active Terms & Conditions.
     */
    #[OA\Get(
        path: '/api/v1/terms',
        summary: 'Get Terms and Conditions',
        tags: ['Legal & Compliance'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Active Terms and Conditions retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'status_code', type: 'integer', example: 200),
                        new OA\Property(property: 'message', type: 'string', example: 'Terms and Conditions retrieved successfully.'),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'type', type: 'string', example: 'terms'),
                                new OA\Property(property: 'title', type: 'string', example: 'Terms and Conditions'),
                                new OA\Property(property: 'version', type: 'string', example: '1.0'),
                                new OA\Property(property: 'effective_date', type: 'string', example: '2026-09-01'),
                                new OA\Property(property: 'content', type: 'string', example: '<h3>1. Agreement to Terms</h3>...'),
                                new OA\Property(property: 'updated_at', type: 'string', example: '2026-09-22T06:00:00.000000Z'),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Terms and conditions document not found'),
        ]
    )]
    public function getTerms(): JsonResponse
    {
        $document = LegalDocument::terms()->active()->latest()->first();

        if (! $document) {
            return ApiResponse::error(
                message: 'Terms and Conditions document is currently unavailable.',
                errorCode: 'ERR_DOCUMENT_NOT_FOUND',
                statusCode: 404
            );
        }

        return ApiResponse::success(
            data: [
                'id' => $document->id,
                'type' => $document->type,
                'slug' => $document->slug,
                'title' => $document->title,
                'version' => $document->version,
                'effective_date' => $document->effective_date?->toDateString(),
                'content' => $document->content,
                'updated_at' => $document->updated_at?->toISOString(),
            ],
            message: 'Terms and Conditions retrieved successfully.',
            statusCode: 200
        );
    }

    /**
     * Get the active Privacy Policy.
     */
    #[OA\Get(
        path: '/api/v1/privacy',
        summary: 'Get Privacy Policy',
        tags: ['Legal & Compliance'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Active Privacy Policy retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'status_code', type: 'integer', example: 200),
                        new OA\Property(property: 'message', type: 'string', example: 'Privacy Policy retrieved successfully.'),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 2),
                                new OA\Property(property: 'type', type: 'string', example: 'privacy'),
                                new OA\Property(property: 'title', type: 'string', example: 'Privacy Policy'),
                                new OA\Property(property: 'version', type: 'string', example: '1.0'),
                                new OA\Property(property: 'effective_date', type: 'string', example: '2026-09-01'),
                                new OA\Property(property: 'content', type: 'string', example: '<h3>1. Information We Collect</h3>...'),
                                new OA\Property(property: 'updated_at', type: 'string', example: '2026-09-22T06:00:00.000000Z'),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Privacy policy document not found'),
        ]
    )]
    public function getPrivacy(): JsonResponse
    {
        $document = LegalDocument::privacy()->active()->latest()->first();

        if (! $document) {
            return ApiResponse::error(
                message: 'Privacy Policy document is currently unavailable.',
                errorCode: 'ERR_DOCUMENT_NOT_FOUND',
                statusCode: 404
            );
        }

        return ApiResponse::success(
            data: [
                'id' => $document->id,
                'type' => $document->type,
                'slug' => $document->slug,
                'title' => $document->title,
                'version' => $document->version,
                'effective_date' => $document->effective_date?->toDateString(),
                'content' => $document->content,
                'updated_at' => $document->updated_at?->toISOString(),
            ],
            message: 'Privacy Policy retrieved successfully.',
            statusCode: 200
        );
    }
}
