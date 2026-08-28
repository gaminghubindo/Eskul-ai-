<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AutoCopy AI — E-Commerce Auto-Copywriter & Store Automation</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (Play CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    colors: {
                        brand: {
                            50: '#ecfdf5',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                        },
                        dark: {
                            800: '#0f172a',
                            850: '#0b1120',
                            900: '#020617',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) lucide.createIcons();
        });
    </script>

    <style>
        [x-cloak] { display: none !important; }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #0b1120;
        }
        ::-webkit-scrollbar-thumb {
            background: #1e293b;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #334155;
        }
        
        .glass-card {
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        
        .glass-header {
            background: rgba(2, 6, 23, 0.85);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        .pulse-glow {
            box-shadow: 0 0 25px rgba(16, 185, 129, 0.25);
        }
    </style>
</head>
<body class="bg-dark-900 text-slate-100 min-h-screen font-sans antialiased selection:bg-brand-500 selection:text-white"
      x-data="ecommerceAutomationApp()"
      x-init="initApp()">

    <!-- Header Navigation -->
    <header class="sticky top-0 z-40 glass-header">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-400 flex items-center justify-center shadow-lg shadow-emerald-500/20">
                    <i data-lucide="sparkles" class="w-5 h-5 text-white"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-extrabold text-lg tracking-tight bg-gradient-to-r from-white via-slate-200 to-slate-400 bg-clip-text text-transparent">
                            AutoCopy AI
                        </span>
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                            v1.0 Pro
                        </span>
                    </div>
                    <p class="text-xs text-slate-400">Multimodal Vision & Store Automation</p>
                </div>
            </div>

            <!-- Store Selector & Quick Actions -->
            <div class="flex items-center gap-3">
                <!-- Store Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-800/80 hover:bg-slate-800 border border-slate-700/60 text-sm font-medium text-slate-200 transition-all">
                        <span class="w-2 h-2 rounded-full {{ $activeStore ? 'bg-emerald-400' : 'bg-amber-400' }} animate-pulse"></span>
                        <span>{{ $activeStore ? $activeStore->store_name : 'Pilih Toko' }}</span>
                        <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400"></i>
                    </button>

                    <div x-show="open" @click.away="open = false" x-cloak 
                         class="absolute right-0 mt-2 w-64 glass-card rounded-xl shadow-2xl p-2 z-50">
                        <div class="px-2 py-1.5 text-xs font-semibold text-slate-400 uppercase tracking-wider">
                            Toko Terhubung
                        </div>
                        @foreach($stores as $st)
                            <a href="?store_id={{ $st->id }}" 
                               class="flex items-center justify-between px-3 py-2 rounded-lg text-sm transition-colors {{ $activeStore && $activeStore->id === $st->id ? 'bg-emerald-500/15 text-emerald-300 font-medium' : 'text-slate-300 hover:bg-slate-800/60' }}">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs px-1.5 py-0.5 rounded bg-slate-800 border border-slate-700 text-slate-300 font-mono">
                                        {{ $st->platform }}
                                    </span>
                                    <span class="truncate max-w-[120px]">{{ $st->store_name }}</span>
                                </div>
                                @if($activeStore && $activeStore->id === $st->id)
                                    <i data-lucide="check" class="w-4 h-4 text-emerald-400"></i>
                                @endif
                            </a>
                        @endforeach
                        <div class="border-t border-slate-800 mt-1 pt-1">
                            <button @click="open = false; showConnectModal = true" 
                                    class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-emerald-400 hover:bg-emerald-500/10 transition-colors">
                                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                                Hubungkan Toko Baru
                            </button>
                        </div>
                    </div>
                </div>

                <!-- API Key Config Button -->
                <button @click="showApiModal = true" 
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-800/80 hover:bg-slate-800 border border-slate-700/60 text-xs font-medium text-slate-300 transition-all">
                    <i data-lucide="key" class="w-3.5 h-3.5 {{ $hasGeminiKey ? 'text-emerald-400' : 'text-amber-400' }}"></i>
                    <span>Gemini API</span>
                </button>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        
        <!-- Welcome & Metrics Banner -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="glass-card rounded-2xl p-5 relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-slate-400 uppercase tracking-wider">Total Produk</span>
                    <div class="p-2 rounded-lg bg-blue-500/10 text-blue-400">
                        <i data-lucide="package" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-3xl font-extrabold text-white">{{ $totalProducts }}</span>
                    <span class="text-xs text-slate-400 ml-1">item di katalog</span>
                </div>
                <div class="absolute -bottom-6 -right-6 w-24 h-24 bg-blue-500/5 rounded-full blur-xl pointer-events-none"></div>
            </div>

            <div class="glass-card rounded-2xl p-5 relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-slate-400 uppercase tracking-wider">AI Optimized</span>
                    <div class="p-2 rounded-lg bg-emerald-500/10 text-emerald-400">
                        <i data-lucide="sparkles" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-3xl font-extrabold text-emerald-400">{{ $optimizedProducts }}</span>
                    <span class="text-xs text-emerald-400/80 ml-1">({{ $successRate }}% Sync)</span>
                </div>
                <div class="absolute -bottom-6 -right-6 w-24 h-24 bg-emerald-500/10 rounded-full blur-xl pointer-events-none"></div>
            </div>

            <div class="glass-card rounded-2xl p-5 relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-slate-400 uppercase tracking-wider">Perlu Optimasi</span>
                    <div class="p-2 rounded-lg bg-amber-500/10 text-amber-400">
                        <i data-lucide="clock" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-3xl font-extrabold text-amber-400">{{ $totalProducts - $optimizedProducts }}</span>
                    <span class="text-xs text-slate-400 ml-1">produk pending</span>
                </div>
                <div class="absolute -bottom-6 -right-6 w-24 h-24 bg-amber-500/5 rounded-full blur-xl pointer-events-none"></div>
            </div>

            <div class="glass-card rounded-2xl p-5 relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-slate-400 uppercase tracking-wider">Rollback / Reverted</span>
                    <div class="p-2 rounded-lg bg-indigo-500/10 text-indigo-400">
                        <i data-lucide="rotate-ccw" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-3xl font-extrabold text-indigo-300">{{ $revertedProducts }}</span>
                    <span class="text-xs text-slate-400 ml-1">telah dipulihkan</span>
                </div>
                <div class="absolute -bottom-6 -right-6 w-24 h-24 bg-indigo-500/5 rounded-full blur-xl pointer-events-none"></div>
            </div>
        </div>

        <!-- Action Control Panel: Category Bulk Automation -->
        <div class="glass-card rounded-2xl p-6 border-l-4 border-l-emerald-500 shadow-xl">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="flex h-2.5 w-2.5 rounded-full bg-emerald-400"></span>
                        <h2 class="text-lg font-bold text-white tracking-tight">Bulk Category Auto-Copywriter Engine</h2>
                    </div>
                    <p class="text-sm text-slate-400">
                        Pilih kategori produk toko untuk menganalisis visual foto via Gemini AI, mengekstrak USP, dan auto-update ke marketplace secara hands-free.
                    </p>
                </div>

                <!-- Form Controls -->
                <div class="flex flex-wrap items-center gap-3">
                    <div class="relative min-w-[240px]">
                        <select x-model="selectedCategory" 
                                class="w-full bg-slate-900/90 border border-slate-700/80 rounded-xl px-4 py-2.5 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 appearance-none">
                            <option value="">-- Pilih Kategori Produk --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat['id'] }}">
                                    {{ $cat['name'] }} ({{ $cat['product_count'] ?? 0 }} item)
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                            <i data-lucide="chevron-down" class="w-4 h-4"></i>
                        </div>
                    </div>

                    <button @click="startBatchAutomation()" 
                            :disabled="!selectedCategory || isProcessing"
                            class="flex items-center gap-2 px-6 py-2.5 rounded-xl font-semibold text-sm transition-all shadow-lg"
                            :class="(!selectedCategory || isProcessing) 
                                ? 'bg-slate-800 text-slate-500 cursor-not-allowed border border-slate-700' 
                                : 'bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white shadow-emerald-500/25 pulse-glow cursor-pointer'">
                        <template x-if="isProcessing">
                            <i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>
                        </template>
                        <template x-if="!isProcessing">
                            <i data-lucide="play" class="w-4 h-4 fill-current"></i>
                        </template>
                        <span x-text="isProcessing ? 'Memproses Antrean...' : 'Start Auto-Update Category'"></span>
                    </button>
                </div>
            </div>

            <!-- Real-Time Progress Bar & SSE Live Terminal (Shows when active) -->
            <div x-show="isProcessing || activeBatch" x-cloak class="mt-6 pt-6 border-t border-slate-800/80 space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-semibold uppercase tracking-wider text-emerald-400 flex items-center gap-1.5">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                            </span>
                            Live Execution Progress
                        </span>
                        <span class="text-xs text-slate-400" x-text="'Batch ID: ' + (activeBatch?.id || 'Inisialisasi...')"></span>
                    </div>

                    <div class="flex items-center gap-4 text-xs font-medium">
                        <span class="text-slate-400">
                            Progres: <strong class="text-white" x-text="progress.processed + '/' + progress.total"></strong> produk
                        </span>
                        <span class="text-emerald-400 font-mono font-bold text-sm" x-text="progress.percentage + '%'"></span>
                    </div>
                </div>

                <!-- Progress Bar Track -->
                <div class="w-full bg-slate-950 rounded-full h-3.5 p-0.5 overflow-hidden border border-slate-800">
                    <div class="bg-gradient-to-r from-emerald-500 via-teal-400 to-emerald-300 h-full rounded-full transition-all duration-300 shadow-sm"
                         :style="`width: ${progress.percentage}%`"></div>
                </div>

                <!-- Live Stream Terminal Log -->
                <div class="bg-slate-950 rounded-xl border border-slate-800 p-3 max-h-40 overflow-y-auto font-mono text-xs space-y-1.5 text-slate-400"
                     x-ref="terminalBox">
                    <template x-for="log in logs" :key="log.id">
                        <div class="flex items-start gap-2 leading-relaxed">
                            <span class="text-slate-600 shrink-0" x-text="log.timestamp"></span>
                            <span class="shrink-0 font-bold"
                                  :class="{
                                      'text-emerald-400': log.type === 'success',
                                      'text-rose-400': log.type === 'error',
                                      'text-blue-400': log.type === 'info',
                                      'text-amber-400': log.type === 'warn'
                                  }" x-text="'[' + log.type.toUpperCase() + ']'"></span>
                            <span class="text-slate-300" x-text="log.message"></span>
                        </div>
                    </template>
                    <div x-show="logs.length === 0" class="text-slate-600 italic">
                        Menunggu event antrean...
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Catalog Section -->
        <div class="space-y-4">
            <!-- Filter & Search Toolbar -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <h3 class="text-lg font-bold text-white">Katalog Produk Toko</h3>
                    <span class="text-xs px-2.5 py-0.5 rounded-full bg-slate-800 border border-slate-700 text-slate-300">
                        {{ $products->total() }} Produk
                    </span>
                </div>

                <!-- Search Form -->
                <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2 w-full sm:w-auto">
                    @if($activeStore)
                        <input type="hidden" name="store_id" value="{{ $activeStore->id }}">
                    @endif
                    <div class="relative w-full sm:w-64">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Cari judul / ID..."
                               class="w-full bg-slate-800/80 border border-slate-700/80 rounded-xl pl-9 pr-3 py-1.5 text-xs text-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40">
                        <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-2 pointer-events-none"></i>
                    </div>

                    <select name="status" onchange="this.form.submit()" 
                            class="bg-slate-800/80 border border-slate-700/80 rounded-xl px-3 py-1.5 text-xs text-slate-200 focus:outline-none">
                        <option value="">Semua Status</option>
                        <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>AI Optimized</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="reverted" {{ request('status') === 'reverted' ? 'selected' : '' }}>Reverted</option>
                    </select>
                </form>
            </div>

            <!-- Products Grid Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse($products as $product)
                    <div class="glass-card rounded-2xl p-4 flex flex-col justify-between hover:border-slate-700/80 transition-all group">
                        <div class="space-y-3">
                            <!-- Image and Header -->
                            <div class="flex gap-3">
                                <div class="w-20 h-20 rounded-xl bg-slate-950 overflow-hidden shrink-0 border border-slate-800 relative group-hover:scale-[1.02] transition-transform">
                                    @php
                                        $imgs = is_array($product->image_urls) ? $product->image_urls : [];
                                        $primaryImg = $imgs[0] ?? 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=300';
                                    @endphp
                                    <img src="{{ $primaryImg }}" alt="{{ $product->original_title }}" 
                                         class="w-full h-full object-cover" loading="lazy">
                                    <span class="absolute bottom-1 right-1 text-[9px] bg-black/70 px-1 rounded text-slate-300 font-mono">
                                        {{ count($imgs) }} foto
                                    </span>
                                </div>

                                <div class="flex-1 min-w-0 space-y-1">
                                    <div class="flex items-center justify-between gap-1">
                                        <span class="text-[10px] font-mono text-slate-400 truncate">
                                            {{ $product->external_product_id }}
                                        </span>
                                        <!-- Status Badge -->
                                        @if($product->status === 'success')
                                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 flex items-center gap-1">
                                                <i data-lucide="check-circle" class="w-3 h-3"></i> AI Optimized
                                            </span>
                                        @elseif($product->status === 'processing')
                                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-blue-500/15 text-blue-400 border border-blue-500/30 flex items-center gap-1 animate-pulse">
                                                <i data-lucide="loader" class="w-3 h-3 animate-spin"></i> Processing
                                            </span>
                                        @elseif($product->status === 'failed')
                                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-rose-500/15 text-rose-400 border border-rose-500/30 flex items-center gap-1">
                                                <i data-lucide="alert-circle" class="w-3 h-3"></i> Failed
                                            </span>
                                        @elseif($product->status === 'reverted')
                                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-indigo-500/15 text-indigo-300 border border-indigo-500/30 flex items-center gap-1">
                                                <i data-lucide="rotate-ccw" class="w-3 h-3"></i> Reverted
                                            </span>
                                        @else
                                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-amber-500/15 text-amber-400 border border-amber-500/30 flex items-center gap-1">
                                                <i data-lucide="clock" class="w-3 h-3"></i> Pending
                                            </span>
                                        @endif
                                    </div>

                                    <h4 class="text-xs font-semibold text-slate-200 line-clamp-2" title="{{ $product->generated_title ?: $product->original_title }}">
                                        {{ $product->generated_title ?: $product->original_title }}
                                    </h4>

                                    <p class="text-[11px] text-slate-400 truncate">
                                        {{ $product->category_name }}
                                    </p>
                                </div>
                            </div>

                            <!-- Extracted USPs preview (if optimized) -->
                            @if($product->generated_usps && is_array($product->generated_usps))
                                <div class="bg-slate-950/60 rounded-xl p-2.5 border border-slate-800/80 space-y-1">
                                    <span class="text-[10px] font-semibold text-emerald-400 uppercase tracking-wider">AI Extracted USPs:</span>
                                    <div class="space-y-1">
                                        @foreach(array_slice($product->generated_usps, 0, 2) as $usp)
                                            <div class="text-[11px] text-slate-300 flex items-center gap-1.5 truncate">
                                                <i data-lucide="check" class="w-3 h-3 text-emerald-400 shrink-0"></i>
                                                <span class="truncate">{{ $usp }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Card Action Buttons -->
                        <div class="mt-4 pt-3 border-t border-slate-800/80 flex items-center justify-between gap-2">
                            <button @click="openDiffModal('{{ $product->id }}')" 
                                    class="flex-1 flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-xs font-medium text-slate-200 transition-colors">
                                <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                Preview Diff
                            </button>

                            @if($product->status === 'success')
                                <button @click="revertSingleProduct('{{ $product->id }}')" 
                                        class="p-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/20 transition-colors"
                                        title="Kembalikan ke Teks Original">
                                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center glass-card rounded-2xl">
                        <i data-lucide="inbox" class="w-12 h-12 text-slate-600 mx-auto mb-3"></i>
                        <h4 class="text-sm font-semibold text-slate-300">Belum Ada Produk Terdata</h4>
                        <p class="text-xs text-slate-500 mt-1">Pilih kategori di atas untuk menarik produk dari marketplace.</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $products->links() }}
            </div>
        </div>
    </main>

    <!-- MODAL 1: Before-After Diff Comparison Modal -->
    <div x-show="showDiffModal" x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/75 backdrop-blur-md p-4"
         @keydown.escape.window="showDiffModal = false">
        <div class="glass-card bg-slate-900/95 rounded-2xl w-full max-w-4xl max-h-[90vh] flex flex-col shadow-2xl border border-slate-700 overflow-hidden"
             @click.away="showDiffModal = false">
            
            <!-- Header -->
            <div class="flex items-center justify-between p-5 border-b border-slate-800 bg-slate-950/60">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-xl bg-emerald-500/10 text-emerald-400">
                        <i data-lucide="split" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-base text-white">Perbandingan Sebelum vs Sesudah AI</h3>
                        <p class="text-xs text-slate-400 font-mono" x-text="'ID: ' + (activeProduct?.external_id || '')"></p>
                    </div>
                </div>
                <button @click="showDiffModal = false" class="p-2 rounded-lg hover:bg-slate-800 text-slate-400 hover:text-white">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Modal Content Body -->
            <div class="p-6 overflow-y-auto space-y-6">
                <template x-if="activeProduct">
                    <div class="space-y-6">
                        <!-- Visual Photos Banner -->
                        <div class="space-y-2">
                            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Foto Produk yang Dianalisis:</span>
                            <div class="flex items-center gap-3 overflow-x-auto pb-2">
                                <template x-for="(img, idx) in activeProduct.image_urls" :key="idx">
                                    <img :src="img" class="w-24 h-24 rounded-xl object-cover border border-slate-800 bg-slate-950 shrink-0">
                                </template>
                            </div>
                        </div>

                        <!-- Diff Grid: Left (Original) vs Right (AI Optimized) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Kolom Kiri: Data Asli -->
                            <div class="p-4 rounded-xl bg-slate-950/70 border border-slate-800/80 space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold px-2.5 py-1 rounded bg-amber-500/15 text-amber-400 border border-amber-500/30">
                                        Data Asli (Sebelum)
                                    </span>
                                </div>

                                <div>
                                    <label class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Judul Produk Asli</label>
                                    <p class="text-xs text-slate-200 mt-1 font-medium bg-slate-900 p-2.5 rounded-lg border border-slate-800"
                                       x-text="activeProduct.original_title"></p>
                                </div>

                                <div>
                                    <label class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Deskripsi Asli</label>
                                    <p class="text-xs text-slate-400 mt-1 whitespace-pre-line bg-slate-900 p-2.5 rounded-lg border border-slate-800 max-h-56 overflow-y-auto leading-relaxed"
                                       x-text="activeProduct.original_desc"></p>
                                </div>
                            </div>

                            <!-- Kolom Kanan: AI Optimized -->
                            <div class="p-4 rounded-xl bg-emerald-950/20 border border-emerald-800/40 space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold px-2.5 py-1 rounded bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                                        AI Multimodal Optimized (Sesudah)
                                    </span>
                                </div>

                                <div>
                                    <label class="text-[11px] font-semibold text-emerald-400 uppercase tracking-wider">Judul SEO Baru (Maks 120 Karakter)</label>
                                    <p class="text-xs text-emerald-200 mt-1 font-semibold bg-slate-900 p-2.5 rounded-lg border border-emerald-800/40"
                                       x-text="activeProduct.generated_title || '(Belum dioptimasi)'"></p>
                                </div>

                                <!-- USPs -->
                                <div x-show="activeProduct.generated_usps">
                                    <label class="text-[11px] font-semibold text-emerald-400 uppercase tracking-wider">Detected Visual USPs</label>
                                    <ul class="mt-1 space-y-1 bg-slate-900 p-2.5 rounded-lg border border-emerald-800/40">
                                        <template x-for="(usp, i) in activeProduct.generated_usps" :key="i">
                                            <li class="text-xs text-emerald-300 flex items-center gap-1.5">
                                                <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-400 shrink-0"></i>
                                                <span x-text="usp"></span>
                                            </li>
                                        </template>
                                    </ul>
                                </div>

                                <div>
                                    <label class="text-[11px] font-semibold text-emerald-400 uppercase tracking-wider">Deskripsi Terstruktur</label>
                                    <p class="text-xs text-slate-300 mt-1 whitespace-pre-line bg-slate-900 p-2.5 rounded-lg border border-emerald-800/40 max-h-56 overflow-y-auto leading-relaxed"
                                       x-text="activeProduct.generated_desc || '(Belum dioptimasi)'"></p>
                                </div>

                                <!-- SEO Keywords Badges -->
                                <div x-show="activeProduct.seo_keywords">
                                    <label class="text-[11px] font-semibold text-emerald-400 uppercase tracking-wider">Target Kata Kunci SEO</label>
                                    <div class="mt-1.5 flex flex-wrap gap-1.5">
                                        <template x-for="(kw, k) in activeProduct.seo_keywords" :key="k">
                                            <span class="text-[10px] px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-300 border border-emerald-500/20 font-mono"
                                                  x-text="'#' + kw"></span>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Footer Action Bar -->
            <div class="flex items-center justify-between p-4 border-t border-slate-800 bg-slate-950">
                <button @click="revertActiveProduct()" 
                        class="flex items-center gap-2 px-4 py-2 text-xs font-semibold text-rose-400 hover:bg-rose-950/40 border border-rose-800/40 rounded-xl transition-colors">
                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    Revert ke Original
                </button>
                <button @click="showDiffModal = false" 
                        class="px-5 py-2 text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-white rounded-xl transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL 2: Hubungkan Toko Baru (OAuth Simulator) -->
    <div x-show="showConnectModal" x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/75 backdrop-blur-md p-4">
        <div class="glass-card bg-slate-900 rounded-2xl w-full max-w-md p-6 border border-slate-700 shadow-2xl space-y-5"
             @click.away="showConnectModal = false">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-base text-white">Hubungkan Toko Marketplace</h3>
                <button @click="showConnectModal = false" class="text-slate-400 hover:text-white">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form @submit.prevent="submitConnectStore()" class="space-y-4">
                <div>
                    <label class="text-xs font-semibold text-slate-300">Platform Marketplace</label>
                    <select x-model="connectForm.platform" 
                            class="w-full mt-1 bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-200">
                        <option value="SHOPEE">Shopee Open API</option>
                        <option value="TIKTOK_SHOP">TikTok Shop Partner</option>
                        <option value="TOKOPEDIA">Tokopedia Seller API</option>
                    </select>
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-300">Nama Toko</label>
                    <input type="text" x-model="connectForm.store_name" required placeholder="Contoh: Butik Cantik Official"
                           class="w-full mt-1 bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-200">
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-300">Shop ID / Partner Code</label>
                    <input type="text" x-model="connectForm.platform_store_id" required placeholder="Contoh: OFFICIAL-9911"
                           class="w-full mt-1 bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-200 font-mono">
                </div>

                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" @click="showConnectModal = false" class="px-4 py-2 rounded-xl text-xs text-slate-400 hover:bg-slate-800">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white">
                        Otorisasi & Hubungkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 3: Gemini API Key Config Modal -->
    <div x-show="showApiModal" x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/75 backdrop-blur-md p-4">
        <div class="glass-card bg-slate-900 rounded-2xl w-full max-w-md p-6 border border-slate-700 shadow-2xl space-y-5"
             @click.away="showApiModal = false">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i data-lucide="key" class="w-5 h-5 text-emerald-400"></i>
                    <h3 class="font-bold text-base text-white">Konfigurasi Google Gemini API</h3>
                </div>
                <button @click="showApiModal = false" class="text-slate-400 hover:text-white">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <p class="text-xs text-slate-400 leading-relaxed">
                Masukkan Google Gemini API Key Anda untuk mengaktifkan AI Multimodal Vision model (<code class="text-emerald-400">gemini-1.5-flash</code>). Jika kosong, sistem otomatis menggunakan Intelligent Built-in Fallback Synthesizer.
            </p>

            <form @submit.prevent="submitApiKey()" class="space-y-4">
                <div>
                    <label class="text-xs font-semibold text-slate-300">Gemini API Key</label>
                    <input type="password" x-model="apiKeyInput" placeholder="AIzaSy..."
                           class="w-full mt-1 bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-200 font-mono">
                </div>

                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" @click="showApiModal = false" class="px-4 py-2 rounded-xl text-xs text-slate-400 hover:bg-slate-800">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white">
                        Simpan Konfigurasi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Alpine.js Main Controller Script -->
    <script>
        function ecommerceAutomationApp() {
            return {
                selectedCategory: '',
                activeStoreId: '{{ $activeStore?->id }}',
                isProcessing: false,
                activeBatch: null,
                progress: {
                    total: 0,
                    processed: 0,
                    success: 0,
                    failed: 0,
                    percentage: 0,
                },
                logs: [],
                eventSource: null,

                // Modals
                showDiffModal: false,
                showConnectModal: false,
                showApiModal: false,
                activeProduct: null,
                apiKeyInput: '{{ env('GEMINI_API_KEY', '') }}',
                connectForm: {
                    platform: 'SHOPEE',
                    store_name: '',
                    platform_store_id: 'OFFICIAL-' + Math.floor(1000 + Math.random() * 9000),
                },

                initApp() {
                    this.$nextTick(() => {
                        if (window.lucide) {
                            window.lucide.createIcons();
                        }
                    });
                },

                appendLog(message, type = 'info') {
                    const time = new Date().toLocaleTimeString();
                    this.logs.unshift({
                        id: Math.random().toString(36).substring(7),
                        timestamp: time,
                        message: message,
                        type: type
                    });
                    if (this.logs.length > 50) this.logs.pop();
                    
                    this.$nextTick(() => {
                        if (this.$refs.terminalBox) {
                            this.$refs.terminalBox.scrollTop = 0;
                        }
                        if (window.lucide) window.lucide.createIcons();
                    });
                },

                async startBatchAutomation() {
                    if (!this.selectedCategory || !this.activeStoreId) return;

                    this.isProcessing = true;
                    this.logs = [];
                    this.appendLog("Menginisialisasi bulk job per kategori...", "info");

                    try {
                        const res = await fetch('/api/automation/start', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify({
                                store_id: this.activeStoreId,
                                category_id: this.selectedCategory,
                            }),
                        });

                        const data = await res.json();
                        if (!data.success) {
                            this.appendLog(data.message || 'Gagal memulai batch', 'error');
                            this.isProcessing = false;
                            return;
                        }

                        this.activeBatch = { id: data.batch_id };
                        this.progress.total = data.total_products;
                        this.progress.processed = 0;
                        this.progress.percentage = 0;

                        this.appendLog(`Batch terdaftar (${data.total_products} produk). Membuka Real-time SSE Stream...`, 'success');

                        // Open SSE Stream
                        this.connectStream(data.batch_id);

                    } catch (e) {
                        this.appendLog("Gagal menghubungi server: " + e.message, "error");
                        this.isProcessing = false;
                    }
                },

                connectStream(batchId) {
                    if (this.eventSource) {
                        this.eventSource.close();
                    }

                    this.eventSource = new EventSource(`/api/automation/stream/${batchId}`);

                    this.eventSource.addEventListener('progress', (e) => {
                        const data = JSON.parse(e.data);
                        this.progress.total = data.total_products;
                        this.progress.processed = data.processed_count;
                        this.progress.success = data.success_count;
                        this.progress.failed = data.failed_count;
                        this.progress.percentage = data.percentage;

                        // Check newly processed jobs
                        if (data.jobs && data.jobs.length > 0) {
                            const latest = data.jobs.find(j => j.status === 'success' || j.status === 'failed');
                            if (latest) {
                                this.appendLog(`Produk [${latest.product_id.substring(0,8)}] status: ${latest.status.toUpperCase()} — ${latest.generated_title || latest.title}`, latest.status === 'success' ? 'success' : 'warn');
                            }
                        }
                    });

                    this.eventSource.addEventListener('finished', (e) => {
                        const data = JSON.parse(e.data);
                        this.progress.percentage = 100;
                        this.appendLog(`🎉 Selesai! Berhasil mengoptimasi ${data.success_count} produk, ${data.failed_count} gagal.`, 'success');
                        this.isProcessing = false;
                        this.eventSource.close();
                        setTimeout(() => {
                            window.location.reload();
                        }, 2500);
                    });

                    this.eventSource.onerror = (err) => {
                        console.warn("SSE stream reconnecting/closed.", err);
                    };
                },

                async openDiffModal(productId) {
                    try {
                        const res = await fetch(`/api/automation/diff/${productId}`);
                        const data = await res.json();
                        if (data.success) {
                            this.activeProduct = data.product;
                            this.showDiffModal = true;
                            this.$nextTick(() => {
                                if (window.lucide) window.lucide.createIcons();
                            });
                        }
                    } catch (e) {
                        alert("Gagal memuat preview diff: " + e.message);
                    }
                },

                async revertActiveProduct() {
                    if (!this.activeProduct) return;
                    if (!confirm("Apakah Anda yakin ingin mengembalikan produk ini ke judul & deskripsi aslinya?")) return;

                    await this.revertSingleProduct(this.activeProduct.id);
                    this.showDiffModal = false;
                },

                async revertSingleProduct(productId) {
                    try {
                        const res = await fetch(`/api/automation/revert/${productId}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                        });
                        const data = await res.json();
                        if (data.success) {
                            alert("Produk berhasil di-revert ke versi original!");
                            window.location.reload();
                        }
                    } catch (e) {
                        alert("Gagal revert: " + e.message);
                    }
                },

                async submitConnectStore() {
                    try {
                        const res = await fetch('/api/stores/connect', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify(this.connectForm),
                        });
                        const data = await res.json();
                        if (data.success) {
                            alert(data.message);
                            window.location.href = `/?store_id=${data.store.id}`;
                        }
                    } catch (e) {
                        alert("Gagal menghubungkan toko: " + e.message);
                    }
                },

                async submitApiKey() {
                    try {
                        const res = await fetch('/api/settings/gemini', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify({ api_key: this.apiKeyInput }),
                        });
                        const data = await res.json();
                        if (data.success) {
                            alert(data.message);
                            this.showApiModal = false;
                            window.location.reload();
                        }
                    } catch (e) {
                        alert("Gagal menyimpan API key: " + e.message);
                    }
                }
            }
        }
    </script>
</body>
</html>
