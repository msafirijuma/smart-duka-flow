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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('shop_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 80);          // shop.suspended, plan.changed
            $table->string('description');
            $table->string('subject_type')->nullable(); // App\Models\Shop
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->json('properties')->nullable();     // old/new values
            $table->string('ip', 45)->nullable();
            $table->timestamps();

            $table->index(['shop_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index('action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
