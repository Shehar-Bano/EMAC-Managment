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
        Schema::table('users', function (Blueprint $table) {
            $table->string('address')->nullable()->after('phone');
            $table->string('role')->default('customer')->index()->after('address');
            $table->string('source')->default('email')->index()->after('role');
            $table->string('account_status')->default('pending')->index()->after('source');
            $table->string('profile_status')->default('incomplete')->index()->after('account_status');
            $table->timestamp('phone_verified_at')->nullable()->after('email_verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'address',
                'role',
                'source',
                'account_status',
                'profile_status',
                'phone_verified_at',
            ]);
        });
    }
};
