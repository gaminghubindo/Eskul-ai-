<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('automation_batches', function (Blueprint $table) {
            if (!Schema::hasColumn('automation_batches', 'automation_type')) {
                $table->string('automation_type', 50)->default('ai_optimization')->after('store_id');
            }
            if (!Schema::hasColumn('automation_batches', 'scope_type')) {
                $table->string('scope_type', 30)->default('category')->after('automation_type');
            }
            if (!Schema::hasColumn('automation_batches', 'target_product_id')) {
                $table->uuid('target_product_id')->nullable()->after('scope_type');
            }
            if (!Schema::hasColumn('automation_batches', 'target_name')) {
                $table->string('target_name', 255)->nullable()->after('target_product_id');
            }
            if (!Schema::hasColumn('automation_batches', 'auto_apply_new')) {
                $table->boolean('auto_apply_new')->default(false)->after('target_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('automation_batches', function (Blueprint $table) {
            $columns = ['automation_type', 'scope_type', 'target_product_id', 'target_name', 'auto_apply_new'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('automation_batches', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
