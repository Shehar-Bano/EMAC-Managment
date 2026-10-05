<?php

namespace Tests\Feature;

use App\Actions\Quote\CreateQuoteAction;
use App\Enums\QuoteStatus;
use App\Enums\ServiceRequestStatus;
use App\Models\Category;
use App\Models\Role;
use App\Models\ServiceRequest;
use App\Models\Subcategory;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ServiceRequestStatusWorkflowTest extends TestCase
{
    use DatabaseTransactions;

    public function test_service_request_status_does_not_change_when_quote_is_created(): void
    {
        $admin = User::factory()->create();
        $adminRole = Role::firstOrCreate(['slug' => 'super-admin'], ['name' => 'Super Admin', 'description' => 'Super Administrator']);
        $admin->roles()->syncWithoutDetaching([$adminRole->id]);

        $customer = User::factory()->create();
        $category = Category::create([
            'name' => 'HVAC Services',
            'slug' => 'hvac-services-test',
            'icon' => '❄️',
            'status' => 'active',
        ]);
        $subcategory = Subcategory::create([
            'category_id' => $category->id,
            'name' => 'AC Maintenance',
            'slug' => 'ac-maintenance-test',
            'icon' => '🔧',
            'status' => 'active',
        ]);

        $address = UserAddress::create([
            'user_id' => $customer->id,
            'address' => '123 Test Boulevard',
            'city' => 'George Town',
            'state' => 'Grand Cayman',
            'country' => 'Cayman Islands',
            'is_primary' => true,
        ]);

        $serviceRequest = ServiceRequest::create([
            'user_id' => $customer->id,
            'user_address_id' => $address->id,
            'category_id' => $category->id,
            'subcategory_id' => $subcategory->id,
            'priority' => 'normal',
            'status' => ServiceRequestStatus::PENDING,
            'description' => 'AC unit is making unusual vibration sounds.',
            'type' => 'app',
        ]);

        $this->assertEquals(ServiceRequestStatus::PENDING, $serviceRequest->status);

        $createQuoteAction = app(CreateQuoteAction::class);
        $quote = $createQuoteAction->execute($admin, [
            'service_request_id' => $serviceRequest->id,
            'service_description' => 'AC Compressor Inspection & Service',
            'labor_cost' => 150.00,
            'materials_cost' => 25.00,
            'expires_at' => now()->addDays(7)->toDateString(),
            'admin_notes' => 'Quote prepared for inspection.',
        ]);

        $this->assertNotNull($quote);
        $serviceRequest->refresh();

        // Status must remain unchanged (pending)
        $this->assertEquals(ServiceRequestStatus::PENDING, $serviceRequest->status);
    }

    public function test_admin_can_update_status_to_pending_active_completed_only(): void
    {
        $admin = User::factory()->create();
        $adminRole = Role::firstOrCreate(['slug' => 'super-admin'], ['name' => 'Super Admin', 'description' => 'Super Administrator']);
        $admin->roles()->syncWithoutDetaching([$adminRole->id]);

        $customer = User::factory()->create();
        $category = Category::create([
            'name' => 'Appliance Repair',
            'slug' => 'appliance-repair-test',
            'icon' => '🔌',
            'status' => 'active',
        ]);
        $subcategory = Subcategory::create([
            'category_id' => $category->id,
            'name' => 'Washing Machine Repair',
            'slug' => 'washing-machine-repair-test',
            'icon' => '🧺',
            'status' => 'active',
        ]);
        $address = UserAddress::create([
            'user_id' => $customer->id,
            'address' => '456 Test Street',
            'city' => 'West Bay',
            'state' => 'Grand Cayman',
            'country' => 'Cayman Islands',
            'is_primary' => true,
        ]);

        $serviceRequest = ServiceRequest::create([
            'user_id' => $customer->id,
            'user_address_id' => $address->id,
            'category_id' => $category->id,
            'subcategory_id' => $subcategory->id,
            'priority' => 'normal',
            'status' => ServiceRequestStatus::PENDING,
            'description' => 'Fix drum bearings.',
            'type' => 'app',
        ]);

        // 1. Update to active
        $response = $this->actingAs($admin)->patch("/dashboard/service-requests/{$serviceRequest->id}/status", [
            'status' => 'active',
        ]);
        $response->assertRedirect();
        $serviceRequest->refresh();
        $this->assertEquals(ServiceRequestStatus::ACTIVE, $serviceRequest->status);

        // 2. Update to completed
        $response = $this->actingAs($admin)->patch("/dashboard/service-requests/{$serviceRequest->id}/status", [
            'status' => 'completed',
        ]);
        $response->assertRedirect();
        $serviceRequest->refresh();
        $this->assertEquals(ServiceRequestStatus::COMPLETED, $serviceRequest->status);

        // 3. Update back to pending
        $response = $this->actingAs($admin)->patch("/dashboard/service-requests/{$serviceRequest->id}/status", [
            'status' => 'pending',
        ]);
        $response->assertRedirect();
        $serviceRequest->refresh();
        $this->assertEquals(ServiceRequestStatus::PENDING, $serviceRequest->status);

        // 4. Updating to quotesent or reject should fail validation with session errors
        $response = $this->actingAs($admin)->patch("/dashboard/service-requests/{$serviceRequest->id}/status", [
            'status' => 'quotesent',
        ]);
        $response->assertSessionHasErrors('status');

        $response = $this->actingAs($admin)->patch("/dashboard/service-requests/{$serviceRequest->id}/status", [
            'status' => 'reject',
        ]);
        $response->assertSessionHasErrors('status');
    }

    public function test_quote_workflow_supports_review_requested_and_returns_admin_response(): void
    {
        $admin = User::factory()->create();
        $adminRole = Role::firstOrCreate(['slug' => 'super-admin'], ['name' => 'Super Admin', 'description' => 'Super Administrator']);
        $admin->roles()->syncWithoutDetaching([$adminRole->id]);

        $customer = User::factory()->create();
        $category = Category::create(['name' => 'Roofing', 'slug' => 'roofing-test', 'status' => 'active']);
        $subcategory = Subcategory::create(['category_id' => $category->id, 'name' => 'Leak Patch', 'slug' => 'leak-patch-test', 'status' => 'active']);
        $address = UserAddress::create([
            'user_id' => $customer->id,
            'address' => '789 Cayman Way',
            'city' => 'George Town',
            'state' => 'Grand Cayman',
            'country' => 'Cayman Islands',
            'is_primary' => true,
        ]);

        $serviceRequest = ServiceRequest::create([
            'user_id' => $customer->id,
            'user_address_id' => $address->id,
            'category_id' => $category->id,
            'subcategory_id' => $subcategory->id,
            'priority' => 'normal',
            'status' => ServiceRequestStatus::PENDING,
            'description' => 'Roof tile cracked during storm.',
            'type' => 'app',
        ]);

        $createQuoteAction = app(CreateQuoteAction::class);
        $quote = $createQuoteAction->execute($admin, [
            'service_request_id' => $serviceRequest->id,
            'service_description' => 'Replace 3 cracked tiles and reseal flashing',
            'labor_cost' => 300.00,
            'materials_cost' => 80.00,
            'expires_at' => now()->addDays(10)->toDateString(),
        ]);

        // Customer responds with question / review request via API
        $response = $this->actingAs($customer, 'sanctum')->postJson("/api/v1/service-requests/{$serviceRequest->id}/quote/respond", [
            'action' => 'ask_question',
            'customer_notes' => 'Can you complete this by Friday before the rain?',
        ]);
        $response->assertOk();
        $quote->refresh();
        $this->assertEquals(QuoteStatus::ASK_FOR_QUESTION, $quote->status);

        // Admin updates status to review_requested and replies
        $response = $this->actingAs($admin)->patch("/dashboard/quotes/{$quote->id}/status", [
            'status' => 'review_requested',
            'admin_notes' => 'Yes, our technician is scheduled for Thursday morning.',
        ]);
        $response->assertRedirect();
        $quote->refresh();
        $this->assertEquals(QuoteStatus::REVIEW_REQUESTED, $quote->status);
        $this->assertEquals('Yes, our technician is scheduled for Thursday morning.', $quote->admin_notes);

        // Customer checks quote via API: admin_notes / admin_response must be present
        $apiResponse = $this->actingAs($customer, 'sanctum')->getJson("/api/v1/service-requests/{$serviceRequest->id}/quote");
        $apiResponse->assertOk();
        $apiResponse->assertJsonPath('data.status', 'review_requested');
        $apiResponse->assertJsonPath('data.admin_notes', 'Yes, our technician is scheduled for Thursday morning.');
        $apiResponse->assertJsonPath('data.admin_response', 'Yes, our technician is scheduled for Thursday morning.');
    }

    public function test_user_creation_with_single_role_id_radio_selection(): void
    {
        $admin = User::factory()->create();
        $adminRole = Role::firstOrCreate(['slug' => 'super-admin'], ['name' => 'Super Admin', 'description' => 'Super Administrator']);
        $admin->roles()->syncWithoutDetaching([$adminRole->id]);

        $techRole = Role::firstOrCreate(['slug' => 'technician'], ['name' => 'Field Technician', 'description' => 'Technician']);

        $response = $this->actingAs($admin)->post('/dashboard/users', [
            'name' => 'New Single Role Tech',
            'email' => 'tech.single@example.com',
            'phone' => '+15551234567',
            'status' => 'active',
            'password' => 'SecurePass123!',
            'type' => 'technicians',
            'role_id' => $techRole->id,
        ]);

        $response->assertRedirect();
        $newUser = User::where('email', 'tech.single@example.com')->first();
        $this->assertNotNull($newUser);
        $this->assertCount(1, $newUser->roles);
        $this->assertEquals($techRole->id, $newUser->roles->first()->id);
    }
}
