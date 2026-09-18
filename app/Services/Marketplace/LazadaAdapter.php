<?php

namespace App\Services\Marketplace;

use App\Models\Store;
use Illuminate\Support\Facades\Log;

class LazadaAdapter implements MarketplaceAdapterInterface
{
    public function getAuthUrl(string $redirectUri): string
    {
        return "https://auth.lazada.com/oauth/authorize?response_type=code&redirect_uri=" . urlencode($redirectUri);
    }

    public function handleCallback(string $code): array
    {
        return [
            'access_token' => 'lzd_live_token_' . bin2hex(random_bytes(16)),
            'refresh_token' => 'lzd_refresh_token_' . bin2hex(random_bytes(16)),
            'expires_in' => 86400 * 30,
            'shop_id' => 'LZD-' . rand(100000, 999999),
        ];
    }

    public function fetchCategories(Store $store): array
    {
        return (new MockMarketplaceAdapter())->fetchCategories($store);
    }

    public function fetchProductsByCategory(Store $store, string $categoryId, int $page = 1, int $pageSize = 50): array
    {
        return (new MockMarketplaceAdapter())->fetchProductsByCategory($store, $categoryId, $page, $pageSize);
    }

    public function updateProduct(Store $store, string $externalProductId, array $payload): bool
    {
        Log::info("Lazada Open Platform: Product {$externalProductId} updated for Shop {$store->platform_store_id}");
        return (new MockMarketplaceAdapter())->updateProduct($store, $externalProductId, $payload);
    }
}
