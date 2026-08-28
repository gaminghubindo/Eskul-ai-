<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessProductOptimizationJob;
use App\Models\AutomationBatch;
use App\Models\AutomationJob;
use App\Models\Product;
use App\Models\Store;
use App\Services\Marketplace\MarketplaceManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AutomationController extends Controller
{
    public function __construct(
        protected MarketplaceManager $marketplaceManager
    ) {}

    /**
     * Start bulk category auto-update job
     */
    public function startCategoryBatch(Request $request): JsonResponse
    {
        $request->validate([
            'store_id' => 'required|uuid|exists:stores,id',
            'category_id' => 'required|string',
        ]);

        $store = Store::findOrFail($request->store_id);
        $adapter = $this->marketplaceManager->getAdapter($store);

        // Fetch products from marketplace
        $rawProducts = $adapter->fetchProductsByCategory($store, $request->category_id);

        if (empty($rawProducts)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada produk ditemukan pada kategori ini di marketplace.',
            ], 404);
        }

        $categoryName = $rawProducts[0]['category_name'] ?? 'Kategori Terpilih';

        // 1. Create Batch
        $batch = AutomationBatch::create([
            'store_id' => $store->id,
            'category_id' => $request->category_id,
            'category_name' => $categoryName,
            'total_products' => count($rawProducts),
            'processed_count' => 0,
            'success_count' => 0,
            'failed_count' => 0,
            'status' => 'processing',
        ]);

        // 2. Upsert products and dispatch individual jobs
        foreach ($rawProducts as $item) {
            $product = Product::updateOrCreate(
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
                    'error_message' => null,
                ]
            );

            $jobRecord = AutomationJob::create([
                'batch_id' => $batch->id,
                'store_id' => $store->id,
                'product_id' => $product->id,
                'status' => 'pending',
            ]);

            // Dispatch individual queue job
            ProcessProductOptimizationJob::dispatch(
                $jobRecord->id,
                $product->id,
                $batch->id
            );
        }

        return response()->json([
            'success' => true,
            'batch_id' => $batch->id,
            'total_products' => count($rawProducts),
            'category_name' => $categoryName,
            'message' => "Berhasil memulai optimasi untuk " . count($rawProducts) . " produk.",
        ]);
    }

    /**
     * Server-Sent Events (SSE) Real-time Stream
     */
    public function streamBatchProgress(string $batchId): StreamedResponse
    {
        return new StreamedResponse(function () use ($batchId) {
            // Disable output buffering
            if (ob_get_level() > 0) {
                ob_end_clean();
            }

            $maxIterations = 60; // Max 60 seconds per connection cycle (auto reconnects)
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

                // If any pending jobs remain and haven't been picked up by an external worker, process 1 job per tick
                $pendingJob = $batch->jobs->firstWhere('status', 'pending');
                if ($pendingJob) {
                    try {
                        $jobInstance = new ProcessProductOptimizationJob(
                            $pendingJob->id,
                            $pendingJob->product_id,
                            $batch->id
                        );
                        $jobInstance->handle($aiService, $marketplaceManager);
                        // Refresh batch with updated jobs
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

                // If completed or failed, close the stream
                if ($batch->status === 'completed' || $batch->status === 'failed') {
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
     * Revert product back to original copy
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

        // 2. Update Database status
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
            'message' => "Produk berhasil dikembalikan ke teks original.",
            'product' => $product,
        ]);
    }

    /**
     * Connect or switch store
     */
    public function connectStore(Request $request): JsonResponse
    {
        $request->validate([
            'platform' => 'required|in:SHOPEE,TIKTOK_SHOP,TOKOPEDIA',
            'store_name' => 'required|string|max:150',
            'platform_store_id' => 'required|string|max:100',
        ]);

        $store = Store::updateOrCreate(
            [
                'platform' => $request->platform,
                'platform_store_id' => $request->platform_store_id,
            ],
            [
                'store_name' => $request->store_name,
                'store_access_token' => 'live_token_' . bin2hex(random_bytes(16)),
                'store_refresh_token' => 'refresh_token_' . bin2hex(random_bytes(16)),
                'token_expires_at' => now()->addDays(30),
                'is_active' => true,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => "Toko {$store->store_name} ({$store->platform}) berhasil terhubung.",
            'store' => $store,
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
