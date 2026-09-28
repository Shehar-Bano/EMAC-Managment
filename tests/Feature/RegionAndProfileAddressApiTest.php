<?php

namespace Tests\Feature;

use App\Models\Region;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RegionAndProfileAddressApiTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['slug' => 'customer'], ['name' => 'Customer', 'description' => 'Customer role']);
    }

    public function test_can_get_regions_list_api(): void
    {
        $region = Region::firstOrCreate(
            ['slug' => 'grand-cayman'],
            [
                'name' => 'Grand Cayman',
                'code' => 'GCM',
                'currency' => 'KYD',
                'description' => 'Grand Cayman territory',
                'status' => 'active',
            ]
        );

        $response = $this->getJson('/api/v1/regions');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'code',
                        'currency',
                        'description',
                        'status',
                    ],
                ],
            ]);
    }

    public function test_can_get_all_regions_without_pagination_when_all_flag_passed(): void
    {
        Region::firstOrCreate(
            ['slug' => 'florida'],
            [
                'name' => 'Florida',
                'code' => 'FL',
                'currency' => 'USD',
                'status' => 'active',
            ]
        );

        $response = $this->getJson('/api/v1/regions?all=1');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'code',
                        'currency',
                    ],
                ],
            ]);

        $this->assertArrayNotHasKey('meta', $response->json());
    }

    public function test_can_get_single_region_details_api(): void
    {
        $region = Region::firstOrCreate(
            ['slug' => 'jamaica'],
            [
                'name' => 'Jamaica',
                'code' => 'JM',
                'currency' => 'JMD',
                'status' => 'active',
            ]
        );

        $response = $this->getJson("/api/v1/regions/{$region->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $region->id)
            ->assertJsonPath('data.name', 'Jamaica')
            ->assertJsonPath('data.code', 'JM')
            ->assertJsonPath('data.currency', 'JMD');
    }

    public function test_user_can_register_with_region_id_in_address(): void
    {
        $region = Region::firstOrCreate(
            ['slug' => 'grand-cayman'],
            [
                'name' => 'Grand Cayman',
                'code' => 'GCM',
                'currency' => 'KYD',
                'status' => 'active',
            ]
        );

        $email = 'region.customer.'.uniqid().'@emac.test';

        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Cayman Resident',
            'email' => $email,
            'phone' => '+13459491234',
            'address' => '78 West Bay Road',
            'region_id' => $region->id,
            'city' => 'George Town',
            'password' => 'Password123!',
            'confirm_password' => 'Password123!',
            'terms_accepted' => true,
            'privacy_policy_accepted' => true,
            'role' => 'customer',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true);

        $user = User::where('email', $email)->firstOrFail();

        $this->assertDatabaseHas('user_addresses', [
            'user_id' => $user->id,
            'region_id' => $region->id,
            'address' => '78 West Bay Road',
            'city' => 'George Town',
            'is_primary' => 1,
        ]);
    }

    public function test_user_profile_edit_saves_region_id_and_returns_region_details(): void
    {
        $region1 = Region::firstOrCreate(
            ['slug' => 'grand-cayman'],
            [
                'name' => 'Grand Cayman',
                'code' => 'GCM',
                'currency' => 'KYD',
                'status' => 'active',
            ]
        );

        $region2 = Region::firstOrCreate(
            ['slug' => 'florida'],
            [
                'name' => 'Florida',
                'code' => 'FL',
                'currency' => 'USD',
                'status' => 'active',
            ]
        );

        $user = User::factory()->create([
            'name' => 'Profile Customer',
            'email' => 'profile.customer.'.uniqid().'@emac.test',
            'role' => 'customer',
            'status' => 'active',
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->patchJson('/api/v1/profile', [
                'name' => 'Updated Profile Customer',
                'phone' => '+13459499999',
                'addresses' => [
                    [
                        'region_id' => $region1->id,
                        'city' => 'George Town',
                        'address' => '100 South Church St',
                        'is_primary' => true,
                    ],
                    [
                        'region_id' => $region2->id,
                        'city' => 'Fort Lauderdale',
                        'address' => '200 Las Olas Blvd',
                        'is_primary' => false,
                    ],
                ],
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'Updated Profile Customer')
            ->assertJsonCount(2, 'data.addresses')
            ->assertJsonPath('data.addresses.0.region_id', $region1->id)
            ->assertJsonPath('data.addresses.0.region_name', 'Grand Cayman')
            ->assertJsonPath('data.addresses.0.region_code', 'GCM')
            ->assertJsonPath('data.addresses.0.currency', 'KYD')
            ->assertJsonPath('data.addresses.0.is_primary', true)
            ->assertJsonPath('data.addresses.1.region_id', $region2->id)
            ->assertJsonPath('data.addresses.1.region_name', 'Florida')
            ->assertJsonPath('data.addresses.1.region_code', 'FL')
            ->assertJsonPath('data.addresses.1.currency', 'USD')
            ->assertJsonPath('data.addresses.1.is_primary', false);

        $this->assertDatabaseHas('user_addresses', [
            'user_id' => $user->id,
            'region_id' => $region1->id,
            'city' => 'George Town',
            'is_primary' => 1,
        ]);

        $this->assertDatabaseHas('user_addresses', [
            'user_id' => $user->id,
            'region_id' => $region2->id,
            'city' => 'Fort Lauderdale',
            'is_primary' => 0,
        ]);

        // Verify GET /api/v1/profile
        $getProfileResponse = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/profile');

        $getProfileResponse->assertStatus(200)
            ->assertJsonPath('data.addresses.0.region_id', $region1->id)
            ->assertJsonPath('data.addresses.0.region_name', 'Grand Cayman');
    }
}
