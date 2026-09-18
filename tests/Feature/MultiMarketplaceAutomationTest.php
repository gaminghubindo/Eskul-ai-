<?php

namespace Tests\Feature;

use App\Models\AutomationBatch;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MultiMarketplaceAutomationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\StoreCatalogSeeder::class);
    }

    public function test_can_fetch_marketplace_schema(): void
    {
        $response = $this->getJson('/api/marketplaces/schema');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'platforms' => [
                    'TOKOPEDIA' => ['name', 'fields'],
                    'SHOPEE' => ['name', 'fields'],
                    'LAZADA' => ['name', 'fields'],
                    'TIKTOK_SHOP' => ['name', 'fields'],
                ],
            ]);
    }

    public function test_can_connect_store_with_dynamic_marketplace_fields(): void
    {
        $payload = [
            'platform' => 'TOKOPEDIA',
            'store_name' => 'Toko Baru Tokopedia Test',
            'shop_id' => '99887766',
            'fs_id' => '12345',
            'client_id' => 'client_tkpd_test_abc',
            'client_secret' => 'secret_tkpd_test_xyz',
        ];

        $response = $this->postJson('/api/stores/connect', $payload);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('stores', [
            'platform' => 'TOKOPEDIA',
            'store_name' => 'Toko Baru Tokopedia Test',
        ]);
    }

    public function test_fails_to_connect_store_if_required_fields_missing(): void
    {
        $payload = [
            'platform' => 'SHOPEE',
            'store_name' => 'Toko Shopee Test',
            // Missing shop_id, partner_id, partner_key, access_token
        ];

        $response = $this->postJson('/api/stores/connect', $payload);

        $response->assertStatus(422)
            ->assertJsonPath('success', false);
    }

    public function test_can_trigger_single_product_automation(): void
    {
        $store = Store::first();
        $product = Product::where('store_id', $store->id)->first();

        $response = $this->postJson("/api/automation/single/{$product->id}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('automation_batches', [
            'store_id' => $store->id,
            'scope_type' => 'single_product',
            'target_product_id' => $product->id,
            'total_products' => 1,
        ]);
    }

    public function test_can_update_product_content_directly(): void
    {
        $product = Product::first();

        $response = $this->postJson("/api/products/{$product->id}/update-content", [
            'generated_title' => 'Judul Produk Baru Hasil Edit',
            'generated_desc' => 'Deskripsi produk baru yang telah disunting.',
            'generated_usps' => "Poin 1\nPoin 2",
            'seo_keywords' => 'keyword 1, keyword 2',
            'sync_to_marketplace' => true,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $product->refresh();
        $this->assertEquals('Judul Produk Baru Hasil Edit', $product->generated_title);
        $this->assertEquals('Deskripsi produk baru yang telah disunting.', $product->generated_desc);
        $this->assertEquals('success', $product->status);
    }

    public function test_can_revert_product_and_allows_reoptimization(): void
    {
        $product = Product::first();
        $product->update([
            'status' => 'success',
            'generated_title' => 'Optimized Title',
        ]);

        $response = $this->postJson("/api/automation/revert/{$product->id}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $product->refresh();
        $this->assertEquals('reverted', $product->status);
        $this->assertNull($product->generated_title);

        // Can re-optimize immediately
        $reoptResponse = $this->postJson("/api/automation/single/{$product->id}");
        $reoptResponse->assertStatus(200);
    }

    public function test_can_cancel_active_batch(): void
    {
        $store = Store::first();
        $batch = AutomationBatch::create([
            'store_id' => $store->id,
            'scope_type' => 'category',
            'category_id' => 'cat_fashion_pria',
            'total_products' => 5,
            'status' => 'processing',
        ]);

        $response = $this->postJson("/api/automation/batches/{$batch->id}/cancel");

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $batch->refresh();
        $this->assertEquals('cancelled', $batch->status);
    }
}
