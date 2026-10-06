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
            $table->foreignId('category_id')->nullable()->after('role')->constrained('categories')->nullOnDelete();
            $table->string('duty_status')->default('on_duty')->nullable()->after('category_id');
            $table->unsignedSmallInteger('experience_years')->nullable()->after('duty_status');
            $table->text('bio')->nullable()->after('experience_years');
            $table->string('emergency_contact_name')->nullable()->after('bio');
            $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name');
            $table->string('certification_id')->nullable()->after('emergency_contact_phone');
            $table->string('certification_body')->nullable()->after('certification_id');
            $table->boolean('is_verified')->default(true)->after('certification_body');
            $table->timestamp('verified_at')->nullable()->after('is_verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('category_id');
            $table->dropColumn([
                'duty_status',
                'experience_years',
                'bio',
                'emergency_contact_name',
                'emergency_contact_phone',
                'certification_id',
                'certification_body',
                'is_verified',
                'verified_at',
            ]);
        });
    }
};
