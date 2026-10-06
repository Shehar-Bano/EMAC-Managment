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
        Schema::table('service_requests', function (Blueprint $table) {
            if (! Schema::hasColumn('service_requests', 'type')) {
                $table->string('type', 20)->default('app')->after('status')->index();
            }
            $table->text('property_information')->nullable()->change();
            $table->date('preferred_service_date')->nullable()->change();
            $table->string('preferred_service_time')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            if (Schema::hasColumn('service_requests', 'type')) {
                $table->dropIndex(['type']);
                $table->dropColumn('type');
            }
        });
    }
};
