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
        // 1. Normalize existing database records to 'normal' or 'emergency'
        DB::table('service_requests')
            ->whereNotIn('priority', ['emergency'])
            ->orWhereNull('priority')
            ->update(['priority' => 'normal']);

        // 2. Update default value on priority column
        Schema::table('service_requests', function (Blueprint $table) {
            $table->string('priority')->default('normal')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->string('priority')->default('medium')->change();
        });
    }
};
