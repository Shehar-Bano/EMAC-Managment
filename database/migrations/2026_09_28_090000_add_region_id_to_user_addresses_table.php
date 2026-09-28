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
        Schema::table('user_addresses', function (Blueprint $table) {
            $table->foreignId('region_id')
                ->nullable()
                ->after('user_id')
                ->constrained('regions')
                ->nullOnDelete();

            $table->index(['user_id', 'region_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_addresses', function (Blueprint $table) {
            $table->dropForeign(['region_id']);
            $table->dropIndex(['user_id', 'region_id']);
            $table->dropColumn('region_id');
        });
    }
};
