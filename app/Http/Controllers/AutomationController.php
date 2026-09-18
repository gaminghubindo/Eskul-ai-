<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessProductOptimizationJob;
use App\Models\AutomationBatch;
use App\Models\AutomationJob;
use App\Models\Product;
use App\Models\Store;
use App\Services\Marketplace\MarketplaceManager;
use App\Services\Marketplace\MockMarketplaceAdapter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AutomationController extends Controller
{
    public function __construct(
        protected MarketplaceManager $marketplaceManager
    ) {}

    /**
     * Get Marketplace Platform Schema & Configuration
     */
    public function getMarketplaceSchema(): JsonResponse
    {
        $platforms = config('marketplaces.platforms', []);
        return response()->json([
            'success' => true,
            'platforms' => $platforms,
        ]);
    }

    /**
     * List all stores with metadata
     */
    public function listStores(): JsonResponse
    {
        $stores = Store::withCount('products')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($store) {
                $platformConfig = config("marketplaces.platforms.{$store->platform}", []);
                return [
                    'id' => $store->id,
                    'store_name' => $store->store_name,
                    'platform' => $store->platform,
                    'platform_name' => $platformConfig['name'] ?? $store->platform,
                    'platform_color' => $platformConfig['color'] ?? '#FF4D00',
                    'platform_store_id' => $store->platform_store_id,
                    'products_count' => $store->products_count,
                    'is_active' => $store->is_active,
                    'token_expires_at' => $store->token_expires_at?->format('d M Y'),
                    'created_at' => $store->created_at->format('d M Y, H:i'),
                ];
            });

        return response()->json([
            'success' => true,
            'stores' => $stores,
        ]);
    }

    /**
     * Connect or Update Store with Dynamic per-Marketplace Schema Validation
     */
    public function connectStore(Request $request): JsonResponse
    {
        $platforms = config('marketplaces.platforms', []);
        $allowedPlatforms = implode(',', array_keys($platforms));

        $baseValidator = Validator::make($request->all(), [
            'platform' => "required|in:{$allowedPlatforms}",
        ]);

        if ($baseValidator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Platform marketplace tidak valid.',
                'errors' => $baseValidator->errors(),
            ], 422);
        }

        $platform = $request->input('platform');
        $platformConfig = $platforms[$platform];

        // Build dynamic validation rules based on platform config
        $rules = [];
        $credentials = [];

        foreach ($platformConfig['fields'] as $field) {
            $fieldName = $field['name'];
            $rules[$fieldName] = $field['validation'] ?? ($field['required'] ? 'required' : 'nullable');
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Harap periksa kembali isian formulir sesuai API resmi ' . $platformConfig['name'],
                'errors' => $validator->errors(),
            ], 422);
        }

        // Collect credentials
        foreach ($platformConfig['fields'] as $field) {
            $fieldName = $field['name'];
            $credentials[$fieldName] = $request->input($fieldName);
        }

        // Determine platform store identifier
        $storeName = $request->input('store_name');
        $platformStoreId = match ($platform) {
            'TOKOPEDIA' => (string) ($request->input('shop_id') ?? 'TKPD-' . rand(10000, 99999)),
            'SHOPEE' => (string) ($request->input('shop_id') ?? 'SHP-' . rand(10000, 99999)),
            'LAZADA' => (string) ($request->input('seller_id') ?? 'LZD-' . rand(10000, 99999)),
            'TIKTOK_SHOP' => (string) ($request->input('shop_cipher') ?? 'TT-' . rand(10000, 99999)),
            default => (string) ($request->input('platform_store_id') ?? 'STORE-' . rand(1000, 9999)),
        };

        $store = Store::updateOrCreate(
            [
                'platform' => $platform,
                'platform_store_id' => $platformStoreId,
            ],
            [
                'store_name' => $storeName,
                'credentials' => $credentials,
                'store_access_token' => $request->input('access_token', 'token_' . bin2hex(random_bytes(16))),
                'store_refresh_token' => $request->input('refresh_token', 'ref_' . bin2hex(random_bytes(16))),
                'token_expires_at' => now()->addDays(60),
                'is_active' => true,
            ]
        );

        // Seed initial mock products if newly connected store has no products
        if ($store->products()->count() === 0) {
            $mockAdapter = new MockMarketplaceAdapter();
            $catalog = $mockAdapter->getMockProductCatalog();
            // Take 6-8 catalog items for variety
            $sampleItems = array_slice($catalog, 0, 8);
            foreach ($sampleItems as $idx => $item) {
                Product::create([
                    'store_id' => $store->id,
                    'external_product_id' => 'EXT-' . strtoupper(substr($platform, 0, 3)) . '-' . sprintf('%03d', $idx + 1),
                    'category_id' => $item['category_id'],
                    'category_name' => $item['category_name'],
                    'image_urls' => $item['image_urls'],
                    'original_title' => $item['original_title'],
                    'original_desc' => $item['original_desc'],
                    'status' => 'pending',
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Toko '{$store->store_name}' ({$platformConfig['name']}) berhasil terhubung dengan aman!",
            'store' => [
                'id' => $store->id,
                'store_name' => $store->store_name,
                'platform' => $store->platform,
                'platform_store_id' => $store->platform_store_id,
            ],
        ]);
    }

    /**
     * Disconnect / Deactivate Store
     */
    public function disconnectStore(string $storeId): JsonResponse
    {
        $store = Store::findOrFail($storeId);
        $name = $store->store_name;
        $store->delete();

        return response()->json([
            'success' => true,
            'message' => "Toko '{$name}' berhasil diputuskan dan dihapus.",
        ]);
    }

    /**
     * Preview Automation Scope (Single Product vs Category)
     */
    public function previewAutomationScope(Request $request): JsonResponse
    {
        $request->validate([
            'store_id' => 'required|uuid|exists:stores,id',
            'scope_type' => 'required|in:category,single_product',
            'target_id' => 'required|string',
        ]);

        $store = Store::findOrFail($request->store_id);

        if ($request->scope_type === 'single_product') {
            $product = Product::where('store_id', $store->id)->where('id', $request->target_id)->first();
            if (!$product) {
                return response()->json(['success' => false, 'message' => 'Produk tidak ditemukan.'], 404);
            }

            return response()->json([
                'success' => true,
                'scope_type' => 'single_product',
                'count' => 1,
                'target_name' => $product->original_title,
                'sample_products' => [[
                    'id' => $product->id,
                    'title' => $product->original_title,
                    'image' => is_array($product->image_urls) ? ($product->image_urls[0] ?? null) : null,
                    'status' => $product->status,
                ]],
            ]);
        }

        // Category Scope
        $products = Product::where('store_id', $store->id)
            ->where('category_id', $request->target_id)
            ->get();

        $categoryName = $products->first()?->category_name;
        if (!$categoryName) {
            $adapter = $this->marketplaceManager->getAdapter($store);
            $categories = $adapter->fetchCategories($store);
            $catMatch = collect($categories)->firstWhere('id', $request->target_id);
            $categoryName = $catMatch['name'] ?? $request->target_id;
        }

        return response()->json([
            'success' => true,
            'scope_type' => 'category',
            'count' => $products->count(),
            'target_name' => $categoryName,
            'sample_products' => $products->take(4)->map(fn($p) => [
                'id' => $p->id,
                'title' => $p->original_title,
                'image' => is_array($p->image_urls) ? ($p->image_urls[0] ?? null) : null,
                'status' => $p->status,
            ]),
        ]);
    }

    /**
     * Start Automation (Supports Single Product and Category Scopes)
     */
    public function startAutomation(Request $request): JsonResponse
    {
        $request->validate([
            'store_id' => 'required|uuid|exists:stores,id',
            'automation_type' => 'required|in:ai_optimization,stock_sync,price_optimization',
            'scope_type' => 'required|in:category,single_product',
            'category_id' => 'required_if:scope_type,category|nullable|string',
            'product_id' => 'required_if:scope_type,single_product|nullable|uuid',
            'auto_apply_new' => 'nullable|boolean',
        ]);

        $store = Store::findOrFail($request->store_id);
        $automationType = $request->automation_type;
        $scopeType = $request->scope_type;

        // SCENARIO 1: Single Product
        if ($scopeType === 'single_product') {
            $product = Product::where('store_id', $store->id)->findOrFail($request->product_id);

            $batch = AutomationBatch::create([
                'store_id' => $store->id,
                'automation_type' => $automationType,
                'scope_type' => 'single_product',
                'target_product_id' => $product->id,
                'target_name' => $product->original_title,
                'category_id' => $product->category_id ?? 'single_item',
                'category_name' => $product->category_name ?? 'Produk Tunggal',
                'total_products' => 1,
                'processed_count' => 0,
                'success_count' => 0,
                'failed_count' => 0,
                'status' => 'processing',
            ]);

            $product->update([
                'status' => 'pending',
                'error_message' => null,
            ]);

            $jobRecord = AutomationJob::create([
                'batch_id' => $batch->id,
                'store_id' => $store->id,
                'product_id' => $product->id,
                'status' => 'pending',
            ]);

            ProcessProductOptimizationJob::dispatch(
                $jobRecord->id,
                $product->id,
                $batch->id
            );

            return response()->json([
                'success' => true,
                'batch_id' => $batch->id,
                'scope_type' => 'single_product',
                'total_products' => 1,
                'target_name' => $product->original_title,
                'message' => "Otomasi dimulai untuk produk: {$product->original_title}",
            ]);
        }

        // SCENARIO 2: Category Scope
        $adapter = $this->marketplaceManager->getAdapter($store);

        // Fetch products: check local DB first, fallback to adapter
        $localProducts = Product::where('store_id', $store->id)
            ->where('category_id', $request->category_id)
            ->get();

        if ($localProducts->isNotEmpty()) {
            $productsToProcess = $localProducts;
            $categoryName = $localProducts->first()->category_name ?? 'Kategori Terpilih';
        } else {
            $rawProducts = $adapter->fetchProductsByCategory($store, $request->category_id);
            if (empty($rawProducts)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada produk ditemukan pada kategori ini di marketplace.',
                ], 404);
            }

            $categoryName = $rawProducts[0]['category_name'] ?? 'Kategori Terpilih';
            $productsToProcess = collect();

            foreach ($rawProducts as $item) {
                $p = Product::updateOrCreate(
                    [
                        'store_id' => $store->id,
                        'external_product_id' => $item['external_product_id'],
                    ],
                    [
                        'category_id' => $item['category_id'],
                        'category_name' => $item['category_name'],
                        'image_urls' => $item['image_urls'],
                        'original_title' => $item['original_title'],
                        'original_desc' => $item['original_desc'],
                        'status' => 'pending',
                    ]
                );
                $productsToProcess->push($p);
            }
        }

        $batch = AutomationBatch::create([
            'store_id' => $store->id,
            'automation_type' => $automationType,
            'scope_type' => 'category',
            'target_name' => $categoryName,
            'category_id' => $request->category_id,
            'category_name' => $categoryName,
            'auto_apply_new' => (bool) $request->input('auto_apply_new', false),
            'total_products' => $productsToProcess->count(),
            'processed_count' => 0,
            'success_count' => 0,
            'failed_count' => 0,
            'status' => 'processing',
        ]);

        foreach ($productsToProcess as $product) {
            $product->update([
                'status' => 'pending',
                'error_message' => null,
            ]);

            $jobRecord = AutomationJob::create([
                'batch_id' => $batch->id,
                'store_id' => $store->id,
                'product_id' => $product->id,
                'status' => 'pending',
            ]);

            ProcessProductOptimizationJob::dispatch(
                $jobRecord->id,
                $product->id,
                $batch->id
            );
        }

        return response()->json([
            'success' => true,
            'batch_id' => $batch->id,
            'scope_type' => 'category',
            'total_products' => $productsToProcess->count(),
            'category_name' => $categoryName,
            'message' => "Berhasil memulai otomasi untuk {$productsToProcess->count()} produk dalam kategori {$categoryName}.",
        ]);
    }

    /**
     * Direct Single Product AI Optimization from Product Card
     */
    public function optimizeSingleProduct(string $productId): JsonResponse
    {
        $product = Product::with('store')->findOrFail($productId);
        $store = $product->store;

        $batch = AutomationBatch::create([
            'store_id' => $store->id,
            'automation_type' => 'ai_optimization',
            'scope_type' => 'single_product',
            'target_product_id' => $product->id,
            'target_name' => $product->original_title,
            'category_id' => $product->category_id ?? 'single_item',
            'category_name' => $product->category_name ?? 'Produk Tunggal',
            'total_products' => 1,
            'processed_count' => 0,
            'success_count' => 0,
            'failed_count' => 0,
            'status' => 'processing',
        ]);

        $product->update([
            'status' => 'pending',
            'error_message' => null,
        ]);

        $jobRecord = AutomationJob::create([
            'batch_id' => $batch->id,
            'store_id' => $store->id,
            'product_id' => $product->id,
            'status' => 'pending',
        ]);

        ProcessProductOptimizationJob::dispatch(
            $jobRecord->id,
            $product->id,
            $batch->id
        );

        return response()->json([
            'success' => true,
            'batch_id' => $batch->id,
            'message' => "Otomasi AI produk '{$product->original_title}' berhasil dimulai!",
        ]);
    }

    /**
     * Update Product Content Directly (Edit Title & Description per Product)
     */
    public function updateProductContent(Request $request, string $productId): JsonResponse
    {
        $request->validate([
            'generated_title' => 'required|string|max:255',
            'generated_desc' => 'required|string',
            'generated_usps' => 'nullable',
            'seo_keywords' => 'nullable',
            'sync_to_marketplace' => 'nullable|boolean',
        ]);

        $product = Product::with('store')->findOrFail($productId);

        $usps = $request->input('generated_usps');
        if (is_string($usps)) {
            $usps = array_filter(array_map('trim', explode("\n", $usps)));
        }

        $keywords = $request->input('seo_keywords');
        if (is_string($keywords)) {
            $keywords = array_filter(array_map('trim', explode(',', $keywords)));
        }

        $product->update([
            'generated_title' => $request->generated_title,
            'generated_desc' => $request->generated_desc,
            'generated_usps' => $usps ?: $product->generated_usps,
            'seo_keywords' => $keywords ?: $product->seo_keywords,
            'status' => 'success',
            'error_message' => null,
            'last_synced_at' => now(),
        ]);

        // Sync to Marketplace Adapter
        if ($request->input('sync_to_marketplace', true)) {
            $adapter = $this->marketplaceManager->getAdapter($product->store);
            $adapter->updateProduct($product->store, $product->external_product_id, [
                'title' => $product->generated_title,
                'description' => $product->generated_desc,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Judul dan deskripsi produk berhasil diperbarui dan disinkronkan!',
            'product' => [
                'id' => $product->id,
                'title' => $product->generated_title,
                'desc' => $product->generated_desc,
                'status' => $product->status,
                'last_synced_at' => $product->last_synced_at->diffForHumans(),
            ],
        ]);
    }

    /**
     * Cancel an ongoing Automation Batch
     */
    public function cancelBatch(string $batchId): JsonResponse
    {
        $batch = AutomationBatch::findOrFail($batchId);

        $batch->update(['status' => 'cancelled']);

        AutomationJob::where('batch_id', $batch->id)
            ->whereIn('status', ['pending', 'processing'])
            ->update([
                'status' => 'failed',
                'error_message' => 'Dibatalkan oleh pengguna.',
            ]);

        return response()->json([
            'success' => true,
            'message' => "Proses otomasi [Batch #{$batch->id}] berhasil dibatalkan.",
        ]);
    }

    /**
     * Update Batch Status (e.g. Pause / Resume)
     */
    public function updateBatchStatus(Request $request, string $batchId): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:pending,processing,paused,cancelled',
        ]);

        $batch = AutomationBatch::findOrFail($batchId);
        $batch->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => "Status batch berhasil diubah menjadi '{$request->status}'.",
        ]);
    }

    /**
     * Server-Sent Events (SSE) Real-time Stream
     */
    public function streamBatchProgress(string $batchId): StreamedResponse
    {
        return new StreamedResponse(function () use ($batchId) {
            if (ob_get_level() > 0) {
                ob_end_clean();
            }

            $maxIterations = 60;
            $iteration = 0;

            $aiService = app(\App\Services\Ai\GeminiMultimodalService::class);
            $marketplaceManager = $this->marketplaceManager;

            while ($iteration < $maxIterations) {
                $batch = AutomationBatch::with(['jobs.product'])->find($batchId);

                if (!$batch) {
                    echo "event: error\n";
                    echo "data: " . json_encode(['message' => 'Batch not found']) . "\n\n";
                    flush();
                    break;
                }

                if ($batch->status === 'cancelled') {
                    echo "event: cancelled\n";
                    echo "data: " . json_encode(['message' => 'Batch dibatalkan']) . "\n\n";
                    flush();
                    break;
                }

                // If any pending jobs remain and haven't been processed, execute 1 tick
                $pendingJob = $batch->jobs->firstWhere('status', 'pending');
                if ($pendingJob && $batch->status === 'processing') {
                    try {
                        $jobInstance = new ProcessProductOptimizationJob(
                            $pendingJob->id,
                            $pendingJob->product_id,
                            $batch->id
                        );
                        $jobInstance->handle($aiService, $marketplaceManager);
                        $batch = AutomationBatch::with(['jobs.product'])->find($batchId);
                    } catch (\Throwable $e) {
                        Log::error("SSE worker tick error: " . $e->getMessage());
                    }
                }

                $jobsSummary = $batch->jobs->map(function ($job) {
                    return [
                        'job_id' => $job->id,
                        'product_id' => $job->product_id,
                        'title' => $job->product->original_title ?? '',
                        'generated_title' => $job->product->generated_title,
                        'usps' => $job->product->generated_usps,
                        'status' => $job->status,
                        'error_message' => $job->error_message,
                        'updated_at' => $job->updated_at->toIso8601String(),
                    ];
                });

                $payload = [
                    'batch_id' => $batch->id,
                    'status' => $batch->status,
                    'total_products' => $batch->total_products,
                    'processed_count' => $batch->processed_count,
                    'success_count' => $batch->success_count,
                    'failed_count' => $batch->failed_count,
                    'percentage' => $batch->total_products > 0 ? round(($batch->processed_count / $batch->total_products) * 100) : 0,
                    'jobs' => $jobsSummary,
                ];

                echo "event: progress\n";
                echo "data: " . json_encode($payload) . "\n\n";
                flush();

                if ($batch->status === 'completed' || $batch->status === 'failed' || $batch->status === 'cancelled') {
                    echo "event: finished\n";
                    echo "data: " . json_encode($payload) . "\n\n";
                    flush();
                    break;
                }

                sleep(1);
                $iteration++;
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache, no-transform',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    /**
     * Get Before-After Diff comparison data
     */
    public function getProductDiff(string $productId): JsonResponse
    {
        $product = Product::with('store')->findOrFail($productId);

        return response()->json([
            'success' => true,
            'product' => [
                'id' => $product->id,
                'external_id' => $product->external_product_id,
                'store_name' => $product->store->store_name,
                'store_platform' => $product->store->platform,
                'category_name' => $product->category_name,
                'image_urls' => $product->image_urls,
                'original_title' => $product->original_title,
                'original_desc' => $product->original_desc,
                'generated_title' => $product->generated_title,
                'generated_desc' => $product->generated_desc,
                'generated_usps' => $product->generated_usps,
                'seo_keywords' => $product->seo_keywords,
                'status' => $product->status,
                'error_message' => $product->error_message,
                'last_synced_at' => $product->last_synced_at?->diffForHumans(),
            ],
        ]);
    }

    /**
     * Revert product back to original copy and allow re-optimization
     */
    public function revertProduct(string $productId): JsonResponse
    {
        $product = Product::with('store')->findOrFail($productId);

        // 1. Sync original data back to marketplace
        $adapter = $this->marketplaceManager->getAdapter($product->store);
        $adapter->updateProduct($product->store, $product->external_product_id, [
            'title' => $product->original_title,
            'description' => $product->original_desc,
        ]);

        // 2. Update Database status - set to 'reverted' so it can be re-optimized!
        $product->update([
            'status' => 'reverted',
            'generated_title' => null,
            'generated_desc' => null,
            'generated_usps' => null,
            'seo_keywords' => null,
            'last_synced_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Produk berhasil dikembalikan ke teks original dan siap di-optimize ulang.",
            'product' => $product,
        ]);
    }

    /**
     * Save / Update Gemini API Key
     */
    public function saveApiKey(Request $request): JsonResponse
    {
        $request->validate([
            'api_key' => 'nullable|string',
        ]);

        $envPath = base_path('.env');
        if (file_exists($envPath)) {
            $content = file_get_contents($envPath);
            if (str_contains($content, 'GEMINI_API_KEY=')) {
                $content = preg_replace('/GEMINI_API_KEY=.*/', 'GEMINI_API_KEY=' . trim($request->api_key), $content);
            } else {
                $content .= "\nGEMINI_API_KEY=" . trim($request->api_key);
            }
            file_put_contents($envPath, $content);
        }

        return response()->json([
            'success' => true,
            'message' => 'Konfigurasi Google Gemini API Key berhasil diperbarui.',
        ]);
    }
}
