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
        Schema::create('regional_service_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id')->constrained('regions')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->foreignId('subcategory_id')->constrained('subcategories')->cascadeOnDelete();
            $table->decimal('price', 12, 2)->default(0.00);
            $table->string('currency', 10)->default('USD');
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('active')->index();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['region_id', 'category_id', 'subcategory_id', 'status'], 'idx_reg_cat_sub_stat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regional_service_prices');
    }
};
