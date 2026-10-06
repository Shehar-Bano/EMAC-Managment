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
        Schema::create('quote_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->constrained('quotes')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->comment('Sender user ID');
            $table->string('sender_type', 20)->default('customer')->comment('customer or admin');
            $table->text('message');
            $table->json('attachments')->nullable();
            $table->boolean('is_internal')->default(false);
            $table->timestamps();

            $table->index(['quote_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quote_messages');
    }
};
