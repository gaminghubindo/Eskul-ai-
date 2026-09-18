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
                'credentials' => [
                    'store_name' => 'Bintang Mode Official (Shopee)',
                    'shop_id' => '8899120',
                    'partner_id' => '2004567',
                    'partner_key' => 'shp_key_demo_66778899',
                    'access_token' => 'shp_live_act_8899aabbccddeeff',
                ],
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
                'credentials' => [
                    'store_name' => 'Glow & Tech Viral (TikTok Shop)',
                    'shop_cipher' => 'GCPO_X92K81M_DEMO',
                    'app_key' => '6ab89c2demo',
                    'app_secret' => 'tt_sec_demo_992244',
                    'access_token' => 'ttp_act_demo_live_token_77',
                ],
                'token_expires_at' => now()->addDays(60),
                'is_active' => true,
            ]
        );

        // 3. Create Tokopedia Official Store
        $tokopediaStore = Store::firstOrCreate(
            ['platform' => 'TOKOPEDIA', 'platform_store_id' => 'OFFICIAL-1928'],
            [
                'store_name' => 'Berkah Gadget Official (Tokopedia)',
                'store_access_token' => 'tkpd_sec_auth_' . bin2hex(random_bytes(16)),
                'store_refresh_token' => 'tkpd_ref_auth_' . bin2hex(random_bytes(16)),
                'credentials' => [
                    'store_name' => 'Berkah Gadget Official (Tokopedia)',
                    'shop_id' => '19283746',
                    'fs_id' => '15520',
                    'client_id' => 'tkpd_client_test_7788',
                    'client_secret' => 'tkpd_sec_sandbox_9922aa88bb',
                ],
                'token_expires_at' => now()->addDays(60),
                'is_active' => true,
            ]
        );

        // 4. Create Lazada Official Store
        $lazadaStore = Store::firstOrCreate(
            ['platform' => 'LAZADA', 'platform_store_id' => 'OFFICIAL-5533'],
            [
                'store_name' => 'Metro Living Official (Lazada)',
                'store_access_token' => 'lzd_sec_auth_' . bin2hex(random_bytes(16)),
                'store_refresh_token' => 'lzd_ref_auth_' . bin2hex(random_bytes(16)),
                'credentials' => [
                    'store_name' => 'Metro Living Official (Lazada)',
                    'seller_id' => 'ID_LZD_99182',
                    'app_key' => '108291',
                    'app_secret' => 'lzd_sec_test_55443322',
                    'access_token' => '50000201a08b99cc77dd88ee11',
                ],
                'token_expires_at' => now()->addDays(60),
                'is_active' => true,
            ]
        );

        // 5. Populate products from Mock catalog across stores
        $adapter = new MockMarketplaceAdapter();
        $catalog = $adapter->getMockProductCatalog();

        foreach ($catalog as $index => $item) {
            $targetStore = match ($item['category_id']) {
                'cat_fashion_pria' => $shopeeStore,
                'cat_sepatu' => $shopeeStore,
                'cat_elektronik' => $tokopediaStore,
                'cat_kecantikan' => $tiktokStore,
                default => $lazadaStore,
            };

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
