<?php

namespace App\Jobs;

use App\Models\AutomationBatch;
use App\Models\AutomationJob;
use App\Models\Product;
use App\Services\Ai\GeminiMultimodalService;
use App\Services\Marketplace\MarketplaceManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessProductOptimizationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [5, 15, 30];

    public function __construct(
        public string $automationJobId,
        public string $productId,
        public string $batchId
    ) {}

    public function handle(
        GeminiMultimodalService $aiService,
        MarketplaceManager $marketplaceManager
    ): void {
        $jobRecord = AutomationJob::find($this->automationJobId);
        $product = Product::with('store')->find($this->productId);
        $batch = AutomationBatch::find($this->batchId);

        if (!$jobRecord || !$product || !$product->store) {
            Log::error("ProcessProductOptimizationJob: Missing required records for Job {$this->automationJobId}");
            return;
        }

        // Idempotency: If already succeeded, do not re-process
        if ($jobRecord->status === 'success') {
            Log::info("ProcessProductOptimizationJob: Job {$this->automationJobId} already completed.");
            return;
        }

        // Mark as PROCESSING
        $jobRecord->update([
            'status' => 'processing',
            'started_at' => now(),
            'error_message' => null,
        ]);
        $product->update(['status' => 'processing']);

        try {
            Log::info("ProcessProductOptimizationJob: Analyzing product {$product->id} via Gemini Vision API");

            // 1. Call Gemini Multimodal AI Service
            $imageUrls = is_array($product->image_urls) ? $product->image_urls : [];
            $aiResult = $aiService->generateProductOptimization(
                $product->original_title,
                $product->original_desc,
                $product->category_name ?? 'General',
                $imageUrls
            );

            // 2. Update to Marketplace via Adapter
            $adapter = $marketplaceManager->getAdapter($product->store);
            $adapter->updateProduct($product->store, $product->external_product_id, [
                'title' => $aiResult['optimized_title'],
                'description' => $aiResult['formatted_description'],
            ]);

            // 3. Atomically persist to Database
            DB::transaction(function () use ($product, $jobRecord, $batch, $aiResult) {
                $product->update([
                    'generated_title' => $aiResult['optimized_title'],
                    'generated_desc' => $aiResult['formatted_description'],
                    'generated_usps' => $aiResult['detected_usps'],
                    'seo_keywords' => $aiResult['seo_keywords'],
                    'status' => 'success',
                    'error_message' => null,
                    'last_synced_at' => now(),
                ]);

                $jobRecord->update([
                    'status' => 'success',
                    'completed_at' => now(),
                ]);

                if ($batch) {
                    $batch->increment('processed_count');
                    $batch->increment('success_count');
                    if ($batch->processed_count >= $batch->total_products) {
                        $batch->update(['status' => 'completed']);
                    }
                }
            });

            Log::info("ProcessProductOptimizationJob: Successfully completed product {$product->id}");

        } catch (Throwable $e) {
            $errorMessage = $e->getMessage();
            Log::error("ProcessProductOptimizationJob failed for product {$product->id}: {$errorMessage}");

            DB::transaction(function () use ($jobRecord, $product, $batch, $errorMessage) {
                $jobRecord->update([
                    'status' => 'failed',
                    'error_message' => mb_substr($errorMessage, 0, 1000),
                    'retry_count' => $jobRecord->retry_count + 1,
                ]);

                $product->update([
                    'status' => 'failed',
                    'error_message' => mb_substr($errorMessage, 0, 1000),
                ]);

                if ($batch) {
                    $batch->increment('processed_count');
                    $batch->increment('failed_count');
                    if ($batch->processed_count >= $batch->total_products) {
                        $batch->update(['status' => 'completed']);
                    }
                }
            });

            // Re-throw if retry attempts remain
            if ($this->attempts() < $this->tries) {
                throw $e;
            }
        }
    }
}
