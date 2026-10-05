<?php

namespace Tests\Feature;

use App\Enums\QuoteStatus;
use App\Models\Quote;
use App\Models\QuoteMessage;
use App\Models\Role;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class QuoteMessageHistoryTest extends TestCase
{
    use DatabaseTransactions;

    public function test_customer_question_via_api_records_message_history(): void
    {
        $customer = User::factory()->create([
            'status' => 'active',
        ]);

        $serviceRequest = ServiceRequest::create([
            'user_id' => $customer->id,
            'status' => 'active',
            'type' => 'app',
            'description' => 'Test inquiry request',
        ]);

        $quote = Quote::create([
            'quote_number' => 'QUO-TEST-001',
            'user_id' => $customer->id,
            'service_request_id' => $serviceRequest->id,
            'service_description' => 'Sample initial scope',
            'labor_cost' => 100,
            'total_price' => 100,
            'status' => QuoteStatus::PENDING,
        ]);

        Sanctum::actingAs($customer);

        $response = $this->postJson("/api/v1/quotes/{$quote->id}/respond", [
            'action' => 'ask_question',
            'customer_notes' => 'Can you please clarify if permits are included in this total?',
        ]);

        $response->assertOk();
        $response->assertJsonPath('data.status', QuoteStatus::REVIEW_REQUESTED->value);
        $response->assertJsonPath('data.customer_notes', 'Can you please clarify if permits are included in this total?');
        $response->assertJsonCount(1, 'data.messages');
        $response->assertJsonPath('data.messages.0.sender_type', 'customer');
        $response->assertJsonPath('data.messages.0.message', 'Can you please clarify if permits are included in this total?');

        $this->assertDatabaseHas('quote_messages', [
            'quote_id' => $quote->id,
            'user_id' => $customer->id,
            'sender_type' => 'customer',
            'message' => 'Can you please clarify if permits are included in this total?',
        ]);
    }

    public function test_admin_reply_records_message_and_updates_status(): void
    {
        $admin = User::factory()->create(['status' => 'active']);
        $adminRole = Role::firstOrCreate(['slug' => 'super-admin'], ['name' => 'Super Admin', 'description' => 'Super Administrator']);
        $admin->roles()->syncWithoutDetaching([$adminRole->id]);

        $customer = User::factory()->create(['status' => 'active']);

        $serviceRequest = ServiceRequest::create([
            'user_id' => $customer->id,
            'status' => 'active',
            'type' => 'app',
            'description' => 'Test inquiry request',
        ]);

        $quote = Quote::create([
            'quote_number' => 'QUO-TEST-002',
            'user_id' => $customer->id,
            'service_request_id' => $serviceRequest->id,
            'service_description' => 'Sample initial scope',
            'labor_cost' => 150,
            'total_price' => 150,
            'status' => QuoteStatus::REVIEW_REQUESTED,
        ]);

        // Customer message already logged
        QuoteMessage::create([
            'quote_id' => $quote->id,
            'user_id' => $customer->id,
            'sender_type' => 'customer',
            'message' => 'Is there any warranty on parts?',
        ]);

        // Admin sends reply via web route
        $response = $this->actingAs($admin)
            ->post(route('dashboard.quotes.messages', $quote), [
                'message' => 'Yes, 1 year full warranty on all parts and labor is included.',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('quote_messages', [
            'quote_id' => $quote->id,
            'user_id' => $admin->id,
            'sender_type' => 'admin',
            'message' => 'Yes, 1 year full warranty on all parts and labor is included.',
        ]);

        $quote->refresh();
        $this->assertEquals(QuoteStatus::REVIEW_REQUESTED, $quote->status);
        $this->assertEquals('Yes, 1 year full warranty on all parts and labor is included.', $quote->admin_notes);
        $this->assertCount(2, $quote->messages);
    }
}
