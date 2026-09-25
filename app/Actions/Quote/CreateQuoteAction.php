<?php

namespace App\Actions\Quote;

use App\Enums\QuoteStatus;
use App\Enums\ServiceRequestStatus;
use App\Models\Quote;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateQuoteAction
{
    /**
     * Create and issue a new price quote for a service request.
     *
     * @param  array<string, mixed>  $data
     */
    public function execute(User $sender, array $data): Quote
    {
        return DB::transaction(function () use ($sender, $data) {
            $serviceRequest = ServiceRequest::with('user')->findOrFail($data['service_request_id']);

            $laborCost = (float) ($data['labor_cost'] ?? 0);
            $materialsCost = (float) ($data['materials_cost'] ?? 0);
            $equipmentCost = (float) ($data['equipment_cost'] ?? 0);
            $tripCharge = (float) ($data['trip_charge'] ?? 0);
            $additionalCharges = (float) ($data['additional_charges'] ?? 0);
            $discount = (float) ($data['discount'] ?? 0);
            $taxRate = (float) ($data['tax_rate'] ?? 0);

            $subtotal = $laborCost + $materialsCost + $equipmentCost + $tripCharge + $additionalCharges;
            $netBeforeTax = max(0, $subtotal - $discount);
            $taxAmount = round($netBeforeTax * ($taxRate / 100), 2);
            $totalPrice = round($netBeforeTax + $taxAmount, 2);

            $quote = Quote::create([
                'service_request_id' => $serviceRequest->id,
                'user_id' => $serviceRequest->user_id,
                'sent_by' => $sender->id,
                'service_description' => $data['service_description'],
                'labor_cost' => $laborCost,
                'materials_cost' => $materialsCost,
                'equipment_cost' => $equipmentCost,
                'trip_charge' => $tripCharge,
                'additional_charges' => $additionalCharges,
                'discount' => $discount,
                'tax_rate' => $taxRate,
                'tax_amount' => $taxAmount,
                'total_price' => $totalPrice,
                'terms_and_conditions' => $data['terms_and_conditions'] ?? $this->defaultTerms(),
                'expires_at' => $data['expires_at'],
                'status' => QuoteStatus::PENDING,
                'admin_notes' => $data['admin_notes'] ?? null,
            ]);

            // Update service request status to approved/quoted when quote is issued
            if ($serviceRequest->status === ServiceRequestStatus::PENDING || $serviceRequest->status === ServiceRequestStatus::IN_REVIEW) {
                $serviceRequest->update(['status' => ServiceRequestStatus::APPROVED]);
            }

            return $quote->load(['serviceRequest', 'user', 'sender']);
        });
    }

    /**
     * Default terms and conditions boilerplate for quotes.
     */
    protected function defaultTerms(): string
    {
        return "1. This quotation is valid until the specified expiration date.\n".
               "2. Pricing includes all labor, materials, and equipment specified above.\n".
               "3. Any additional work requested outside this scope will be quoted separately.\n".
               "4. Payment is due upon completion of services unless otherwise agreed.\n".
               '5. Cancellations made less than 24 hours before scheduled appointment may incur a trip charge.';
    }
}
