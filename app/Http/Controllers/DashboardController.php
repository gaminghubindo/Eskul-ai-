<?php

namespace App\Http\Controllers;

use App\Models\AutomationBatch;
use App\Models\Product;
use App\Models\Store;
use App\Services\Marketplace\MarketplaceManager;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected MarketplaceManager $marketplaceManager
    ) {}

    public function index(Request $request): View
    {
        // 1. Get or create default store
        $stores = Store::where('is_active', true)->get();
        $selectedStoreId = $request->query('store_id', $stores->first()?->id);
        $activeStore = $stores->firstWhere('id', $selectedStoreId) ?? $stores->first();

        // 2. Fetch categories for active store
        $categories = [];
        if ($activeStore) {
            $adapter = $this->marketplaceManager->getAdapter($activeStore);
            $categories = $adapter->fetchCategories($activeStore);
        }

        // 3. Products query with filters
        $productsQuery = Product::with('store');
        if ($activeStore) {
            $productsQuery->where('store_id', $activeStore->id);
        }

        if ($request->filled('category')) {
            $productsQuery->where('category_id', $request->query('category'));
        }

        if ($request->filled('status')) {
            $productsQuery->where('status', $request->query('status'));
        }

        if ($request->filled('search')) {
            $search = '%' . $request->query('search') . '%';
            $productsQuery->where(function ($q) use ($search) {
                $q->where('original_title', 'like', $search)
                  ->orWhere('generated_title', 'like', $search)
                  ->orWhere('external_product_id', 'like', $search);
            });
        }

        $products = $productsQuery->latest('updated_at')->paginate(12)->withQueryString();

        // 4. Statistics Calculation
        $totalProducts = Product::when($activeStore, fn($q) => $q->where('store_id', $activeStore->id))->count();
        $optimizedProducts = Product::when($activeStore, fn($q) => $q->where('store_id', $activeStore->id))->where('status', 'success')->count();
        $failedProducts = Product::when($activeStore, fn($q) => $q->where('store_id', $activeStore->id))->where('status', 'failed')->count();
        $revertedProducts = Product::when($activeStore, fn($q) => $q->where('store_id', $activeStore->id))->where('status', 'reverted')->count();
        $successRate = $totalProducts > 0 ? round(($optimizedProducts / $totalProducts) * 100, 1) : 0;

        // 5. Recent Batches
        $recentBatches = AutomationBatch::when($activeStore, fn($q) => $q->where('store_id', $activeStore->id))
            ->latest()
            ->take(5)
            ->get();

        $geminiApiKey = env('GEMINI_API_KEY', '');
        $hasGeminiKey = !empty($geminiApiKey);

        return view('dashboard', compact(
            'stores',
            'activeStore',
            'categories',
            'products',
            'totalProducts',
            'optimizedProducts',
            'failedProducts',
            'revertedProducts',
            'successRate',
            'recentBatches',
            'hasGeminiKey'
        ));
    }
}
