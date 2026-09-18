<?php

namespace App\Services\Marketplace;

use App\Models\Store;
use InvalidArgumentException;

class MarketplaceManager
{
    public function getAdapter(Store $store): MarketplaceAdapterInterface
    {
        // If the store is designated as MOCK or sandbox credentials
        if (str_starts_with($store->platform_store_id, 'OFFICIAL-') || str_starts_with($store->platform_store_id, 'MOCK-')) {
            return new MockMarketplaceAdapter();
        }

        return match ($store->platform) {
            'SHOPEE' => new ShopeeAdapter(),
            'TIKTOK_SHOP' => new TikTokShopAdapter(),
            'TOKOPEDIA' => new TokopediaAdapter(),
            'LAZADA' => new LazadaAdapter(),
            default => new MockMarketplaceAdapter(),
        };
    }
}
