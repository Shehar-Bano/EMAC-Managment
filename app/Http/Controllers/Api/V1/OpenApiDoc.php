<?php

namespace App\Http\Controllers\Api\V1;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    description: 'Complete REST API documentation for the EMAC platform including Customer Authentication, OTP verification, Password Management, Social Login, and User Profiles.',
    title: 'EMAC ERP & Customer Authentication API'
)]
#[OA\Server(
    url: '/',
    description: 'Current Server Environment'
)]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    bearerFormat: 'JWT/Sanctum',
    scheme: 'bearer',
    description: 'Enter your Bearer token in the format: Bearer <token>'
)]
#[OA\Tag(
    name: 'Authentication',
    description: 'Customer Authentication & Registration APIs'
)]
#[OA\Tag(
    name: 'OTP',
    description: 'Centralized One-Time Password lifecycle and verification'
)]
#[OA\Tag(
    name: 'Password Management',
    description: 'Forgot password, reset authorization, and authenticated password change'
)]
#[OA\Tag(
    name: 'User Profile',
    description: 'Customer profile retrieval and completion'
)]
#[OA\Tag(
    name: 'Legal & Compliance',
    description: 'Terms and Conditions & Privacy Policy public documentation endpoints'
)]
class OpenApiDoc
{
    #[OA\Post(
        path: '/api/v1/auth/register',
        summary: 'Register a new customer',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'email', 'phone', 'address', 'password', 'confirm_password', 'terms_accepted', 'privacy_policy_accepted', 'role'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'John Doe'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'john@example.com'),
                    new OA\Property(property: 'phone', type: 'string', example: '+1234567890'),
                    new OA\Property(property: 'address', type: 'string', example: '123 Example Street'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'StrongPassword123!'),
                    new OA\Property(property: 'confirm_password', type: 'string', format: 'password', example: 'StrongPassword123!'),
                    new OA\Property(property: 'terms_accepted', type: 'boolean', example: true),
                    new OA\Property(property: 'privacy_policy_accepted', type: 'boolean', example: true),
                    new OA\Property(property: 'role', type: 'string', enum: ['customer'], example: 'customer'),
                ]
            )
        ),
        tags: ['Authentication'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Registration successful. OTP sent.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'status_code', type: 'integer', example: 201),
                        new OA\Property(property: 'message', type: 'string', example: 'Registration successful. Please verify your account using the OTP sent to your registered contact.'),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(property: 'user_id', type: 'integer', example: 101),
                                new OA\Property(property: 'email', type: 'string', example: 'john@example.com'),
                                new OA\Property(property: 'phone', type: 'string', example: '+1234567890'),
                                new OA\Property(property: 'role', type: 'string', example: 'customer'),
                                new OA\Property(property: 'account_status', type: 'string', example: 'pending'),
                                new OA\Property(property: 'profile_status', type: 'string', example: 'incomplete'),
                                new OA\Property(property: 'otp_required', type: 'boolean', example: true),
                                new OA\Property(property: 'otp_purpose', type: 'string', example: 'registration'),
                            ],
                            type: 'object'
                        ),
                        new OA\Property(property: 'meta', type: 'object'),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation Error',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'status_code', type: 'integer', example: 422),
                        new OA\Property(property: 'error_code', type: 'string', example: 'ERR_VALIDATION_FAILED'),
                        new OA\Property(property: 'message', type: 'string', example: 'The email field must be a valid email address.'),
                    ]
                )
            ),
        ]
    )]
    public function registerDoc() {}

    #[OA\Post(
        path: '/api/v1/auth/login',
        summary: 'Customer login',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password', 'role'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'john@example.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'StrongPassword123!'),
                    new OA\Property(property: 'role', type: 'string', enum: ['customer'], example: 'customer'),
                ]
            )
        ),
        tags: ['Authentication'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Login successful with Bearer access token',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'status_code', type: 'integer', example: 200),
                        new OA\Property(property: 'message', type: 'string', example: 'Login successful.'),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(
                                    property: 'user',
                                    properties: [
                                        new OA\Property(property: 'id', type: 'integer', example: 101),
                                        new OA\Property(property: 'name', type: 'string', example: 'John Doe'),
                                        new OA\Property(property: 'email', type: 'string', example: 'john@example.com'),
                                        new OA\Property(property: 'phone', type: 'string', example: '+1234567890'),
                                        new OA\Property(property: 'role', type: 'string', example: 'customer'),
                                        new OA\Property(property: 'account_status', type: 'string', example: 'verified'),
                                        new OA\Property(property: 'profile_status', type: 'string', example: 'incomplete'),
                                    ],
                                    type: 'object'
                                ),
                                new OA\Property(property: 'access_token', type: 'string', example: '1|239a0fa02...'),
                                new OA\Property(property: 'token_type', type: 'string', example: 'Bearer'),
                                new OA\Property(property: 'expires_in', type: 'integer', example: 86400),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Invalid credentials'),
            new OA\Response(response: 403, description: 'Account not verified or restricted'),
        ]
    )]
    public function loginDoc() {}

    #[OA\Post(
        path: '/api/v1/auth/social',
        summary: 'Social Authentication (Google)',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['provider', 'id_token', 'role'],
                properties: [
                    new OA\Property(property: 'provider', type: 'string', enum: ['google'], example: 'google'),
                    new OA\Property(property: 'id_token', type: 'string', example: 'eyJhbGciOi...'),
                    new OA\Property(property: 'role', type: 'string', enum: ['customer'], example: 'customer'),
                ]
            )
        ),
        tags: ['Authentication'],
        responses: [
            new OA\Response(response: 200, description: 'Google authentication successful'),
            new OA\Response(response: 401, description: 'Invalid provider token'),
            new OA\Response(response: 409, description: 'Secure account linking required'),
        ]
    )]
    public function socialDoc() {}

    #[OA\Post(
        path: '/api/v1/auth/otp/send',
        summary: 'Send / Resend OTP',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['identifier', 'purpose'],
                properties: [
                    new OA\Property(property: 'identifier', type: 'string', example: 'john@example.com'),
                    new OA\Property(property: 'purpose', type: 'string', enum: ['registration', 'forgot_password', 'reset_password'], example: 'registration'),
                ]
            )
        ),
        tags: ['OTP'],
        responses: [
            new OA\Response(response: 200, description: 'OTP sent successfully'),
            new OA\Response(response: 429, description: 'Resend cooldown limit (60s) reached'),
        ]
    )]
    public function sendOtpDoc() {}

    #[OA\Post(
        path: '/api/v1/auth/otp/verify',
        summary: 'Verify OTP',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['identifier', 'otp', 'purpose'],
                properties: [
                    new OA\Property(property: 'identifier', type: 'string', example: 'john@example.com'),
                    new OA\Property(property: 'otp', type: 'string', example: '123456'),
                    new OA\Property(property: 'purpose', type: 'string', enum: ['registration', 'forgot_password', 'reset_password'], example: 'registration'),
                ]
            )
        ),
        tags: ['OTP'],
        responses: [
            new OA\Response(response: 200, description: 'OTP verified successfully'),
            new OA\Response(response: 422, description: 'Invalid or expired OTP'),
            new OA\Response(response: 429, description: 'Too many OTP attempts (max 5)'),
        ]
    )]
    public function verifyOtpDoc() {}

    #[OA\Post(
        path: '/api/v1/auth/password/forgot',
        summary: 'Request forgot password OTP',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'john@example.com'),
                ]
            )
        ),
        tags: ['Password Management'],
        responses: [
            new OA\Response(response: 200, description: 'If an account exists, OTP has been sent'),
        ]
    )]
    public function forgotPasswordDoc() {}

    #[OA\Post(
        path: '/api/v1/auth/password/reset',
        summary: 'Reset forgotten password',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['reset_token', 'new_password', 'confirm_password'],
                properties: [
                    new OA\Property(property: 'reset_token', type: 'string', example: 'a9f4c3...'),
                    new OA\Property(property: 'new_password', type: 'string', format: 'password', example: 'NewStrongPassword123!'),
                    new OA\Property(property: 'confirm_password', type: 'string', format: 'password', example: 'NewStrongPassword123!'),
                ]
            )
        ),
        tags: ['Password Management'],
        responses: [
            new OA\Response(response: 200, description: 'Password reset successfully'),
        ]
    )]
    public function resetPasswordDoc() {}

    #[OA\Post(
        path: '/api/v1/auth/password/change/request-otp',
        summary: 'Request OTP for authenticated password change',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['current_password'],
                properties: [
                    new OA\Property(property: 'current_password', type: 'string', format: 'password', example: 'CurrentPassword123!'),
                ]
            )
        ),
        tags: ['Password Management'],
        responses: [
            new OA\Response(response: 200, description: 'OTP sent for password change'),
        ]
    )]
    public function requestChangeOtpDoc() {}

    #[OA\Post(
        path: '/api/v1/auth/password/change',
        summary: 'Change password using short-lived change token',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['password_change_token', 'new_password', 'confirm_password'],
                properties: [
                    new OA\Property(property: 'password_change_token', type: 'string', example: 'b8e2...'),
                    new OA\Property(property: 'new_password', type: 'string', format: 'password', example: 'BrandNewPassword123!'),
                    new OA\Property(property: 'confirm_password', type: 'string', format: 'password', example: 'BrandNewPassword123!'),
                ]
            )
        ),
        tags: ['Password Management'],
        responses: [
            new OA\Response(response: 200, description: 'Password changed successfully'),
        ]
    )]
    public function changePasswordDoc() {}

    #[OA\Post(
        path: '/api/v1/auth/refresh',
        summary: 'Refresh access token',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: false,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'refresh_token', type: 'string', nullable: true),
                ]
            )
        ),
        tags: ['Authentication'],
        responses: [
            new OA\Response(response: 200, description: 'Access token refreshed successfully'),
        ]
    )]
    public function refreshDoc() {}

    #[OA\Post(
        path: '/api/v1/auth/logout',
        summary: 'Logout user',
        security: [['bearerAuth' => []]],
        tags: ['Authentication'],
        responses: [
            new OA\Response(response: 200, description: 'Logout successful'),
        ]
    )]
    public function logoutDoc() {}

    #[OA\Get(
        path: '/api/v1/auth/profile',
        summary: 'Get customer profile',
        security: [['bearerAuth' => []]],
        tags: ['User Profile'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Profile retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'status_code', type: 'integer', example: 200),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 101),
                                new OA\Property(property: 'name', type: 'string', example: 'John Doe'),
                                new OA\Property(property: 'email', type: 'string', example: 'john@example.com'),
                                new OA\Property(property: 'phone', type: 'string', example: '+1234567890'),
                                new OA\Property(property: 'address', type: 'string', example: '123 Example Street'),
                                new OA\Property(property: 'role', type: 'string', example: 'customer'),
                                new OA\Property(property: 'source', type: 'string', example: 'email'),
                                new OA\Property(property: 'account_status', type: 'string', example: 'verified'),
                                new OA\Property(property: 'profile_status', type: 'string', example: 'incomplete'),
                                new OA\Property(property: 'email_verified_at', type: 'string', example: '2026-09-21T10:15:00Z'),
                                new OA\Property(property: 'phone_verified_at', type: 'string', nullable: true),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
        ]
    )]
    public function profileDoc() {}

    #[OA\Patch(
        path: '/api/v1/profile',
        summary: 'Update or complete profile',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'John Doe'),
                    new OA\Property(property: 'phone', type: 'string', example: '+1234567890'),
                    new OA\Property(property: 'address', type: 'string', example: 'Updated 456 Avenue'),
                ]
            )
        ),
        tags: ['User Profile'],
        responses: [
            new OA\Response(response: 200, description: 'Profile updated successfully'),
        ]
    )]
    public function updateProfileDoc() {}
}
