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
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('user_address_id')->nullable()->constrained('user_addresses')->nullOnDelete();
            $table->text('description');
            $table->text('property_information');
            $table->date('preferred_service_date');
            $table->string('preferred_service_time');
            $table->string('priority')->default('medium');
            $table->text('additional_notes')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'status']);
            $table->index(['user_address_id']);
            $table->index(['priority', 'status']);
            $table->index(['preferred_service_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_requests');
    }
};
