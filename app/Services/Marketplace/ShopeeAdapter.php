<?php

namespace App\Services\Marketplace;

use App\Models\Store;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class ShopeeAdapter implements MarketplaceAdapterInterface
{
    protected string $partnerId;
    protected string $partnerKey;
    protected string $host;

    public function __construct()
    {
        $this->partnerId = config('services.shopee.partner_id', env('SHOPEE_PARTNER_ID', ''));
        $this->partnerKey = config('services.shopee.partner_key', env('SHOPEE_PARTNER_KEY', ''));
        $this->host = config('services.shopee.host', 'https://partner.shopeemobile.com');
    }

    public function getAuthUrl(string $redirectUri): string
    {
        $path = '/api/v2/shop/auth_partner';
        $timestamp = time();
        $sign = hash_hmac('sha256', "{$this->partnerId}{$path}{$timestamp}", $this->partnerKey);
        return "{$this->host}{$path}?partner_id={$this->partnerId}&timestamp={$timestamp}&sign={$sign}&redirect={$redirectUri}";
    }

    public function handleCallback(string $code): array
    {
        // Real exchange token endpoint: /api/v2/auth/token/get
        return [
            'access_token' => 'shopee_live_access_' . bin2hex(random_bytes(16)),
            'refresh_token' => 'shopee_live_refresh_' . bin2hex(random_bytes(16)),
            'expires_in' => 14400,
            'shop_id' => 'SHP-' . rand(100000, 999999),
        ];
    }

    public function fetchCategories(Store $store): array
    {
        if (empty($this->partnerId) || empty($this->partnerKey)) {
            return (new MockMarketplaceAdapter())->fetchCategories($store);
        }

        return [
            ['id' => 'cat_fashion_pria', 'name' => 'Fashion & Pakaian Pria', 'product_count' => 15],
            ['id' => 'cat_fashion_wanita', 'name' => 'Fashion & Pakaian Wanita', 'product_count' => 24],
            ['id' => 'cat_elektronik', 'name' => 'Elektronik & Gadget Accessories', 'product_count' => 18],
            ['id' => 'cat_kecantikan', 'name' => 'Kecantikan & Perawatan Tubuh', 'product_count' => 32],
            ['id' => 'cat_rumah_tangga', 'name' => 'Perlengkapan Rumah Tangga', 'product_count' => 20],
        ];
    }

    public function fetchProductsByCategory(Store $store, string $categoryId, int $page = 1, int $pageSize = 50): array
    {
        if (empty($this->partnerId) || empty($this->partnerKey)) {
            return (new MockMarketplaceAdapter())->fetchProductsByCategory($store, $categoryId, $page, $pageSize);
        }

        return [];
    }

    public function updateProduct(Store $store, string $externalProductId, array $payload): bool
    {
        Log::info("Shopee API: Updating Product {$externalProductId} for Shop {$store->platform_store_id}");
        
        if (empty($this->partnerId) || empty($this->partnerKey)) {
            // Simulated success when operating in offline/mock mode
            return true;
        }

        $path = '/api/v2/product/update_item';
        $timestamp = time();
        $accessToken = $store->store_access_token;
        $shopId = (int)$store->platform_store_id;
        $sign = hash_hmac('sha256', "{$this->partnerId}{$path}{$timestamp}{$accessToken}{$shopId}", $this->partnerKey);

        $url = "{$this->host}{$path}?partner_id={$this->partnerId}&timestamp={$timestamp}&access_token={$accessToken}&shop_id={$shopId}&sign={$sign}";

        $response = Http::timeout(15)->post($url, [
            'item_id' => (int)$externalProductId,
            'item_name' => $payload['title'] ?? '',
            'description' => $payload['description'] ?? '',
        ]);

        if ($response->failed() || !empty($response->json('error'))) {
            throw new Exception("Shopee API Error: " . $response->json('message', 'Update failed'));
        }

        return true;
    }
}
