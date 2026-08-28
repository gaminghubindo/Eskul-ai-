<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Store;
use App\Services\Marketplace\MockMarketplaceAdapter;
use Illuminate\Database\Seeder;

class StoreCatalogSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Shopee Official Demo Store
        $shopeeStore = Store::firstOrCreate(
            ['platform' => 'SHOPEE', 'platform_store_id' => 'OFFICIAL-8899'],
            [
                'store_name' => 'Bintang Mode Official (Shopee)',
                'store_access_token' => 'shopee_sec_auth_' . bin2hex(random_bytes(16)),
                'store_refresh_token' => 'shopee_ref_auth_' . bin2hex(random_bytes(16)),
                'token_expires_at' => now()->addDays(60),
                'is_active' => true,
            ]
        );

        // 2. Create TikTok Shop Trending Store
        $tiktokStore = Store::firstOrCreate(
            ['platform' => 'TIKTOK_SHOP', 'platform_store_id' => 'OFFICIAL-4422'],
            [
                'store_name' => 'Glow & Tech Viral (TikTok Shop)',
                'store_access_token' => 'tiktok_sec_auth_' . bin2hex(random_bytes(16)),
                'store_refresh_token' => 'tiktok_ref_auth_' . bin2hex(random_bytes(16)),
                'token_expires_at' => now()->addDays(60),
                'is_active' => true,
            ]
        );

        // 3. Populate products from Mock catalog
        $adapter = new MockMarketplaceAdapter();
        $catalog = $adapter->getMockProductCatalog();

        foreach ($catalog as $index => $item) {
            // Assign fashion & shoes to Shopee, electronics & beauty to TikTok
            $targetStore = in_array($item['category_id'], ['cat_fashion_pria', 'cat_sepatu']) ? $shopeeStore : $tiktokStore;

            // Pre-optimize first 2 items to give immediate Before-After Diff previews
            $isPreOptimized = ($index === 0 || $index === 6);

            Product::updateOrCreate(
                [
                    'store_id' => $targetStore->id,
                    'external_product_id' => $item['external_product_id'],
                ],
                [
                    'category_id' => $item['category_id'],
                    'category_name' => $item['category_name'],
                    'image_urls' => $item['image_urls'],
                    'original_title' => $item['original_title'],
                    'original_desc' => $item['original_desc'],
                    'generated_title' => $isPreOptimized 
                        ? "Kemeja Pria Lengan Panjang Katun Oxford Slim Fit Premium - Navy Modern" 
                        : null,
                    'generated_desc' => $isPreOptimized 
                        ? "✨ Kemeja Oxford Pria Lengan Panjang Premium ✨\n\nTampil percaya diri dan profesional di setiap momen dengan Kemeja Katun Oxford Slim Fit. Dibuat dengan konstruksi bahan katun oxford bergramasi ideal yang adem, halus, dan tidak mudah kusut.\n\n📌 KEUNGGULAN UTAMA:\n• Bahan 100% Katun Oxford premium bertekstur rapat dan bernapas\n• Pola potongan Slim Fit ergonomis yang pas membentuk postur tubuh\n• Kerah kancing tegas & jahitan ganda kokoh standar butik\n• Warna Navy pekat elegan tidak mudah pudar dicuci berulang\n\n📦 DETAIL PRODUK:\n• Material: Premium Cotton Oxford\n• Model: Lengan Panjang Kancing Manset\n• Varian Ukuran: M, L, XL, XXL\n• Garansi tukar size jika tidak pas!" 
                        : null,
                    'generated_usps' => $isPreOptimized 
                        ? ["Bahan katun oxford premium adem & tebal", "Potongan slim fit presisi & rapi", "Warna navy pekat tahan luntur", "Kerah berkancing formal & casual"] 
                        : null,
                    'seo_keywords' => $isPreOptimized 
                        ? ["kemeja pria", "kemeja oxford", "kemeja navy", "kemeja kerja pria", "baju formal pria", "kemeja slim fit"] 
                        : null,
                    'status' => $isPreOptimized ? 'success' : 'pending',
                    'last_synced_at' => $isPreOptimized ? now()->subHours(2) : null,
                ]
            );
        }
    }
}
