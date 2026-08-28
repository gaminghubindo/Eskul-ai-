<?php

namespace App\Services\Marketplace;

use App\Models\Store;

interface MarketplaceAdapterInterface
{
    /**
     * Get OAuth authorization URL for the seller.
     */
    public function getAuthUrl(string $redirectUri): string;

    /**
     * Handle OAuth token exchange.
     */
    public function handleCallback(string $code): array;

    /**
     * Fetch categories available in the connected store.
     */
    public function fetchCategories(Store $store): array;

    /**
     * Fetch products by category with pagination.
     */
    public function fetchProductsByCategory(Store $store, string $categoryId, int $page = 1, int $pageSize = 50): array;

    /**
     * Update product title and description in the marketplace.
     */
    public function updateProduct(Store $store, string $externalProductId, array $payload): bool;
}
