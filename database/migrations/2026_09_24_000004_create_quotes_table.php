<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->string('quote_number')->unique();
            $table->foreignId('service_request_id')->constrained('service_requests')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->comment('Customer ID');
            $table->foreignId('sent_by')->nullable()->constrained('users')->nullOnDelete()->comment('SuperAdmin or Staff ID who sent quote');

            $table->text('service_description');
            $table->decimal('labor_cost', 12, 2)->default(0.00);
            $table->decimal('materials_cost', 12, 2)->default(0.00);
            $table->decimal('equipment_cost', 12, 2)->default(0.00);
            $table->decimal('trip_charge', 12, 2)->default(0.00)->comment('Trip or service charge');
            $table->decimal('additional_charges', 12, 2)->default(0.00);
            $table->decimal('discount', 12, 2)->default(0.00);
            $table->decimal('tax_rate', 5, 2)->default(0.00)->comment('Tax percentage e.g. 5.00%');
            $table->decimal('tax_amount', 12, 2)->default(0.00);
            $table->decimal('total_price', 12, 2)->default(0.00);

            $table->longText('terms_and_conditions')->nullable();
            $table->date('expires_at')->nullable();
            $table->string('status', 30)->default('pending')->index();

            $table->text('customer_notes')->nullable()->comment('Notes, reason for decline, or question from customer');
            $table->text('admin_notes')->nullable();

            $table->timestamp('approved_at')->nullable();
            $table->timestamp('declined_at')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index(['service_request_id', 'status']);
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
