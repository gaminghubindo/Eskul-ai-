<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'store_id',
        'external_product_id',
        'category_id',
        'category_name',
        'image_urls',
        'original_title',
        'original_desc',
        'generated_usps',
        'generated_title',
        'generated_desc',
        'seo_keywords',
        'status',
        'error_message',
        'last_synced_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'image_urls' => 'array',
            'generated_usps' => 'array',
            'seo_keywords' => 'array',
            'last_synced_at' => 'datetime',
        ];
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function automationJobs(): HasMany
    {
        return $this->hasMany(AutomationJob::class);
    }
}
