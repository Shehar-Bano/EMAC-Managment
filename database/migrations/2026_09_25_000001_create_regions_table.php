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
        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('code', 10)->nullable()->comment('ISO or short code e.g. GC, FL, JM');
            $table->string('currency', 10)->default('USD');
            $table->text('description')->nullable();
            $table->string('status', 20)->default('active')->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->softDeletes();
            $table->timestamps();

            $table->index(['status', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regions');
    }
};
