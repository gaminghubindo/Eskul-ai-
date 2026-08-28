<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'platform',
        'platform_store_id',
        'store_name',
        'store_access_token',
        'store_refresh_token',
        'token_expires_at',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'store_access_token' => 'encrypted',
            'store_refresh_token' => 'encrypted',
            'token_expires_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function automationBatches(): HasMany
    {
        return $this->hasMany(AutomationBatch::class);
    }

    public function automationJobs(): HasMany
    {
        return $this->hasMany(AutomationJob::class);
    }
}
