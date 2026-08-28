<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('store_id')->constrained('stores')->cascadeOnDelete();
            $table->string('external_product_id', 100);
            $table->string('category_id', 100);
            $table->string('category_name', 255)->nullable();
            $table->json('image_urls');
            
            // Original marketplace snapshot for rollback
            $table->string('original_title', 255);
            $table->text('original_desc');
            
            // AI generated content
            $table->json('generated_usps')->nullable();
            $table->string('generated_title', 255)->nullable();
            $table->text('generated_desc')->nullable();
            $table->json('seo_keywords')->nullable();
            
            // Status & Tracking
            $table->enum('status', ['pending', 'processing', 'success', 'failed', 'reverted'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();

            $table->unique(['store_id', 'external_product_id']);
            $table->index(['store_id', 'category_id']);
            $table->index(['status', 'store_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
