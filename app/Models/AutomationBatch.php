<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AutomationBatch extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'store_id',
        'automation_type',
        'scope_type',
        'target_product_id',
        'target_name',
        'auto_apply_new',
        'category_id',
        'category_name',
        'total_products',
        'processed_count',
        'success_count',
        'failed_count',
        'status',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'target_product_id');
    }

    public function jobs(): HasMany
    {
        return $this->hasMany(AutomationJob::class, 'batch_id');
    }
}
