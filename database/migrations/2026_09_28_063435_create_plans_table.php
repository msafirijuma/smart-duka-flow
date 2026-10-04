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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');              // Free, Pro, Business
            $table->string('slug')->unique();    // free, pro, business
            $table->string('price_label')->nullable(); // "TZS 0", "TZS 25,000/mo"
            $table->decimal('price', 12, 2)->default(0);
            $table->unsignedInteger('max_products')->nullable(); // null = unlimited
            $table->unsignedInteger('max_staff')->nullable();
            $table->unsignedInteger('max_shops')->nullable(); // per owner account later
            $table->boolean('has_reports')->default(true);
            $table->boolean('has_exports')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
