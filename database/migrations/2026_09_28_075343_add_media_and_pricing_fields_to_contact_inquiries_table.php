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
        Schema::table('contact_inquiries', function (Blueprint $table) {
            $table->foreignId('region_id')->nullable()->after('market')->constrained('regions')->nullOnDelete();
            $table->foreignId('subcategory_id')->nullable()->after('category_id')->constrained('subcategories')->nullOnDelete();
            $table->decimal('estimated_price', 12, 2)->nullable()->after('subcategory_id');
            $table->string('currency', 10)->nullable()->after('estimated_price');
            $table->json('photographs')->nullable()->after('message');
            $table->string('video')->nullable()->after('photographs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_inquiries', function (Blueprint $table) {
            $table->dropForeign(['region_id']);
            $table->dropForeign(['subcategory_id']);
            $table->dropColumn(['region_id', 'subcategory_id', 'estimated_price', 'currency', 'photographs', 'video']);
        });
    }
};
