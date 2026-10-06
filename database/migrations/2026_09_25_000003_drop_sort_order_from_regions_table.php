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
        Schema::table('regions', function (Blueprint $table) {
            if (Schema::hasColumn('regions', 'sort_order')) {
                $table->dropIndex(['status', 'sort_order']);
                $table->dropColumn('sort_order');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('regions', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')->default(0)->after('status');
            $table->index(['status', 'sort_order']);
        });
    }
};
