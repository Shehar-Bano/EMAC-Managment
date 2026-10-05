<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Normalize existing database records to 'pending', 'active', 'completed'
        DB::table('service_requests')
            ->whereIn('status', ['quotesent', 'in_progress', 'processing', 'quote_sent', 'quoted', 'accepted'])
            ->update(['status' => 'active']);

        DB::table('service_requests')
            ->whereIn('status', ['reject', 'rejected', 'cancelled', 'canceled'])
            ->update(['status' => 'pending']);

        DB::table('service_requests')
            ->whereNotIn('status', ['pending', 'active', 'completed'])
            ->orWhereNull('status')
            ->update(['status' => 'pending']);

        // 2. Ensure default value on status column is 'pending'
        Schema::table('service_requests', function (Blueprint $table) {
            $table->string('status')->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->string('status')->default('pending')->change();
        });
    }
};
