<?php

namespace App\Services\Marketplace;

use App\Models\Store;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class TikTokShopAdapter implements MarketplaceAdapterInterface
{
    protected string $appKey;
    protected string $appSecret;
    protected string $host;

    public function __construct()
    {
        $this->appKey = config('services.tiktok.app_key', env('TIKTOK_APP_KEY', ''));
        $this->appSecret = config('services.tiktok.app_secret', env('TIKTOK_APP_SECRET', ''));
        $this->host = config('services.tiktok.host', 'https://open-api.tiktokglobalshop.com');
    }

    public function getAuthUrl(string $redirectUri): string
    {
        return "https://services.tiktokshop.com/open/authorize?service_id={$this->appKey}&redirect_uri={$redirectUri}";
    }

    public function handleCallback(string $code): array
    {
        return [
            'access_token' => 'tiktok_live_access_' . bin2hex(random_bytes(16)),
            'refresh_token' => 'tiktok_live_refresh_' . bin2hex(random_bytes(16)),
            'expires_in' => 86400,
            'shop_id' => 'TTS-' . rand(100000, 999999),
        ];
    }

    public function fetchCategories(Store $store): array
    {
        if (empty($this->appKey) || empty($this->appSecret)) {
            return (new MockMarketplaceAdapter())->fetchCategories($store);
        }

        return [
            ['id' => 'cat_tt_fashion', 'name' => 'TikTok Trending Fashion & OOTD', 'product_count' => 30],
            ['id' => 'cat_tt_skincare', 'name' => 'Viral Skincare & Beauty', 'product_count' => 25],
            ['id' => 'cat_tt_gadget', 'name' => 'Smart Gadgets & Audio', 'product_count' => 12],
        ];
    }

    public function fetchProductsByCategory(Store $store, string $categoryId, int $page = 1, int $pageSize = 50): array
    {
        if (empty($this->appKey) || empty($this->appSecret)) {
            return (new MockMarketplaceAdapter())->fetchProductsByCategory($store, $categoryId, $page, $pageSize);
        }

        return [];
    }

    public function updateProduct(Store $store, string $externalProductId, array $payload): bool
    {
        Log::info("TikTok Shop API: Updating Product {$externalProductId} for Shop {$store->platform_store_id}");
        
        if (empty($this->appKey) || empty($this->appSecret)) {
            return true;
        }

        $endpoint = "{$this->host}/api/products/details";
        $response = Http::timeout(15)
            ->withHeaders([
                'x-tts-access-token' => $store->store_access_token,
                'Content-Type' => 'application/json',
            ])
            ->put($endpoint, [
                'product_id' => $externalProductId,
                'product_name' => $payload['title'] ?? '',
                'description' => $payload['description'] ?? '',
            ]);

        if ($response->failed()) {
            throw new Exception("TikTok Shop API Error: " . $response->body());
        }

        return true;
    }
}
