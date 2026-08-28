<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('platform', 50); // SHOPEE, TOKOPEDIA, TIKTOK_SHOP
            $table->string('platform_store_id', 100);
            $table->string('store_name', 150);
            
            // Encrypted at rest
            $table->text('store_access_token');
            $table->text('store_refresh_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['platform', 'platform_store_id']);
            $table->index(['user_id', 'platform']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
