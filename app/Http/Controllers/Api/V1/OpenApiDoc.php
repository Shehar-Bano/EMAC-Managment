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
#[OA\Tag(
    name: 'Service Requests',
    description: 'Customer service request submission and tracking APIs'
)]
#[OA\Tag(
    name: 'Quotes',
    description: 'Price quotation review and customer response (approve, decline, ask question) APIs'
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
                    new OA\Property(property: 'address', type: 'string', example: '123 Example Street', description: 'Primary address line (stored in user_addresses table)'),
                    new OA\Property(property: 'country', type: 'string', example: 'Cayman Islands', nullable: true),
                    new OA\Property(property: 'state', type: 'string', example: 'Grand Cayman', nullable: true),
                    new OA\Property(property: 'city', type: 'string', example: 'George Town', nullable: true),
                    new OA\Property(
                        property: 'addresses',
                        type: 'array',
                        items: new OA\Items(
                            properties: [
                                new OA\Property(property: 'country', type: 'string', example: 'Cayman Islands'),
                                new OA\Property(property: 'state', type: 'string', example: 'Grand Cayman'),
                                new OA\Property(property: 'city', type: 'string', example: 'George Town'),
                                new OA\Property(property: 'address', type: 'string', example: '123 Example Street, Apt 4B'),
                                new OA\Property(property: 'is_primary', type: 'boolean', example: true),
                            ]
                        ),
                        nullable: true,
                        description: 'Optional list of user addresses'
                    ),
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
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'john@example.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'StrongPassword123!'),
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
                                new OA\Property(
                                    property: 'addresses',
                                    type: 'array',
                                    items: new OA\Items(
                                        properties: [
                                            new OA\Property(property: 'id', type: 'integer', example: 1),
                                            new OA\Property(property: 'country', type: 'string', example: 'Cayman Islands'),
                                            new OA\Property(property: 'state', type: 'string', example: 'Grand Cayman'),
                                            new OA\Property(property: 'city', type: 'string', example: 'George Town'),
                                            new OA\Property(property: 'address', type: 'string', example: '123 Example Street, Apt 4B'),
                                            new OA\Property(property: 'is_primary', type: 'boolean', example: true),
                                        ]
                                    )
                                ),
                                new OA\Property(property: 'role', type: 'string', example: 'customer'),
                                new OA\Property(property: 'source', type: 'string', example: 'email'),
                                new OA\Property(property: 'account_status', type: 'string', example: 'verified'),
                                new OA\Property(property: 'profile_status', type: 'string', example: 'complete'),
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
        summary: 'Update or complete profile with multiple addresses',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'John Doe'),
                    new OA\Property(property: 'phone', type: 'string', example: '+1234567890'),
                    new OA\Property(property: 'address', type: 'string', example: 'Updated 456 Avenue'),
                    new OA\Property(
                        property: 'addresses',
                        type: 'array',
                        items: new OA\Items(
                            properties: [
                                new OA\Property(property: 'country', type: 'string', example: 'Cayman Islands'),
                                new OA\Property(property: 'state', type: 'string', example: 'Grand Cayman'),
                                new OA\Property(property: 'city', type: 'string', example: 'George Town'),
                                new OA\Property(property: 'address', type: 'string', example: '456 Seven Mile Beach Rd'),
                                new OA\Property(property: 'is_primary', type: 'boolean', example: true),
                            ]
                        )
                    ),
                ]
            )
        ),
        tags: ['User Profile'],
        responses: [
            new OA\Response(response: 200, description: 'Profile updated successfully'),
        ]
    )]
    public function updateProfileDoc() {}

    #[OA\Post(
        path: '/api/v1/service-requests',
        summary: 'Submit a new customer service request',
        description: 'Allows authenticated customers to submit a service request with description, property details, location address ID, schedule preferences, priority, optional notes, photographs (jpg/png/webp max 10MB), and videos (mp4/mov/avi max 50MB).',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    required: ['description', 'property_information', 'user_address_id', 'preferred_service_date', 'preferred_service_time'],
                    properties: [
                        new OA\Property(property: 'description', type: 'string', example: 'Central AC is making a loud rattling noise and not blowing cold air.', description: 'Detailed written description of the issue'),
                        new OA\Property(property: 'property_information', type: 'string', example: '2-storey residential villa, Unit 4B, rooftop AC compressor access via exterior ladder.', description: 'Property details and access instructions'),
                        new OA\Property(property: 'user_address_id', type: 'integer', example: 1, description: 'ID of existing user address record from user_addresses table'),
                        new OA\Property(property: 'preferred_service_date', type: 'string', format: 'date', example: '2026-10-05', description: 'Requested service date (YYYY-MM-DD)'),
                        new OA\Property(property: 'preferred_service_time', type: 'string', example: '10:00 AM - 12:00 PM', description: 'Requested time slot or hour'),
                        new OA\Property(property: 'priority', type: 'string', enum: ['low', 'medium', 'high', 'emergency'], example: 'high', description: 'Priority of service (defaults to medium)'),
                        new OA\Property(property: 'additional_notes', type: 'string', example: 'Gate code is 4321. Please call 15 minutes before arrival.', nullable: true),
                        new OA\Property(
                            property: 'photographs[]',
                            type: 'array',
                            items: new OA\Items(type: 'string', format: 'binary'),
                            description: 'Up to 10 image files (JPEG, PNG, WEBP, max 10MB each)'
                        ),
                        new OA\Property(
                            property: 'videos[]',
                            type: 'array',
                            items: new OA\Items(type: 'string', format: 'binary'),
                            description: 'Up to 3 video files (MP4, MOV, AVI, WEBM, max 50MB each)'
                        ),
                    ]
                )
            )
        ),
        tags: ['Service Requests'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Service request created successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'status_code', type: 'integer', example: 201),
                        new OA\Property(property: 'message', type: 'string', example: 'Service request submitted successfully.'),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'description', type: 'string', example: 'Central AC is making a loud rattling noise.'),
                                new OA\Property(property: 'property_information', type: 'string', example: '2-storey residential villa, Unit 4B.'),
                                new OA\Property(property: 'preferred_service_date', type: 'string', example: '2026-10-05'),
                                new OA\Property(property: 'preferred_service_time', type: 'string', example: '10:00 AM - 12:00 PM'),
                                new OA\Property(property: 'priority', type: 'string', example: 'high'),
                                new OA\Property(property: 'status', type: 'string', example: 'pending'),
                                new OA\Property(property: 'additional_notes', type: 'string', example: 'Gate code is 4321.'),
                                new OA\Property(
                                    property: 'location',
                                    properties: [
                                        new OA\Property(property: 'id', type: 'integer', example: 1),
                                        new OA\Property(property: 'address', type: 'string', example: '123 Example Street'),
                                        new OA\Property(property: 'city', type: 'string', example: 'George Town'),
                                        new OA\Property(property: 'state', type: 'string', example: 'Grand Cayman'),
                                        new OA\Property(property: 'country', type: 'string', example: 'Cayman Islands'),
                                    ],
                                    type: 'object'
                                ),
                                new OA\Property(
                                    property: 'photographs',
                                    type: 'array',
                                    items: new OA\Items(
                                        properties: [
                                            new OA\Property(property: 'id', type: 'integer', example: 1),
                                            new OA\Property(property: 'file_url', type: 'string', example: 'http://localhost:8000/storage/service_requests/photos/abc123.jpg'),
                                            new OA\Property(property: 'file_name', type: 'string', example: 'ac_unit.jpg'),
                                        ]
                                    )
                                ),
                                new OA\Property(
                                    property: 'videos',
                                    type: 'array',
                                    items: new OA\Items(
                                        properties: [
                                            new OA\Property(property: 'id', type: 'integer', example: 1),
                                            new OA\Property(property: 'file_url', type: 'string', example: 'http://localhost:8000/storage/service_requests/videos/xyz789.mp4'),
                                            new OA\Property(property: 'file_name', type: 'string', example: 'ac_noise.mp4'),
                                        ]
                                    )
                                ),
                                new OA\Property(property: 'created_at', type: 'string', example: '2026-09-24T16:30:00+05:00'),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Validation error'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function createServiceRequestDoc() {}

    #[OA\Get(
        path: '/api/v1/service-requests',
        summary: 'List customer service requests',
        description: 'Get a paginated list of service requests submitted by the authenticated customer (or all requests if admin/staff). Supports filtering by status, priority, and pagination.',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'status', in: 'query', required: false, schema: new OA\Schema(type: 'string', enum: ['pending', 'in_review', 'approved', 'in_progress', 'completed', 'cancelled'])),
            new OA\Parameter(name: 'priority', in: 'query', required: false, schema: new OA\Schema(type: 'string', enum: ['low', 'medium', 'high', 'emergency'])),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', example: 15)),
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        tags: ['Service Requests'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of service requests retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'status_code', type: 'integer', example: 200),
                        new OA\Property(property: 'message', type: 'string', example: 'Service requests retrieved successfully.'),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer', example: 1),
                                    new OA\Property(property: 'description', type: 'string', example: 'Central AC issue'),
                                    new OA\Property(property: 'priority', type: 'string', example: 'high'),
                                    new OA\Property(property: 'status', type: 'string', example: 'pending'),
                                    new OA\Property(property: 'preferred_service_date', type: 'string', example: '2026-10-05'),
                                    new OA\Property(property: 'created_at', type: 'string', example: '2026-09-24T16:30:00+05:00'),
                                ]
                            )
                        ),
                        new OA\Property(
                            property: 'meta',
                            properties: [
                                new OA\Property(property: 'current_page', type: 'integer', example: 1),
                                new OA\Property(property: 'per_page', type: 'integer', example: 15),
                                new OA\Property(property: 'total', type: 'integer', example: 5),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function listServiceRequestsDoc() {}

    #[OA\Get(
        path: '/api/v1/service-requests/{id}',
        summary: 'Get details of a specific service request',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        tags: ['Service Requests'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Service request details retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'status_code', type: 'integer', example: 200),
                        new OA\Property(property: 'message', type: 'string', example: 'Service request details retrieved successfully.'),
                        new OA\Property(property: 'data', type: 'object'),
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Service request not found'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function getServiceRequestDoc() {}

    #[OA\Get(
        path: '/api/v1/quotes/{id}',
        summary: 'Get details of a price quote',
        description: 'Allows customer to view itemized pricing, labor, materials, trip charges, discounts, taxes, and terms for an issued quote.',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        tags: ['Quotes'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Quote details retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'status_code', type: 'integer', example: 200),
                        new OA\Property(property: 'message', type: 'string', example: 'Quote retrieved successfully.'),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'quote_number', type: 'string', example: 'QUO-2026-00001'),
                                new OA\Property(property: 'service_request_id', type: 'integer', example: 1),
                                new OA\Property(property: 'service_description', type: 'string', example: 'Compressor replacement & AC system recharge.'),
                                new OA\Property(
                                    property: 'costs',
                                    properties: [
                                        new OA\Property(property: 'labor', type: 'number', example: 150.00),
                                        new OA\Property(property: 'materials', type: 'number', example: 320.00),
                                        new OA\Property(property: 'equipment', type: 'number', example: 50.00),
                                        new OA\Property(property: 'trip_charge', type: 'number', example: 25.00),
                                        new OA\Property(property: 'additional_charges', type: 'number', example: 0.00),
                                        new OA\Property(property: 'subtotal', type: 'number', example: 545.00),
                                        new OA\Property(property: 'discount', type: 'number', example: 25.00),
                                        new OA\Property(property: 'tax_rate', type: 'number', example: 5.0),
                                        new OA\Property(property: 'tax_amount', type: 'number', example: 26.00),
                                        new OA\Property(property: 'total_price', type: 'number', example: 546.00),
                                    ],
                                    type: 'object'
                                ),
                                new OA\Property(property: 'status', type: 'string', example: 'pending'),
                                new OA\Property(property: 'status_label', type: 'string', example: 'Pending Review'),
                                new OA\Property(property: 'expires_at', type: 'string', example: '2026-10-15'),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Quote not found'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function getQuoteDoc() {}

    #[OA\Post(
        path: '/api/v1/quotes/{id}/respond',
        summary: 'Customer responds to a quote',
        description: 'Customer can approve, decline, or ask a question regarding an issued price quote.',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['action'],
                properties: [
                    new OA\Property(property: 'action', type: 'string', enum: ['approve', 'decline', 'ask_question'], example: 'approve'),
                    new OA\Property(property: 'customer_notes', type: 'string', example: 'Can we schedule this for Friday morning?', nullable: true),
                ]
            )
        ),
        tags: ['Quotes'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Response recorded successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'status_code', type: 'integer', example: 200),
                        new OA\Property(property: 'message', type: 'string', example: 'Quote approved successfully.'),
                        new OA\Property(property: 'data', type: 'object'),
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Invalid action or validation error'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function respondQuoteDoc() {}
}
