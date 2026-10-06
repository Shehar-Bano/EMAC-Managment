<?php

namespace Tests\Feature;

use App\Actions\User\CreateUserAction;
use App\Actions\User\UpdateUserAction;
use App\Models\Category;
use App\Models\Region;
use App\Models\Role;
use App\Models\Subcategory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TechnicianProfileAndSkillsTest extends TestCase
{
    use DatabaseTransactions;

    protected Role $technicianRole;

    protected Category $category;

    protected Subcategory $sub1;

    protected Subcategory $sub2;

    protected Region $region;

    protected function setUp(): void
    {
        parent::setUp();

        $this->technicianRole = Role::firstOrCreate(['slug' => 'technician'], [
            'name' => 'Technician',
            'description' => 'Technician role',
        ]);

        $this->region = Region::firstOrCreate(['slug' => 'grand-cayman-west'], [
            'name' => 'Grand Cayman - Western District',
            'code' => 'GCM-WEST',
            'currency' => 'KYD',
            'description' => 'Western District',
            'status' => 'active',
        ]);

        $this->category = Category::firstOrCreate(['slug' => 'plumbing-services'], [
            'name' => 'Plumbing Services',
            'description' => 'Plumbing repairs and installations',
            'icon' => 'wrench',
            'status' => 'active',
        ]);

        $this->sub1 = Subcategory::firstOrCreate(['slug' => 'pipe-leak-repair', 'category_id' => $this->category->id], [
            'name' => 'Pipe Leak Repair & Diagnostic',
            'description' => 'Fix leaking pipes',
            'icon' => '🔧',
            'status' => 'active',
        ]);

        $this->sub2 = Subcategory::firstOrCreate(['slug' => 'water-heater-install', 'category_id' => $this->category->id], [
            'name' => 'Water Heater Installation',
            'description' => 'Install tank and tankless water heaters',
            'icon' => '⚡',
            'status' => 'active',
        ]);
    }

    public function test_can_create_technician_with_skill_and_subskills_via_action(): void
    {
        $action = app(CreateUserAction::class);

        $tech = $action->execute([
            'name' => 'Noah Davis',
            'email' => 'noah.davis.test@emac.ky',
            'phone' => '+1 345 924 8831',
            'password' => 'Password123!',
            'status' => 'active',
            'role' => 'technician',
            'roles' => [$this->technicianRole->id],
            'category_id' => $this->category->id,
            'subcategories' => [$this->sub1->id, $this->sub2->id],
            'duty_status' => 'on_duty',
            'experience_years' => 8,
            'bio' => 'Lead Master Technician specializing in luxury residential hydraulics.',
            'emergency_contact_name' => 'Sarah Davis',
            'emergency_contact_phone' => '+1 345 925 1102',
            'certification_id' => 'EMAC-TECH-CERT-0024',
            'certification_body' => 'Cayman Islands Building & Trade Guild',
            'is_verified' => true,
            'addresses' => [
                [
                    'region_id' => $this->region->id,
                    'city' => 'George Town',
                    'country' => 'Cayman Islands',
                    'address' => '42 Seven Mile Beach Rd',
                    'zipcode' => 'KY1-1102',
                ],
            ],
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $tech->id,
            'email' => 'noah.davis.test@emac.ky',
            'category_id' => $this->category->id,
            'duty_status' => 'on_duty',
            'experience_years' => 8,
            'certification_id' => 'EMAC-TECH-CERT-0024',
        ]);

        $this->assertDatabaseHas('usersubskills', [
            'user_id' => $tech->id,
            'subcategory_id' => $this->sub1->id,
        ]);

        $this->assertDatabaseHas('usersubskills', [
            'user_id' => $tech->id,
            'subcategory_id' => $this->sub2->id,
        ]);

        $this->assertEquals(2, $tech->subcategories()->count());
        $this->assertEquals($this->category->id, $tech->category->id);
    }

    public function test_can_update_technician_skill_and_subskills_via_action(): void
    {
        $createAction = app(CreateUserAction::class);
        $updateAction = app(UpdateUserAction::class);

        $tech = $createAction->execute([
            'name' => 'Old Tech Name',
            'email' => 'tech.update.test@emac.ky',
            'phone' => '+1 345 000 0000',
            'password' => 'Password123!',
            'status' => 'active',
            'role' => 'technician',
            'roles' => [$this->technicianRole->id],
            'category_id' => $this->category->id,
            'subcategories' => [$this->sub1->id],
        ]);

        $this->assertEquals(1, $tech->subcategories()->count());

        $updateAction->execute($tech, [
            'name' => 'Updated Tech Name',
            'email' => 'tech.update.test@emac.ky',
            'phone' => '+1 345 111 2222',
            'status' => 'active',
            'duty_status' => 'break',
            'experience_years' => 10,
            'subcategories' => [$this->sub1->id, $this->sub2->id],
        ]);

        $tech->refresh();
        $this->assertEquals('Updated Tech Name', $tech->name);
        $this->assertEquals('break', $tech->duty_status);
        $this->assertEquals(10, $tech->experience_years);
        $this->assertEquals(2, $tech->subcategories()->count());
    }

    public function test_technician_profile_api_returns_exact_structure(): void
    {
        $action = app(CreateUserAction::class);

        $tech = $action->execute([
            'name' => 'Noah Davis',
            'email' => 'noah.davis.api@emac.ky',
            'phone' => '+1 345 924 8831',
            'password' => 'Password123!',
            'status' => 'active',
            'role' => 'technician',
            'roles' => [$this->technicianRole->id],
            'category_id' => $this->category->id,
            'subcategories' => [$this->sub1->id, $this->sub2->id],
            'duty_status' => 'on_duty',
            'experience_years' => 8,
            'bio' => 'Lead Master Technician specializing in luxury residential hydraulics, high-pressure line diagnostics, and bespoke hardware repair.',
            'emergency_contact_name' => 'Sarah Davis',
            'emergency_contact_phone' => '+1 345 925 1102',
            'certification_id' => 'EMAC-TECH-CERT-0024',
            'certification_body' => 'Cayman Islands Building & Trade Guild',
            'is_verified' => true,
            'addresses' => [
                [
                    'region_id' => $this->region->id,
                    'city' => 'George Town',
                    'country' => 'Cayman Islands',
                    'address' => '42 Seven Mile Beach Rd',
                    'zipcode' => 'KY1-1102',
                ],
            ],
        ]);

        $response = $this->actingAs($tech, 'sanctum')->getJson('/api/v1/technician/profile');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status_code' => 200,
                'message' => 'Technician profile retrieved successfully.',
                'data' => [
                    'id' => $tech->id,
                    'user_id' => $tech->id,
                    'name' => 'Noah Davis',
                    'email' => 'noah.davis.api@emac.ky',
                    'phone' => '+1 345 924 8831',
                    'role' => 'technician',
                    'certification_id' => 'EMAC-TECH-CERT-0024',
                    'certification_body' => 'Cayman Islands Building & Trade Guild',
                    'is_verified' => true,
                    'duty_status' => 'on_duty',
                    'experience_years' => 8,
                    'bio' => 'Lead Master Technician specializing in luxury residential hydraulics, high-pressure line diagnostics, and bespoke hardware repair.',
                    'emergency_contact_name' => 'Sarah Davis',
                    'emergency_contact_phone' => '+1 345 925 1102',
                    'statistics' => [
                        'rating' => 4.92,
                        'total_reviews' => 86,
                        'total_jobs_completed' => 142,
                        'active_jobs_count' => 1,
                        'completion_rate' => 98.6,
                    ],
                    'operating_region' => [
                        'id' => $this->region->id,
                        'name' => 'Grand Cayman - Western District',
                        'code' => 'GCM-WEST',
                        'city' => 'George Town',
                    ],
                    'specializations' => [
                        [
                            'id' => $this->category->id,
                            'name' => 'Plumbing Services',
                            'slug' => 'plumbing-services',
                            'is_primary' => true,
                        ],
                    ],
                    'services_provided' => [
                        [
                            'id' => $this->sub1->id,
                            'category_id' => $this->category->id,
                            'category_name' => 'Plumbing Services',
                            'name' => 'Pipe Leak Repair & Diagnostic',
                            'slug' => 'pipe-leak-repair',
                            'is_active' => true,
                        ],
                        [
                            'id' => $this->sub2->id,
                            'category_id' => $this->category->id,
                            'category_name' => 'Plumbing Services',
                            'name' => 'Water Heater Installation',
                            'slug' => 'water-heater-install',
                            'is_active' => true,
                        ],
                    ],
                ],
            ]);
    }

    public function test_can_update_technician_profile_via_api(): void
    {
        $action = app(CreateUserAction::class);

        $tech = $action->execute([
            'name' => 'Initial Tech',
            'email' => 'tech.api.update@emac.ky',
            'phone' => '+1 345 111 0000',
            'password' => 'Password123!',
            'status' => 'active',
            'role' => 'technician',
            'roles' => [$this->technicianRole->id],
            'category_id' => $this->category->id,
            'subcategories' => [$this->sub1->id],
            'duty_status' => 'on_duty',
        ]);

        $response = $this->actingAs($tech, 'sanctum')->postJson('/api/v1/technician/profile', [
            'duty_status' => 'off_duty',
            'bio' => 'Updated technician bio.',
            'experience_years' => 12,
            'subcategories' => [$this->sub1->id, $this->sub2->id],
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status_code' => 200,
                'message' => 'Technician profile updated successfully.',
                'data' => [
                    'duty_status' => 'off_duty',
                    'experience_years' => 12,
                    'bio' => 'Updated technician bio.',
                ],
            ]);

        $this->assertEquals(2, $tech->subcategories()->count());
    }

    public function test_can_update_technician_profile_via_put_request(): void
    {
        $action = app(CreateUserAction::class);

        $tech = $action->execute([
            'name' => 'Noah Davis',
            'email' => 'noah.put.test@emac.ky',
            'phone' => '+1 345 924 8831',
            'password' => 'Password123!',
            'status' => 'active',
            'role' => 'technician',
            'roles' => [$this->technicianRole->id],
            'category_id' => $this->category->id,
            'subcategories' => [$this->sub1->id],
            'duty_status' => 'on_duty',
            'experience_years' => 5,
            'bio' => 'Initial bio',
            'emergency_contact_name' => 'Old Contact',
            'emergency_contact_phone' => '+1 345 000 0000',
            'addresses' => [
                [
                    'region_id' => $this->region->id,
                    'city' => 'George Town',
                    'country' => 'Cayman Islands',
                    'address' => 'Old Address',
                    'zipcode' => 'KY1-1102',
                ],
            ],
        ]);

        $newRegion = Region::firstOrCreate(['slug' => 'cayman-brac'], [
            'name' => 'Cayman Brac',
            'code' => 'CYB',
            'currency' => 'KYD',
            'description' => 'Sister Island',
            'status' => 'active',
        ]);

        $response = $this->actingAs($tech, 'sanctum')->putJson('/api/v1/technician/profile', [
            'name' => 'Noah Davis Updated',
            'phone' => '+1 345 999 8888',
            'bio' => 'Lead Master Technician specializing in luxury residential hydraulics, high-pressure line diagnostics, and bespoke hardware repair.',
            'experience_years' => 8,
            'duty_status' => 'break',
            'emergency_contact_name' => 'Sarah Davis',
            'emergency_contact_phone' => '+1 345 925 1102',
            'operating_territory_id' => $newRegion->id,
            'city' => 'West End',
            'address' => '99 Bluff Road',
            'subcategories' => [$this->sub1->id, $this->sub2->id],
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status_code' => 200,
                'message' => 'Technician profile updated successfully.',
                'data' => [
                    'id' => $tech->id,
                    'name' => 'Noah Davis Updated',
                    'phone' => '+1 345 999 8888',
                    'bio' => 'Lead Master Technician specializing in luxury residential hydraulics, high-pressure line diagnostics, and bespoke hardware repair.',
                    'experience_years' => 8,
                    'duty_status' => 'break',
                    'emergency_contact_name' => 'Sarah Davis',
                    'emergency_contact_phone' => '+1 345 925 1102',
                    'operating_region' => [
                        'id' => $newRegion->id,
                        'name' => 'Cayman Brac',
                        'city' => 'West End',
                    ],
                ],
            ]);

        $tech->refresh();
        $this->assertEquals('Noah Davis Updated', $tech->name);
        $this->assertEquals('+1 345 999 8888', $tech->phone);
        $this->assertEquals('Sarah Davis', $tech->emergency_contact_name);
        $this->assertEquals('+1 345 925 1102', $tech->emergency_contact_phone);
        $this->assertEquals(8, $tech->experience_years);
        $this->assertEquals('break', $tech->duty_status);
        $this->assertEquals(2, $tech->subcategories()->count());

        $primaryAddress = $tech->addresses()->where('is_primary', true)->first();
        $this->assertNotNull($primaryAddress);
        $this->assertEquals($newRegion->id, $primaryAddress->region_id);
        $this->assertEquals('West End', $primaryAddress->city);
    }

    public function test_can_toggle_technician_duty_status(): void
    {
        $action = app(CreateUserAction::class);

        $tech = $action->execute([
            'name' => 'Noah Davis',
            'email' => 'noah.duty.test@emac.ky',
            'phone' => '+1 345 924 8831',
            'password' => 'Password123!',
            'status' => 'active',
            'role' => 'technician',
            'roles' => [$this->technicianRole->id],
            'duty_status' => 'off_duty',
        ]);

        // Toggle to ON_DUTY (case-insensitive test)
        $response = $this->actingAs($tech, 'sanctum')->patchJson('/api/v1/technician/duty-status', [
            'duty_status' => 'ON_DUTY',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status_code' => 200,
                'message' => 'Duty status changed to on_duty.',
                'data' => [
                    'technician_id' => $tech->id,
                    'duty_status' => 'on_duty',
                ],
            ]);

        $tech->refresh();
        $this->assertEquals('on_duty', $tech->duty_status);

        // Toggle to break
        $response = $this->actingAs($tech, 'sanctum')->patchJson('/api/v1/technician/duty-status', [
            'duty_status' => 'break',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status_code' => 200,
                'message' => 'Duty status changed to break.',
                'data' => [
                    'technician_id' => $tech->id,
                    'duty_status' => 'break',
                ],
            ]);

        $tech->refresh();
        $this->assertEquals('break', $tech->duty_status);
    }

    public function test_can_get_technician_services_catalogue_with_is_assigned(): void
    {
        $action = app(CreateUserAction::class);

        $tech = $action->execute([
            'name' => 'Noah Davis',
            'email' => 'noah.services.test@emac.ky',
            'phone' => '+1 345 924 8831',
            'password' => 'Password123!',
            'status' => 'active',
            'role' => 'technician',
            'roles' => [$this->technicianRole->id],
            'category_id' => $this->category->id,
            'subcategories' => [$this->sub1->id], // only sub1 assigned
        ]);

        $response = $this->actingAs($tech, 'sanctum')->getJson('/api/v1/technician/services');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status_code' => 200,
                'message' => 'Services catalogue retrieved successfully.',
            ])
            ->assertJsonFragment([
                'id' => $this->sub1->id,
                'name' => 'Pipe Leak Repair & Diagnostic',
                'is_assigned' => true,
            ])
            ->assertJsonFragment([
                'id' => $this->sub2->id,
                'name' => 'Water Heater Installation',
                'is_assigned' => false,
            ]);
    }

    public function test_can_update_technician_services_via_dedicated_endpoint(): void
    {
        $action = app(CreateUserAction::class);

        $tech = $action->execute([
            'name' => 'Noah Davis',
            'email' => 'noah.serv.update@emac.ky',
            'phone' => '+1 345 924 8831',
            'password' => 'Password123!',
            'status' => 'active',
            'role' => 'technician',
            'roles' => [$this->technicianRole->id],
            'category_id' => $this->category->id,
            'subcategories' => [$this->sub1->id],
        ]);

        $response = $this->actingAs($tech, 'sanctum')->putJson('/api/v1/technician/services', [
            'subcategories' => [$this->sub1->id, $this->sub2->id],
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status_code' => 200,
                'message' => 'Technician sub-services updated successfully.',
                'data' => [
                    'technician_id' => $tech->id,
                    'assigned_count' => 2,
                ],
            ]);

        $tech->refresh();
        $this->assertEquals(2, $tech->subcategories()->count());
    }

    public function test_can_upload_technician_avatar(): void
    {
        Storage::fake('public');

        $action = app(CreateUserAction::class);

        $tech = $action->execute([
            'name' => 'Noah Davis',
            'email' => 'noah.avatar.test@emac.ky',
            'phone' => '+1 345 924 8831',
            'password' => 'Password123!',
            'status' => 'active',
            'role' => 'technician',
            'roles' => [$this->technicianRole->id],
        ]);

        $file = UploadedFile::fake()->create('tech_avatar.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($tech, 'sanctum')->postJson('/api/v1/technician/avatar', [
            'avatar' => $file,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status_code' => 200,
                'message' => 'Avatar uploaded successfully.',
            ])
            ->assertJsonStructure([
                'data' => [
                    'avatar_url',
                ],
            ]);

        $tech->refresh();
        $this->assertNotNull($tech->avatar);
        Storage::disk('public')->assertExists($tech->avatar);
    }
}
