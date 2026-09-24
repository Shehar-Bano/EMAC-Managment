<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Enums\AuthSource;
use App\Enums\OtpPurpose;
use App\Enums\ProfileStatus;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        User::whereIn('email', [
            'john@example.com',
            'jane@example.com',
            'admin@example.com',
            'pending@example.com',
            'blocked@example.com',
            'existing@example.com',
            'googleuser@example.com',
            'testuser@example.com',
            'john.profile@example.com',
        ])->forceDelete();
    }

    /**
     * Test successful customer registration.
     */
    public function test_customer_can_register_successfully(): void
    {
        $payload = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+1234567890',
            'address' => '123 Example Street',
            'password' => 'StrongPassword123!',
            'confirm_password' => 'StrongPassword123!',
            'terms_accepted' => true,
            'privacy_policy_accepted' => true,
            'role' => 'customer',
        ];

        $response = $this->postJson('/api/v1/auth/register', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'status_code' => 201,
                'message' => 'Registration successful. Please verify your account using the OTP sent to your registered contact.',
                'data' => [
                    'email' => 'john@example.com',
                    'phone' => '+1234567890',
                    'role' => 'customer',
                    'account_status' => 'pending',
                    'profile_status' => 'incomplete',
                    'otp_required' => true,
                    'otp_purpose' => 'registration',
                ],
            ]);

        $userId = $response->json('data.user_id');

        $this->assertDatabaseHas('users', [
            'id' => $userId,
            'email' => 'john@example.com',
            'account_status' => 'pending',
            'profile_status' => 'incomplete',
            'role' => 'customer',
            'source' => 'email',
        ]);

        $this->assertDatabaseHas('user_addresses', [
            'user_id' => $userId,
            'address' => '123 Example Street',
            'is_primary' => 1,
        ]);

        $this->assertDatabaseHas('otps', [
            'identifier' => 'john@example.com',
            'purpose' => 'registration',
        ]);
    }

    /**
     * Test registration with multiple structured addresses in user_addresses table.
     */
    public function test_customer_can_register_with_addresses_array(): void
    {
        $payload = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '+1987654321',
            'addresses' => [
                [
                    'country' => 'Cayman Islands',
                    'state' => 'Grand Cayman',
                    'city' => 'George Town',
                    'address' => '789 West Bay Road',
                    'is_primary' => true,
                ],
                [
                    'country' => 'United States',
                    'state' => 'Florida',
                    'city' => 'Miami',
                    'address' => '100 Biscayne Blvd',
                    'is_primary' => false,
                ],
            ],
            'password' => 'StrongPassword123!',
            'confirm_password' => 'StrongPassword123!',
            'terms_accepted' => true,
            'privacy_policy_accepted' => true,
            'role' => 'customer',
        ];

        $response = $this->postJson('/api/v1/auth/register', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'status_code' => 201,
            ]);

        $userId = $response->json('data.user_id');

        $this->assertDatabaseHas('user_addresses', [
            'user_id' => $userId,
            'country' => 'Cayman Islands',
            'city' => 'George Town',
            'address' => '789 West Bay Road',
            'is_primary' => 1,
        ]);

        $this->assertDatabaseHas('user_addresses', [
            'user_id' => $userId,
            'country' => 'United States',
            'city' => 'Miami',
            'address' => '100 Biscayne Blvd',
            'is_primary' => 0,
        ]);
    }

    /**
     * Test registration rejects non-customer roles.
     */
    public function test_registration_rejects_admin_role(): void
    {
        $payload = [
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'phone' => '+1234567890',
            'address' => '123 Admin Street',
            'password' => 'StrongPassword123!',
            'confirm_password' => 'StrongPassword123!',
            'terms_accepted' => true,
            'privacy_policy_accepted' => true,
            'role' => 'admin',
        ];

        $response = $this->postJson('/api/v1/auth/register', $payload);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'status_code' => 422,
            ]);
    }

    /**
     * Test pending customer cannot login.
     */
    public function test_pending_customer_cannot_login(): void
    {
        $user = User::create([
            'name' => 'Pending User',
            'email' => 'pending@example.com',
            'phone' => '+1234567890',
            'address' => '123 Street',
            'password' => Hash::make('Password123!'),
            'role' => 'customer',
            'source' => AuthSource::EMAIL,
            'account_status' => AccountStatus::PENDING,
            'profile_status' => ProfileStatus::INCOMPLETE,
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'pending@example.com',
            'password' => 'Password123!',
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'status_code' => 403,
                'error_code' => 'ERR_ACCOUNT_NOT_VERIFIED',
                'message' => 'Your account has not been verified. Please verify your account using the OTP.',
            ]);
    }

    /**
     * Test OTP verification verifies account and allows login.
     */
    public function test_otp_verification_flow_and_subsequent_login(): void
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+1234567890',
            'address' => '123 Street',
            'password' => Hash::make('StrongPassword123!'),
            'role' => 'customer',
            'source' => AuthSource::EMAIL,
            'account_status' => AccountStatus::PENDING,
            'profile_status' => ProfileStatus::INCOMPLETE,
        ]);

        Otp::create([
            'user_id' => $user->id,
            'identifier' => 'john@example.com',
            'purpose' => OtpPurpose::REGISTRATION,
            'otp_hash' => Hash::make('123456'),
            'attempt_count' => 0,
            'max_attempts' => 5,
            'expires_at' => now()->addMinutes(5),
        ]);

        // 1. Verify with incorrect OTP
        $invalidVerify = $this->postJson('/api/v1/auth/otp/verify', [
            'identifier' => 'john@example.com',
            'otp' => '999999',
            'purpose' => 'registration',
        ]);

        $invalidVerify->assertStatus(422)
            ->assertJson([
                'success' => false,
                'error_code' => 'ERR_INVALID_OTP',
            ]);

        // 2. Verify with correct OTP
        $validVerify = $this->postJson('/api/v1/auth/otp/verify', [
            'identifier' => 'john@example.com',
            'otp' => '123456',
            'purpose' => 'registration',
        ]);

        $validVerify->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status_code' => 200,
                'message' => 'Account verified successfully. You can now log in.',
                'data' => [
                    'user_id' => $user->id,
                    'account_status' => 'verified',
                    'profile_status' => 'incomplete',
                    'otp_purpose' => 'registration',
                    'login_required' => true,
                ],
            ]);

        $this->assertEquals(AccountStatus::VERIFIED, $user->fresh()->account_status);

        // 3. Login now succeeds without requiring role
        $loginResponse = $this->postJson('/api/v1/auth/login', [
            'email' => 'john@example.com',
            'password' => 'StrongPassword123!',
        ]);

        $loginResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status_code' => 200,
                'message' => 'Login successful.',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'email' => 'john@example.com',
                        'role' => 'customer',
                        'account_status' => 'verified',
                        'profile_status' => 'incomplete',
                    ],
                    'token_type' => 'Bearer',
                    'expires_in' => 86400,
                ],
            ]);

        $this->assertNotEmpty($loginResponse->json('data.access_token'));
    }

    /**
     * Test forgot password, OTP verification, and password reset.
     */
    public function test_forgot_password_and_reset_flow(): void
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+1234567890',
            'address' => '123 Street',
            'password' => Hash::make('OldPassword123!'),
            'role' => 'customer',
            'source' => AuthSource::EMAIL,
            'account_status' => AccountStatus::VERIFIED,
            'profile_status' => ProfileStatus::INCOMPLETE,
        ]);

        // 1. Request forgot password OTP
        $forgotResponse = $this->postJson('/api/v1/auth/password/forgot', [
            'email' => 'john@example.com',
        ]);

        $forgotResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'otp_required' => true,
                    'purpose' => 'forgot_password',
                ],
            ]);

        // Create known OTP for test
        Otp::where('identifier', 'john@example.com')->delete();
        Otp::create([
            'user_id' => $user->id,
            'identifier' => 'john@example.com',
            'purpose' => OtpPurpose::FORGOT_PASSWORD,
            'otp_hash' => Hash::make('654321'),
            'attempt_count' => 0,
            'max_attempts' => 5,
            'expires_at' => now()->addMinutes(5),
        ]);

        // 2. Verify forgot password OTP
        $verifyResponse = $this->postJson('/api/v1/auth/otp/verify', [
            'identifier' => 'john@example.com',
            'otp' => '654321',
            'purpose' => 'forgot_password',
        ]);

        $verifyResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status_code' => 200,
                'data' => [
                    'purpose' => 'forgot_password',
                    'expires_in_seconds' => 600,
                ],
            ]);

        $resetToken = $verifyResponse->json('data.reset_token');
        $this->assertNotEmpty($resetToken);

        // 3. Reset Password
        $resetResponse = $this->postJson('/api/v1/auth/password/reset', [
            'reset_token' => $resetToken,
            'new_password' => 'NewStrongPassword123!',
            'confirm_password' => 'NewStrongPassword123!',
        ]);

        $resetResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Password reset successfully. Please log in using your new password.',
                'data' => null,
            ]);

        // 4. Verify login with new password
        $this->assertTrue(Hash::check('NewStrongPassword123!', $user->fresh()->password));
    }

    /**
     * Test Profile Retrieval and Profile Update.
     */
    public function test_customer_profile_retrieval_and_update(): void
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+1234567890',
            'address' => '123 Initial Street',
            'password' => Hash::make('Password123!'),
            'role' => 'customer',
            'source' => AuthSource::EMAIL,
            'account_status' => AccountStatus::VERIFIED,
            'profile_status' => ProfileStatus::INCOMPLETE,
        ]);

        $token = $user->createToken('test')->plainTextToken;

        // 1. Get Profile
        $profileResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/auth/profile');

        $profileResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status_code' => 200,
                'message' => 'Profile retrieved successfully.',
                'data' => [
                    'id' => $user->id,
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                    'phone' => '+1234567890',
                    'role' => 'customer',
                    'source' => 'email',
                    'account_status' => 'verified',
                    'profile_status' => 'incomplete',
                ],
            ]);

        // 2. Complete Profile
        $updateResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->patchJson('/api/v1/profile', [
                'name' => 'John Updated',
                'phone' => '+9876543210',
                'address' => '456 Updated Boulevard',
            ]);

        $updateResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => 'John Updated',
                    'phone' => '+9876543210',
                    'address' => '456 Updated Boulevard',
                    'profile_status' => 'complete',
                ],
            ]);

        $this->assertEquals(ProfileStatus::COMPLETE, $user->fresh()->profile_status);
    }

    /**
     * Test Social Login (Google).
     */
    public function test_google_social_login_creates_and_authenticates_customer(): void
    {
        $response = $this->postJson('/api/v1/auth/social', [
            'provider' => 'google',
            'id_token' => 'test_google_token_googleuser@example.com',
            'role' => 'customer',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status_code' => 200,
                'message' => 'Google authentication successful.',
                'data' => [
                    'user' => [
                        'email' => 'googleuser@example.com',
                        'role' => 'customer',
                        'source' => 'google',
                        'account_status' => 'verified',
                        'profile_status' => 'incomplete',
                    ],
                    'token_type' => 'Bearer',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'googleuser@example.com',
            'source' => 'google',
            'account_status' => 'verified',
        ]);

        $this->assertDatabaseHas('social_accounts', [
            'provider' => 'google',
            'provider_email' => 'googleuser@example.com',
        ]);
    }

    /**
     * Test Logout.
     */
    public function test_authenticated_user_can_logout(): void
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+1234567890',
            'password' => Hash::make('Password123!'),
            'role' => 'customer',
            'source' => AuthSource::EMAIL,
            'account_status' => AccountStatus::VERIFIED,
            'profile_status' => ProfileStatus::COMPLETE,
        ]);

        $token = $user->createToken('test')->plainTextToken;

        $logoutResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/auth/logout');

        $logoutResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Logout successful.',
                'data' => null,
            ]);

        $this->assertCount(0, $user->fresh()->tokens);
    }

    /**
     * Test Authenticated Change Password Flow.
     */
    public function test_authenticated_customer_can_change_password(): void
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+1234567890',
            'password' => Hash::make('CurrentPassword123!'),
            'role' => 'customer',
            'source' => AuthSource::EMAIL,
            'account_status' => AccountStatus::VERIFIED,
            'profile_status' => ProfileStatus::COMPLETE,
        ]);

        $token = $user->createToken('test')->plainTextToken;

        // 1. Request OTP for change password
        $otpReqResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/auth/password/change/request-otp', [
                'current_password' => 'CurrentPassword123!',
            ]);

        $otpReqResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'purpose' => 'reset_password',
                ],
            ]);

        // Create known OTP
        Otp::where('identifier', 'john@example.com')->delete();
        Otp::create([
            'user_id' => $user->id,
            'identifier' => 'john@example.com',
            'purpose' => OtpPurpose::RESET_PASSWORD,
            'otp_hash' => Hash::make('789012'),
            'attempt_count' => 0,
            'max_attempts' => 5,
            'expires_at' => now()->addMinutes(5),
        ]);

        // 2. Verify OTP for reset_password
        $verifyResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/auth/otp/verify', [
                'identifier' => 'john@example.com',
                'otp' => '789012',
                'purpose' => 'reset_password',
            ]);

        $verifyResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'purpose' => 'reset_password',
                ],
            ]);

        $changeToken = $verifyResponse->json('data.password_change_token');
        $this->assertNotEmpty($changeToken);

        // 3. Change password with token
        $changeResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/auth/password/change', [
                'password_change_token' => $changeToken,
                'new_password' => 'UpdatedBrandNew123!',
                'confirm_password' => 'UpdatedBrandNew123!',
            ]);

        $changeResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Password changed successfully.',
            ]);

        $this->assertTrue(Hash::check('UpdatedBrandNew123!', $user->fresh()->password));
    }

    /**
     * Test Token Refresh.
     */
    public function test_token_refresh_issues_new_token(): void
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+1234567890',
            'password' => Hash::make('Password123!'),
            'role' => 'customer',
            'source' => AuthSource::EMAIL,
            'account_status' => AccountStatus::VERIFIED,
            'profile_status' => ProfileStatus::COMPLETE,
        ]);

        $token = $user->createToken('test')->plainTextToken;

        $refreshResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/auth/refresh');

        $refreshResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Access token refreshed successfully.',
                'data' => [
                    'token_type' => 'Bearer',
                    'expires_in' => 86400,
                ],
            ]);

        $this->assertNotEmpty($refreshResponse->json('data.access_token'));
    }

    /**
     * Test OTP Resend Cooldown Limit (429).
     */
    public function test_otp_resend_cooldown_limit_returns_429(): void
    {
        // 1. Send first OTP
        $this->postJson('/api/v1/auth/otp/send', [
            'identifier' => 'testuser@example.com',
            'purpose' => 'registration',
        ])->assertStatus(200);

        // 2. Immediately send again within 60s cooldown
        $secondResponse = $this->postJson('/api/v1/auth/otp/send', [
            'identifier' => 'testuser@example.com',
            'purpose' => 'registration',
        ]);

        $secondResponse->assertStatus(429)
            ->assertJson([
                'success' => false,
                'error_code' => 'ERR_OTP_RESEND_LIMIT',
            ]);
    }

    /**
     * Test Restricted Account Login Denied (403).
     */
    public function test_suspended_or_blocked_account_login_is_denied(): void
    {
        $user = User::create([
            'name' => 'Blocked User',
            'email' => 'blocked@example.com',
            'phone' => '+1234567890',
            'password' => Hash::make('Password123!'),
            'role' => 'customer',
            'source' => AuthSource::EMAIL,
            'account_status' => AccountStatus::BLOCKED,
            'profile_status' => ProfileStatus::INCOMPLETE,
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'blocked@example.com',
            'password' => 'Password123!',
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'error_code' => 'ERR_ACCOUNT_RESTRICTED',
                'message' => 'Your account is currently restricted.',
            ]);
    }

    /**
     * Test Social Login returns 409 when unlinked email account exists.
     */
    public function test_social_login_returns_409_when_unlinked_email_exists(): void
    {
        User::create([
            'name' => 'Existing User',
            'email' => 'existing@example.com',
            'phone' => '+1234567890',
            'password' => Hash::make('Password123!'),
            'role' => 'customer',
            'source' => AuthSource::EMAIL,
            'account_status' => AccountStatus::VERIFIED,
            'profile_status' => ProfileStatus::COMPLETE,
        ]);

        $response = $this->postJson('/api/v1/auth/social', [
            'provider' => 'google',
            'id_token' => 'test_google_token_existing@example.com',
            'role' => 'customer',
        ]);

        $response->assertStatus(409)
            ->assertJson([
                'success' => false,
                'error_code' => 'ERR_ACCOUNT_LINK_REQUIRED',
            ]);
    }

    /**
     * Test Swagger API Documentation UI and JSON spec are accessible.
     */
    public function test_swagger_documentation_is_accessible(): void
    {
        $response = $this->get('/api/documentation');
        $response->assertStatus(200);

        $specResponse = $this->get('/docs');
        $specResponse->assertStatus(200)
            ->assertJsonPath('info.title', 'EMAC ERP & Customer Authentication API');
    }

    /**
     * Test updating user profile with multiple addresses saves to user_addresses table.
     */
    public function test_update_profile_with_multiple_addresses_saves_to_table(): void
    {
        $user = User::create([
            'name' => 'John Customer',
            'email' => 'john.profile@example.com',
            'phone' => '+15551234567',
            'password' => Hash::make('Password123!'),
            'role' => 'customer',
            'source' => AuthSource::EMAIL,
            'account_status' => AccountStatus::VERIFIED,
            'profile_status' => ProfileStatus::INCOMPLETE,
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->patchJson('/api/v1/profile', [
                'name' => 'Johnathan Customer Updated',
                'phone' => '+15559876543',
                'addresses' => [
                    [
                        'country' => 'Cayman Islands',
                        'state' => 'Grand Cayman',
                        'city' => 'George Town',
                        'address' => '123 Seven Mile Beach Rd',
                        'is_primary' => true,
                    ],
                    [
                        'country' => 'United States',
                        'state' => 'Florida',
                        'city' => 'Miami',
                        'address' => '456 Brickell Ave, Suite 200',
                        'is_primary' => false,
                    ],
                ],
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Profile updated successfully.',
            ])
            ->assertJsonPath('data.name', 'Johnathan Customer Updated')
            ->assertJsonPath('data.phone', '+15559876543')
            ->assertJsonPath('data.profile_status', 'complete')
            ->assertJsonCount(2, 'data.addresses')
            ->assertJsonPath('data.addresses.0.city', 'George Town')
            ->assertJsonPath('data.addresses.0.is_primary', true)
            ->assertJsonPath('data.addresses.1.city', 'Miami')
            ->assertJsonPath('data.addresses.1.is_primary', false);

        $this->assertDatabaseHas('user_addresses', [
            'user_id' => $user->id,
            'country' => 'Cayman Islands',
            'city' => 'George Town',
            'is_primary' => 1,
        ]);

        $this->assertDatabaseHas('user_addresses', [
            'user_id' => $user->id,
            'country' => 'United States',
            'city' => 'Miami',
            'is_primary' => 0,
        ]);

        // Verify GET /api/v1/auth/profile returns the addresses
        $getProfileResponse = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/auth/profile');

        $getProfileResponse->assertStatus(200)
            ->assertJsonPath('data.name', 'Johnathan Customer Updated')
            ->assertJsonCount(2, 'data.addresses');
    }
}
