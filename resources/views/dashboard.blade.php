<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Eskul AI — 3D Multi-Marketplace Hub & Delivery OS</title>
    
    <!-- Google Fonts: Plus Jakarta Sans (UI) & JetBrains Mono (Technical/SKU) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Three.js (r128) for Real 3D WebGL Rendering -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    
    <!-- Tailwind CSS (Play CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    colors: {
                        brand: {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            200: '#fed7aa',
                            300: '#fdba74',
                            400: '#fb923c',
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c',
                            800: '#9a3412',
                            900: '#7c2d12',
                        },
                        marketplace: {
                            tokopedia: '#03AC0E',
                            shopee: '#EE4D2D',
                            lazada: '#0055FF',
                            tiktok: '#111827',
                        },
                        neutral: {
                            page: '#F4F6F9',
                            card: '#FFFFFF',
                            border: '#E2E8F0',
                            subtle: '#F8FAFC',
                            hover: '#F1F5F9',
                            heading: '#0F172A',
                            body: '#334155',
                            muted: '#64748B',
                        }
                    },
                    boxShadow: {
                        'glow-brand': '0 0 25px rgba(249, 115, 22, 0.22)',
                        'glow-emerald': '0 0 25px rgba(3, 172, 14, 0.22)',
                        'glow-orange': '0 0 25px rgba(238, 77, 45, 0.22)',
                        'glow-blue': '0 0 25px rgba(0, 85, 255, 0.22)',
                        'soft-card': '0 4px 20px -2px rgba(15, 23, 42, 0.05), 0 2px 6px -1px rgba(15, 23, 42, 0.03)',
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
        
        /* Modern Clean Scrollbar */
        ::-webkit-scrollbar {
            width: 7px;
            height: 7px;
        }
        ::-webkit-scrollbar-track {
            background: #F1F5F9;
        }
        ::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #F97316;
        }

        /* Glassmorphism & Soft Cards */
        .card-surface {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05);
        }

        .card-glass {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(226, 232, 240, 0.85);
        }

        .canvas-3d-container {
            position: relative;
            overflow: hidden;
            border-radius: 1.25rem;
        }
        
        .canvas-3d-container canvas {
            display: block;
            width: 100% !important;
            height: 100% !important;
        }

        /* Laser Scan Keyframe */
        @keyframes scannerBeam {
            0% { top: 0%; opacity: 0.8; }
            50% { top: 90%; opacity: 1; }
            100% { top: 0%; opacity: 0.8; }
        }
        .scanner-line {
            animation: scannerBeam 2s ease-in-out infinite;
        }

        /* Float pulse keyframe */
        @keyframes softFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-6px); }
        }
        .animate-soft-float {
            animation: softFloat 4s ease-in-out infinite;
        }
    </style>
</head>
<body class="bg-[#F4F6F9] text-slate-700 min-h-screen font-sans antialiased selection:bg-brand-500 selection:text-white"
      x-data="eskul3DApp()"
      x-init="initApp()">

    <!-- ========================================================================= -->
    <!-- TOAST NOTIFICATION CONTAINER -->
    <!-- ========================================================================= -->
    <div class="fixed bottom-5 right-5 z-[9999] flex flex-col gap-2.5 max-w-sm w-full pointer-events-none">
        <template x-for="t in toasts" :key="t.id">
            <div class="pointer-events-auto flex items-start gap-3 p-4 rounded-2xl border shadow-xl transition-all duration-300 transform translate-y-0"
                 :class="{
                     'bg-white border-emerald-300 text-emerald-900 shadow-glow-emerald': t.type === 'success',
                     'bg-white border-rose-300 text-rose-900': t.type === 'error',
                     'bg-white border-amber-300 text-amber-900': t.type === 'warning',
                     'bg-white border-brand-300 text-brand-900 shadow-glow-brand': t.type === 'info'
                 }">
                <div class="p-2 rounded-xl shrink-0"
                     :class="{
                         'bg-emerald-50 text-emerald-600': t.type === 'success',
                         'bg-rose-50 text-rose-600': t.type === 'error',
                         'bg-amber-50 text-amber-600': t.type === 'warning',
                         'bg-brand-50 text-brand-600': t.type === 'info'
                     }">
                    <template x-if="t.type === 'success'"><i data-lucide="check-circle-2" class="w-5 h-5"></i></template>
                    <template x-if="t.type === 'error'"><i data-lucide="alert-circle" class="w-5 h-5"></i></template>
                    <template x-if="t.type === 'warning'"><i data-lucide="alert-triangle" class="w-5 h-5"></i></template>
                    <template x-if="t.type === 'info'"><i data-lucide="info" class="w-5 h-5"></i></template>
                </div>
                <div class="flex-1 text-xs">
                    <p class="font-bold uppercase tracking-wider text-[11px]" x-text="t.title"></p>
                    <p class="text-slate-600 mt-0.5 leading-relaxed" x-text="t.message"></p>
                </div>
                <button @click="removeToast(t.id)" class="text-slate-400 hover:text-slate-700 shrink-0">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        </template>
    </div>

    <!-- ========================================================================= -->
    <!-- 1. APP HEADER -->
    <!-- ========================================================================= -->
    <header class="sticky top-0 z-40 border-b border-slate-200 bg-white/90 backdrop-blur-md shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between gap-4">
            
            <!-- Left: Brand Logo & Title -->
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-brand-500 to-amber-600 flex items-center justify-center text-white font-black text-sm shadow-glow-brand group-hover:scale-105 transition-transform">
                        <i data-lucide="truck" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-extrabold text-base tracking-tight text-slate-900 group-hover:text-brand-600 transition-colors">
                                Eskul AI
                            </span>
                            <span class="px-2 py-0.5 text-[10px] font-mono font-bold bg-brand-50 text-brand-700 border border-brand-200 rounded-full">
                                3D Delivery OS
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500 font-mono">Tokopedia &bull; Shopee &bull; Lazada</p>
                    </div>
                </a>
            </div>

            <!-- Center: Navigation Tabs (Desktop) -->
            <nav class="hidden md:flex items-center p-1.5 rounded-2xl bg-slate-100/90 border border-slate-200">
                <button @click="currentTab = 'overview'" 
                        class="px-4 py-2 rounded-xl text-xs font-semibold flex items-center gap-2 transition-all"
                        :class="currentTab === 'overview' ? 'bg-white text-slate-900 shadow-sm font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60'">
                    <i data-lucide="layout-dashboard" class="w-4 h-4 text-brand-500"></i>
                    Ringkasan 3D
                </button>
                <button @click="currentTab = 'catalog'" 
                        class="px-4 py-2 rounded-xl text-xs font-semibold flex items-center gap-2 transition-all"
                        :class="currentTab === 'catalog' ? 'bg-white text-slate-900 shadow-sm font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60'">
                    <i data-lucide="package" class="w-4 h-4 text-amber-500"></i>
                    Katalog Produk
                    <span class="px-1.5 py-0.2 bg-slate-200 text-slate-700 text-[10px] rounded-md font-mono font-bold">{{ $totalProducts }}</span>
                </button>
                <button @click="currentTab = 'automation'" 
                        class="px-4 py-2 rounded-xl text-xs font-semibold flex items-center gap-2 transition-all"
                        :class="currentTab === 'automation' ? 'bg-white text-slate-900 shadow-sm font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60'">
                    <i data-lucide="zap" class="w-4 h-4 text-emerald-500"></i>
                    Studio Otomasi
                </button>
                <button @click="currentTab = 'stores'" 
                        class="px-4 py-2 rounded-xl text-xs font-semibold flex items-center gap-2 transition-all"
                        :class="currentTab === 'stores' ? 'bg-white text-slate-900 shadow-sm font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60'">
                    <i data-lucide="store" class="w-4 h-4 text-blue-500"></i>
                    Kelola Toko
                    <span class="px-1.5 py-0.2 bg-slate-200 text-slate-700 text-[10px] rounded-md font-mono font-bold">{{ $stores->count() }}</span>
                </button>
            </nav>

            <!-- Right Actions: Channel Switcher, + Hubungkan Toko, Gemini Status -->
            <div class="flex items-center gap-2.5">
                
                <!-- Gemini API Status -->
                <button @click="showApiModal = true" 
                        class="h-10 px-3 rounded-xl border border-slate-200 bg-white hover:border-slate-300 flex items-center gap-2 transition-all text-xs font-mono group shadow-sm">
                    <span class="w-2.5 h-2.5 rounded-full {{ $hasGeminiKey ? 'bg-emerald-500 shadow-glow-emerald animate-pulse' : 'bg-rose-500' }}"></span>
                    <span class="hidden sm:inline font-semibold text-slate-700 group-hover:text-slate-900">Gemini Vision</span>
                    <span class="text-[10px] px-1.5 py-0.5 rounded font-bold {{ $hasGeminiKey ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                        {{ $hasGeminiKey ? 'ONLINE' : 'SETUP' }}
                    </span>
                </button>

                <!-- Store Channel Switcher Dropdown -->
                <div class="relative" x-data="{ openStoreDropdown: false }">
                    <button @click="openStoreDropdown = !openStoreDropdown"
                            class="h-10 px-3.5 rounded-xl border border-slate-200 bg-white hover:border-brand-500/50 flex items-center gap-2.5 transition-all text-xs font-medium shadow-sm">
                        <span class="w-2.5 h-2.5 rounded-full"
                              :class="{
                                  'bg-[#EE4D2D]': '{{ $activeStore?->platform }}' === 'SHOPEE',
                                  'bg-[#03AC0E]': '{{ $activeStore?->platform }}' === 'TOKOPEDIA',
                                  'bg-[#0055FF]': '{{ $activeStore?->platform }}' === 'LAZADA',
                                  'bg-slate-900': '{{ $activeStore?->platform }}' === 'TIKTOK_SHOP'
                              }"></span>
                        <div class="text-left leading-tight hidden sm:block">
                            <span class="font-bold text-slate-900 block truncate max-w-[130px]">{{ $activeStore?->store_name ?? 'Pilih Toko' }}</span>
                            <span class="text-[10px] text-slate-500 font-mono uppercase">{{ $activeStore?->platform ?? 'NONE' }}</span>
                        </div>
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 transition-transform" :class="{ 'rotate-180': openStoreDropdown }"></i>
                    </button>

                    <!-- Dropdown Panel -->
                    <div x-show="openStoreDropdown" @click.away="openStoreDropdown = false" x-cloak
                         class="absolute right-0 mt-2 w-72 rounded-2xl bg-white border border-slate-200 shadow-2xl p-2 z-50">
                        <div class="px-3 py-2 text-[10px] font-mono uppercase tracking-wider text-slate-500 border-b border-slate-100">
                            Channel Toko Terhubung
                        </div>
                        <div class="py-1 max-h-60 overflow-y-auto space-y-1">
                            @forelse($stores as $st)
                                <a href="?store_id={{ $st->id }}" 
                                   class="flex items-center justify-between p-2.5 rounded-xl transition-all text-xs font-medium {{ $activeStore && $activeStore->id === $st->id ? 'bg-brand-50 text-brand-900 font-bold border border-brand-200' : 'text-slate-700 hover:bg-slate-100' }}">
                                    <div class="flex items-center gap-2 truncate">
                                        <span class="w-2.5 h-2.5 rounded-full shrink-0"
                                              style="background-color: {{ match($st->platform) { 'TOKOPEDIA' => '#03AC0E', 'SHOPEE' => '#EE4D2D', 'LAZADA' => '#0055FF', default => '#0F172A' } }}"></span>
                                        <span class="truncate">{{ $st->store_name }}</span>
                                    </div>
                                    <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-slate-100 text-slate-700 shrink-0 uppercase font-bold">{{ $st->platform }}</span>
                                </a>
                            @empty
                                <div class="p-3 text-center text-xs text-slate-500">Belum ada toko terhubung.</div>
                            @endforelse
                        </div>
                        <div class="pt-2 border-t border-slate-100">
                            <button @click="openStoreDropdown = false; openAddStoreModal()" 
                                    class="w-full flex items-center justify-center gap-2 p-2 rounded-xl text-xs font-bold text-brand-600 hover:bg-brand-50 transition-colors">
                                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                                Tambah Toko Baru
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Primary Action: + Hubungkan Toko -->
                <button @click="openAddStoreModal()" 
                        class="h-10 px-4 rounded-xl bg-brand-600 hover:bg-brand-500 text-white text-xs font-bold flex items-center gap-2 shadow-glow-brand transition-all active:scale-95">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span class="hidden sm:inline">Hubungkan Toko</span>
                </button>

            </div>
        </div>

        <!-- Mobile Navigation Tabs Bar -->
        <div class="flex md:hidden items-center justify-around border-t border-slate-200 bg-white px-2 py-2 text-xs font-medium">
            <button @click="currentTab = 'overview'" class="p-2 rounded-lg" :class="currentTab === 'overview' ? 'text-brand-600 font-bold' : 'text-slate-500'">Ringkasan</button>
            <button @click="currentTab = 'catalog'" class="p-2 rounded-lg" :class="currentTab === 'catalog' ? 'text-brand-600 font-bold' : 'text-slate-500'">Katalog</button>
            <button @click="currentTab = 'automation'" class="p-2 rounded-lg" :class="currentTab === 'automation' ? 'text-brand-600 font-bold' : 'text-slate-500'">Otomasi</button>
            <button @click="currentTab = 'stores'" class="p-2 rounded-lg" :class="currentTab === 'stores' ? 'text-brand-600 font-bold' : 'text-slate-500'">Toko</button>
        </div>
    </header>

    <!-- ========================================================================= -->
    <!-- MAIN CONTENT -->
    <!-- ========================================================================= -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

        <!-- ========================================================================= -->
        <!-- 3D HERO BANNER: REAL-TIME LOW-POLY 3D COURIER & CARGO SCOOTER -->
        <!-- ========================================================================= -->
        <div class="card-surface p-6 sm:p-8 rounded-3xl relative overflow-hidden grid grid-cols-1 lg:grid-cols-12 gap-8 items-center bg-gradient-to-r from-white via-amber-50/20 to-orange-50/30">
            
            <!-- Left 7 Cols: Store Hub Info & Interactive 3D State Controls -->
            <div class="lg:col-span-7 space-y-5 z-10">
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 rounded-full text-[10px] font-mono font-bold uppercase tracking-wider text-white shadow-sm"
                          style="background-color: {{ match($activeStore?->platform) { 'TOKOPEDIA' => '#03AC0E', 'SHOPEE' => '#EE4D2D', 'LAZADA' => '#0055FF', default => '#EA580C' } }}">
                        {{ $activeStore?->platform ?? 'HUB AKTIF' }}
                    </span>
                    <span class="text-xs font-mono text-slate-500">3D Courier & Logistics Engine</span>
                </div>

                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight leading-snug">
                        {{ $activeStore?->store_name ?? 'Eskul AI Multi-Marketplace Hub' }}
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-600 mt-1.5 leading-relaxed">
                        Pusat otomasi listing multi-marketplace dengan kecerdasan Gemini Vision multimodal. Kurir 3D merespons arah kursor mouse secara interaktif, mensimulasikan rute pengiriman paket ke toko resmi.
                    </p>
                </div>

                <!-- 3D Courier Animation State Preview Triggers -->
                <div class="pt-2">
                    <span class="text-[10px] font-mono uppercase text-slate-500 tracking-wider font-bold block mb-2">
                        SIMULASI STATE KURIR 3D:
                    </span>
                    <div class="flex items-center gap-2 flex-wrap text-xs font-mono">
                        <button @click="setCourierState('idle')" 
                                class="px-3 py-1.5 rounded-xl border transition-all"
                                :class="courierState === 'idle' ? 'bg-brand-600 text-white border-brand-600 shadow-glow-brand font-bold' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50'">
                            📦 Idle Standby
                        </button>
                        <button @click="setCourierState('driving')" 
                                class="px-3 py-1.5 rounded-xl border transition-all"
                                :class="courierState === 'driving' ? 'bg-amber-600 text-white border-amber-600 shadow-glow-orange font-bold' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50'">
                            🛵 Armada Jalan
                        </button>
                        <button @click="setCourierState('success')" 
                                class="px-3 py-1.5 rounded-xl border transition-all"
                                :class="courierState === 'success' ? 'bg-emerald-600 text-white border-emerald-600 shadow-glow-emerald font-bold' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50'">
                            🎉 Paket Sampai
                        </button>
                        <button @click="setCourierState('error')" 
                                class="px-3 py-1.5 rounded-xl border transition-all"
                                :class="courierState === 'error' ? 'bg-rose-600 text-white border-rose-600 font-bold' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50'">
                            ⚠️ Kendala Rute
                        </button>
                    </div>
                </div>

                <!-- Action Shortcut Buttons -->
                <div class="flex items-center gap-3 pt-2">
                    <button @click="currentTab = 'automation'" 
                            class="px-4 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-500 text-white text-xs font-bold flex items-center gap-2 shadow-glow-brand transition-all">
                        <i data-lucide="zap" class="w-4 h-4"></i>
                        Mulai Otomasi Muatan
                    </button>
                    <button @click="openAddStoreModal()" 
                            class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-xs font-bold flex items-center gap-2 transition-all shadow-sm">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        Tambah Channel Toko
                    </button>
                </div>
            </div>

            <!-- Right 5 Cols: REAL THREE.JS 3D CANVAS (Interactive Mascot) -->
            <div class="lg:col-span-5 h-[280px] sm:h-[320px] w-full relative canvas-3d-container bg-gradient-to-br from-amber-50/60 via-slate-50 to-orange-50/40 border border-slate-200 shadow-inner flex items-center justify-center">
                <div id="hero-3d-canvas" class="w-full h-full"></div>
                
                <!-- Overlay 3D Status Badge -->
                <div class="absolute bottom-3 left-3 right-3 flex items-center justify-between pointer-events-none text-[10px] font-mono text-slate-600 px-3 py-1.5 rounded-xl bg-white/85 backdrop-blur-md border border-slate-200 shadow-sm">
                    <span>STATE KURIR: <strong class="text-brand-600 uppercase" x-text="courierState"></strong></span>
                    <span class="text-emerald-700 font-bold">WebGL 3D Accelerated &bull; 60 FPS</span>
                </div>
            </div>

        </div>

        <!-- ========================================================================= -->
        <!-- MARKETPLACE MASCOT SHOWCASE (Toped Owl, Bimo Shopee, Laz Lion) -->
        <!-- ========================================================================= -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            
            <!-- Tokopedia Card -->
            <div class="card-surface p-4 rounded-3xl border transition-all duration-300 flex items-center gap-4 group cursor-pointer"
                 :class="'{{ $activeStore?->platform }}' === 'TOKOPEDIA' ? 'border-[#03AC0E] bg-emerald-50/30 shadow-glow-emerald ring-2 ring-[#03AC0E]/20' : 'border-slate-200 hover:border-emerald-300'"
                 @click="switchToPlatform('TOKOPEDIA')">
                <div class="w-16 h-16 rounded-2xl overflow-hidden bg-emerald-100 shrink-0 border border-emerald-200 shadow-sm">
                    <img src="{{ asset('assets/images/mascot_tokopedia.jpg') }}" alt="Toped Tokopedia" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between mb-0.5">
                        <span class="text-[10px] font-mono font-bold text-[#03AC0E] bg-emerald-100/70 px-2 py-0.5 rounded-full uppercase">Tokopedia</span>
                        <span class="w-2 h-2 rounded-full bg-[#03AC0E] animate-pulse"></span>
                    </div>
                    <h4 class="font-extrabold text-sm text-slate-900 truncate">Toped & Owl Courier</h4>
                    <p class="text-[11px] text-slate-500">Koneksi resmi FS ID & Shop API</p>
                </div>
            </div>

            <!-- Shopee Card -->
            <div class="card-surface p-4 rounded-3xl border transition-all duration-300 flex items-center gap-4 group cursor-pointer"
                 :class="'{{ $activeStore?->platform }}' === 'SHOPEE' ? 'border-[#EE4D2D] bg-orange-50/30 shadow-glow-orange ring-2 ring-[#EE4D2D]/20' : 'border-slate-200 hover:border-orange-300'"
                 @click="switchToPlatform('SHOPEE')">
                <div class="w-16 h-16 rounded-2xl overflow-hidden bg-orange-100 shrink-0 border border-orange-200 shadow-sm">
                    <img src="{{ asset('assets/images/mascot_shopee.jpg') }}" alt="Bimo Shopee" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between mb-0.5">
                        <span class="text-[10px] font-mono font-bold text-[#EE4D2D] bg-orange-100/70 px-2 py-0.5 rounded-full uppercase">Shopee</span>
                        <span class="w-2 h-2 rounded-full bg-[#EE4D2D] animate-pulse"></span>
                    </div>
                    <h4 class="font-extrabold text-sm text-slate-900 truncate">Bimo Shopee Express</h4>
                    <p class="text-[11px] text-slate-500">Partner Key & v2.product sync</p>
                </div>
            </div>

            <!-- Lazada Card -->
            <div class="card-surface p-4 rounded-3xl border transition-all duration-300 flex items-center gap-4 group cursor-pointer"
                 :class="'{{ $activeStore?->platform }}' === 'LAZADA' ? 'border-[#0055FF] bg-blue-50/30 shadow-glow-blue ring-2 ring-[#0055FF]/20' : 'border-slate-200 hover:border-blue-300'"
                 @click="switchToPlatform('LAZADA')">
                <div class="w-16 h-16 rounded-2xl overflow-hidden bg-blue-100 shrink-0 border border-blue-200 shadow-sm">
                    <img src="{{ asset('assets/images/mascot_lazada.jpg') }}" alt="Laz Lion" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between mb-0.5">
                        <span class="text-[10px] font-mono font-bold text-[#0055FF] bg-blue-100/70 px-2 py-0.5 rounded-full uppercase">Lazada</span>
                        <span class="w-2 h-2 rounded-full bg-[#0055FF] animate-pulse"></span>
                    </div>
                    <h4 class="font-extrabold text-sm text-slate-900 truncate">Laz Lion Tech Courier</h4>
                    <p class="text-[11px] text-slate-500">App Secret & REST Gateway</p>
                </div>
            </div>

        </div>

        <!-- ===================================================================== -->
        <!-- TAB 1: OVERVIEW / RINGKASAN -->
        <!-- ===================================================================== -->
        <div x-show="currentTab === 'overview'" x-transition class="space-y-8">
            
            <!-- 4 KPI Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <div class="card-surface p-5 rounded-2xl border border-slate-200">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-mono uppercase text-slate-500 font-bold">Total SKU Muatan</span>
                        <div class="p-2.5 rounded-xl bg-blue-50 text-blue-600"><i data-lucide="boxes" class="w-5 h-5"></i></div>
                    </div>
                    <div class="mt-4 flex items-baseline gap-2">
                        <span class="text-3xl font-extrabold text-slate-900">{{ $totalProducts }}</span>
                        <span class="text-xs text-slate-500">item listing</span>
                    </div>
                </div>

                <div class="card-surface p-5 rounded-2xl border border-slate-200">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-mono uppercase text-slate-500 font-bold">Teroptimasi AI</span>
                        <div class="p-2.5 rounded-xl bg-emerald-50 text-emerald-600"><i data-lucide="sparkles" class="w-5 h-5"></i></div>
                    </div>
                    <div class="mt-4 flex items-baseline gap-2">
                        <span class="text-3xl font-extrabold text-emerald-600">{{ $optimizedProducts }}</span>
                        <span class="text-xs text-slate-500">/ {{ $totalProducts }}</span>
                    </div>
                </div>

                <div class="card-surface p-5 rounded-2xl border border-slate-200">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-mono uppercase text-slate-500 font-bold">Dinormalkan / Revert</span>
                        <div class="p-2.5 rounded-xl bg-amber-50 text-amber-600"><i data-lucide="rotate-ccw" class="w-5 h-5"></i></div>
                    </div>
                    <div class="mt-4 flex items-baseline gap-2">
                        <span class="text-3xl font-extrabold text-amber-600">{{ $revertedProducts }}</span>
                        <span class="text-xs text-slate-500">siap di-optimize ulang</span>
                    </div>
                </div>

                <div class="card-surface p-5 rounded-2xl border border-slate-200">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-mono uppercase text-slate-500 font-bold">Channel Terhubung</span>
                        <div class="p-2.5 rounded-xl bg-brand-50 text-brand-600"><i data-lucide="store" class="w-5 h-5"></i></div>
                    </div>
                    <div class="mt-4 flex items-baseline gap-2">
                        <span class="text-3xl font-extrabold text-slate-900">{{ $stores->count() }}</span>
                        <span class="text-xs text-slate-500">toko online</span>
                    </div>
                </div>
            </div>

            <!-- Recent Batches Table -->
            <div class="card-surface p-6 rounded-3xl border border-slate-200 space-y-4 shadow-soft-card">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <i data-lucide="history" class="w-4 h-4 text-brand-600"></i>
                        Riwayat Pengiriman & Batch Terakhir
                    </h3>
                    <button @click="currentTab = 'automation'" class="text-xs font-mono font-bold text-brand-600 hover:text-brand-700">
                        Lihat Konsol Otomasi &rarr;
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-200 text-slate-500 font-mono text-[11px] uppercase bg-slate-50/50">
                                <th class="py-3 px-4">Batch ID / Target</th>
                                <th class="py-3 px-4">Jenis Otomasi</th>
                                <th class="py-3 px-4">Cakupan</th>
                                <th class="py-3 px-4">Hasil / Progress</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4">Waktu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-mono">
                            @forelse($recentBatches->take(5) as $b)
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="py-3.5 px-4">
                                        <span class="font-bold text-slate-900 block">{{ $b->target_name ?? $b->category_name ?? 'Otomasi' }}</span>
                                        <span class="text-[10px] text-slate-500">{{ substr($b->id, 0, 8) }}</span>
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-700">
                                        {{ match($b->automation_type) { 'stock_sync' => 'Sinkronisasi Stok', 'price_optimization' => 'Optimasi Harga', default => 'AI Copy & SEO' } }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $b->scope_type === 'single_product' ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-blue-100 text-blue-800 border border-blue-200' }}">
                                            {{ $b->scope_type === 'single_product' ? '1 Produk' : 'Kategori' }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 font-bold text-slate-900">
                                        <span class="text-emerald-600">{{ $b->success_count }}</span> / {{ $b->total_products }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ match($b->status) {
                                            'completed' => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
                                            'processing' => 'bg-amber-100 text-amber-800 border border-amber-200 animate-pulse',
                                            default => 'bg-slate-100 text-slate-700 border border-slate-200'
                                        } }}">
                                            {{ $b->status }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-500">{{ $b->created_at->diffForHumans() }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-slate-500">Belum ada batch otomasi terdaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- ===================================================================== -->
        <!-- TAB 2: KATALOG PRODUK (INDIVIDUAL PRODUCT ACTIONS) -->
        <!-- ===================================================================== -->
        <div x-show="currentTab === 'catalog'" x-transition class="space-y-6">
            
            <!-- Filters Bar -->
            <div class="card-surface p-4 sm:p-5 rounded-2xl border border-slate-200 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
                
                <!-- Search input -->
                <form action="{{ route('dashboard') }}" method="GET" class="flex-1 max-w-md flex items-center gap-2">
                    <input type="hidden" name="store_id" value="{{ $activeStore?->id }}">
                    @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
                    @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
                    
                    <div class="relative w-full">
                        <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Cari nama produk, SKU, atau keyword..."
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-10 pr-4 py-2 text-xs text-slate-800 focus:outline-none focus:border-brand-500 focus:bg-white transition-colors">
                    </div>
                    <button type="submit" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 transition-colors">
                        Cari
                    </button>
                </form>

                <!-- Filter Dropdowns -->
                <div class="flex items-center gap-2.5 flex-wrap">
                    <select onchange="window.location.href=this.value" 
                            class="bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-700 focus:outline-none focus:border-brand-500 font-mono shadow-sm">
                        <option value="?store_id={{ $activeStore?->id }}&status={{ request('status') }}&search={{ request('search') }}">
                            Semua Kategori ({{ count($categories) }})
                        </option>
                        @foreach($categories as $cat)
                            <option value="?store_id={{ $activeStore?->id }}&category={{ $cat['id'] }}&status={{ request('status') }}&search={{ request('search') }}"
                                    {{ request('category') === $cat['id'] ? 'selected' : '' }}>
                                {{ $cat['name'] }}
                            </option>
                        @endforeach
                    </select>

                    <select onchange="window.location.href=this.value" 
                            class="bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-700 focus:outline-none focus:border-brand-500 font-mono shadow-sm">
                        <option value="?store_id={{ $activeStore?->id }}&category={{ request('category') }}&search={{ request('search') }}">
                            Semua Status
                        </option>
                        <option value="?store_id={{ $activeStore?->id }}&status=success&category={{ request('category') }}&search={{ request('search') }}"
                                {{ request('status') === 'success' ? 'selected' : '' }}>
                            Teroptimasi AI
                        </option>
                        <option value="?store_id={{ $activeStore?->id }}&status=reverted&category={{ request('category') }}&search={{ request('search') }}"
                                {{ request('status') === 'reverted' ? 'selected' : '' }}>
                            Dinormalkan (Reverted)
                        </option>
                        <option value="?store_id={{ $activeStore?->id }}&status=pending&category={{ request('category') }}&search={{ request('search') }}"
                                {{ request('status') === 'pending' ? 'selected' : '' }}>
                            Belum Dioptimasi
                        </option>
                    </select>
                </div>
            </div>

            <!-- Products Grid: Individual Cards with Custom Actions -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($products as $product)
                    <div class="card-surface rounded-3xl border border-slate-200 overflow-hidden flex flex-col justify-between hover:border-slate-300 hover:shadow-lg transition-all duration-300 group">
                        
                        <!-- Top: Product Image & Badges -->
                        <div>
                            <div class="relative aspect-video w-full overflow-hidden bg-slate-100">
                                @php
                                    $imgs = is_array($product->image_urls) ? $product->image_urls : [];
                                    $firstImg = $imgs[0] ?? 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600&auto=format&fit=crop&q=80';
                                @endphp
                                <img src="{{ $firstImg }}" alt="{{ $product->original_title }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>

                                <div class="absolute top-3 left-3 flex items-center gap-1.5">
                                    <span class="px-2 py-1 rounded-md text-[10px] font-mono font-bold text-white shadow-md uppercase"
                                          style="background-color: {{ match($product->store->platform) { 'TOKOPEDIA' => '#03AC0E', 'SHOPEE' => '#EE4D2D', 'LAZADA' => '#0055FF', default => '#0F172A' } }}">
                                        {{ $product->store->platform }}
                                    </span>
                                    <span class="px-2 py-1 rounded-md text-[10px] font-mono font-bold shadow-md uppercase {{ match($product->status) {
                                        'success' => 'bg-emerald-600 text-white',
                                        'reverted' => 'bg-amber-500 text-white',
                                        'failed' => 'bg-rose-600 text-white',
                                        default => 'bg-slate-700 text-slate-100'
                                    } }}">
                                        {{ match($product->status) {
                                            'success' => 'AI OPTIMIZED',
                                            'reverted' => 'REVERTED (ASLI)',
                                            'failed' => 'GAGAL',
                                            default => 'BELUM OPTIMASI'
                                        } }}
                                    </span>
                                </div>

                                <!-- 3D Mascot Avatar Stamp per Marketplace -->
                                <div class="absolute top-3 right-3 w-9 h-9 rounded-full overflow-hidden border-2 border-white shadow-lg bg-white shrink-0 group-hover:scale-110 transition-transform"
                                     title="Mascot {{ $product->store->platform }}">
                                    <img src="{{ match($product->store->platform) {
                                        'TOKOPEDIA' => asset('assets/images/mascot_tokopedia.jpg'),
                                        'SHOPEE' => asset('assets/images/mascot_shopee.jpg'),
                                        'LAZADA' => asset('assets/images/mascot_lazada.jpg'),
                                        default => asset('assets/images/courier_hero.jpg')
                                    } }}" alt="{{ $product->store->platform }}" class="w-full h-full object-cover">
                                </div>

                                <div class="absolute bottom-3 left-3 right-3 text-[11px] font-mono text-white flex items-center justify-between">
                                    <span class="truncate font-semibold drop-shadow">{{ $product->category_name ?? 'Kategori Umum' }}</span>
                                    <span class="text-[10px] text-slate-200 shrink-0 drop-shadow">{{ $product->external_product_id }}</span>
                                </div>
                            </div>

                            <!-- Middle: Title -->
                            <div class="p-5 space-y-3">
                                <div>
                                    <span class="text-[10px] font-mono text-slate-400 uppercase tracking-wider block font-bold">JUDUL AKTIF DI MARKETPLACE</span>
                                    <h3 class="font-bold text-sm text-slate-900 line-clamp-2 mt-0.5 leading-snug">
                                        {{ $product->generated_title ?? $product->original_title }}
                                    </h3>
                                </div>

                                @if($product->status === 'success' && $product->generated_usps)
                                    <div class="pt-2 border-t border-slate-100">
                                        <span class="text-[10px] font-mono text-emerald-700 font-bold uppercase tracking-wider block mb-1">
                                            AI DETECTED USPS
                                        </span>
                                        <div class="flex flex-wrap gap-1">
                                            @foreach(array_slice($product->generated_usps, 0, 3) as $usp)
                                                <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-800 border border-emerald-200 text-[10px] font-medium">
                                                    &bull; {{ $usp }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Action Bar per Product Card -->
                        <div class="p-4 border-t border-slate-100 bg-slate-50/70 flex flex-col gap-2">
                            <div class="flex items-center gap-2">
                                <button @click="openDiffModal('{{ $product->id }}')" 
                                        class="h-10 flex-1 px-3 rounded-xl bg-white hover:bg-slate-100 border border-slate-200 text-slate-800 text-xs font-semibold flex items-center justify-center gap-1.5 transition-colors shadow-sm">
                                    <i data-lucide="eye" class="w-3.5 h-3.5 text-slate-500"></i>
                                    <span>Preview Diff</span>
                                </button>

                                <button @click="openEditModal('{{ $product->id }}')" 
                                        class="h-10 px-3 rounded-xl bg-white hover:bg-slate-100 border border-slate-200 text-slate-700 hover:text-slate-900 text-xs font-semibold flex items-center justify-center gap-1.5 transition-colors shadow-sm"
                                        title="Edit Deskripsi & Judul Khusus Produk Ini">
                                    <i data-lucide="edit-3" class="w-3.5 h-3.5 text-brand-600"></i>
                                    <span>Edit</span>
                                </button>

                                @if($product->status === 'success')
                                    <button @click="confirmRevert('{{ $product->id }}')" 
                                            class="h-10 w-10 rounded-xl bg-white hover:bg-rose-50 border border-slate-200 hover:border-rose-300 text-slate-500 hover:text-rose-600 flex items-center justify-center transition-colors shrink-0 shadow-sm"
                                            title="Normalkan kembali ke teks original (Bisa di-optimize ulang)">
                                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                                    </button>
                                @endif
                            </div>

                            <!-- Single Item AI Optimization Trigger -->
                            <button @click="triggerSingleProductOptimize('{{ $product->id }}')" 
                                    class="w-full h-9 rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 transition-all shadow-sm"
                                    :class="'{{ $product->status }}' === 'success' 
                                        ? 'bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-300' 
                                        : 'bg-brand-600 hover:bg-brand-500 text-white shadow-glow-brand'">
                                <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                                <span>{{ $product->status === 'success' ? 'Optimasi Ulang Produk Ini (AI)' : 'Optimasi AI Produk Ini' }}</span>
                            </button>
                        </div>

                    </div>
                @empty
                    <div class="col-span-full py-16 text-center card-surface rounded-3xl border border-slate-200 p-8">
                        <i data-lucide="package-search" class="w-12 h-12 text-slate-400 mx-auto mb-3"></i>
                        <h3 class="text-base font-bold text-slate-800">Tidak ada produk ditemukan</h3>
                        <p class="text-xs text-slate-500 mt-1">Sesuaikan kata kunci pencarian atau ganti filter kategori/status.</p>
                    </div>
                @endforelse
            </div>

            <div class="pt-6">
                {{ $products->links() }}
            </div>

        </div>

        <!-- ===================================================================== -->
        <!-- TAB 3: STUDIO OTOMASI & 3D ROAD DIORAMA PROGRESS -->
        <!-- ===================================================================== -->
        <div x-show="currentTab === 'automation'" x-transition class="space-y-8">
            
            <!-- Setup Form -->
            <div class="card-surface p-6 sm:p-8 rounded-3xl border border-slate-200 space-y-6 shadow-soft-card">
                
                <div class="border-b border-slate-200 pb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <span class="text-xs font-mono uppercase tracking-wider text-brand-600 font-bold">KONSOL PENGATURAN OTOMASI</span>
                        <h2 class="text-2xl font-extrabold text-slate-900 mt-1">Pilih Jenis & Cakupan Muatan</h2>
                    </div>

                    <div class="px-4 py-2 rounded-2xl bg-slate-50 border border-slate-200 flex items-center gap-3">
                        <span class="w-2.5 h-2.5 rounded-full"
                              style="background-color: {{ match($activeStore?->platform) { 'TOKOPEDIA' => '#03AC0E', 'SHOPEE' => '#EE4D2D', 'LAZADA' => '#0055FF', default => '#EA580C' } }}"></span>
                        <div class="text-xs">
                            <span class="text-slate-500 block text-[10px] uppercase font-mono">Toko Target:</span>
                            <strong class="text-slate-900 font-bold">{{ $activeStore?->store_name }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Automation Types -->
                <div class="space-y-3">
                    <label class="text-xs font-mono uppercase text-slate-500 font-bold block">01 // JENIS OTOMASI</label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        
                        <div @click="automationForm.type = 'ai_optimization'" 
                             class="p-4 rounded-2xl border cursor-pointer transition-all flex flex-col justify-between"
                             :class="automationForm.type === 'ai_optimization' ? 'bg-orange-50/50 border-brand-500 shadow-glow-brand ring-2 ring-brand-500/20' : 'bg-white border-slate-200 hover:border-slate-300'">
                            <div class="flex items-center justify-between mb-2">
                                <div class="p-2.5 rounded-xl bg-orange-100 text-brand-700"><i data-lucide="sparkles" class="w-5 h-5"></i></div>
                                <span class="w-4 h-4 rounded-full border flex items-center justify-center"
                                      :class="automationForm.type === 'ai_optimization' ? 'border-brand-500 bg-brand-500' : 'border-slate-300'">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white" x-show="automationForm.type === 'ai_optimization'"></span>
                                </span>
                            </div>
                            <h4 class="font-bold text-slate-900 text-sm">AI Copy & SEO Merchandising</h4>
                            <p class="text-[11px] text-slate-500 mt-1">Ekstraksi USP foto, formula judul SEO, dan deskripsi berkonversi tinggi.</p>
                        </div>

                        <div @click="automationForm.type = 'stock_sync'" 
                             class="p-4 rounded-2xl border cursor-pointer transition-all flex flex-col justify-between"
                             :class="automationForm.type === 'stock_sync' ? 'bg-blue-50/50 border-blue-500 shadow-glow-blue ring-2 ring-blue-500/20' : 'bg-white border-slate-200 hover:border-slate-300'">
                            <div class="flex items-center justify-between mb-2">
                                <div class="p-2.5 rounded-xl bg-blue-100 text-blue-700"><i data-lucide="refresh-cw" class="w-5 h-5"></i></div>
                                <span class="w-4 h-4 rounded-full border flex items-center justify-center"
                                      :class="automationForm.type === 'stock_sync' ? 'border-blue-500 bg-blue-500' : 'border-slate-300'">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white" x-show="automationForm.type === 'stock_sync'"></span>
                                </span>
                            </div>
                            <h4 class="font-bold text-slate-900 text-sm">Sinkronisasi Stok Real-Time</h4>
                            <p class="text-[11px] text-slate-500 mt-1">Penyelarasan inventaris lintas channel mencegah overselling.</p>
                        </div>

                        <div @click="automationForm.type = 'price_optimization'" 
                             class="p-4 rounded-2xl border cursor-pointer transition-all flex flex-col justify-between"
                             :class="automationForm.type === 'price_optimization' ? 'bg-emerald-50/50 border-emerald-500 shadow-glow-emerald ring-2 ring-emerald-500/20' : 'bg-white border-slate-200 hover:border-slate-300'">
                            <div class="flex items-center justify-between mb-2">
                                <div class="p-2.5 rounded-xl bg-emerald-100 text-emerald-700"><i data-lucide="tag" class="w-5 h-5"></i></div>
                                <span class="w-4 h-4 rounded-full border flex items-center justify-center"
                                      :class="automationForm.type === 'price_optimization' ? 'border-emerald-500 bg-emerald-500' : 'border-slate-300'">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white" x-show="automationForm.type === 'price_optimization'"></span>
                                </span>
                            </div>
                            <h4 class="font-bold text-slate-900 text-sm">Optimasi Harga & Margin Dinamis</h4>
                            <p class="text-[11px] text-slate-500 mt-1">Penyesuaian markup dan batas harga minimum otomatis.</p>
                        </div>

                    </div>
                </div>

                <!-- Scope Selection -->
                <div class="space-y-3 pt-2">
                    <label class="text-xs font-mono uppercase text-slate-500 font-bold block">02 // CAKUPAN PENERAPAN</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        
                        <div @click="automationForm.scope = 'single_product'; fetchPreview()" 
                             class="p-4 rounded-2xl border cursor-pointer transition-all"
                             :class="automationForm.scope === 'single_product' ? 'bg-amber-50/50 border-amber-500 ring-2 ring-amber-500/20' : 'bg-white border-slate-200 hover:border-slate-300'">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <div class="p-2 rounded-xl bg-amber-100 text-amber-700"><i data-lucide="box" class="w-5 h-5"></i></div>
                                    <div>
                                        <h4 class="font-bold text-slate-900 text-sm">Satu Produk Tertentu</h4>
                                        <p class="text-[11px] text-slate-500">Hanya proses 1 item terpilih</p>
                                    </div>
                                </div>
                                <span class="w-4 h-4 rounded-full border flex items-center justify-center"
                                      :class="automationForm.scope === 'single_product' ? 'border-amber-500 bg-amber-500' : 'border-slate-300'">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white" x-show="automationForm.scope === 'single_product'"></span>
                                </span>
                            </div>
                        </div>

                        <div @click="automationForm.scope = 'category'; fetchPreview()" 
                             class="p-4 rounded-2xl border cursor-pointer transition-all"
                             :class="automationForm.scope === 'category' ? 'bg-blue-50/50 border-blue-500 ring-2 ring-blue-500/20' : 'bg-white border-slate-200 hover:border-slate-300'">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <div class="p-2 rounded-xl bg-blue-100 text-blue-700"><i data-lucide="folder-tree" class="w-5 h-5"></i></div>
                                    <div>
                                        <h4 class="font-bold text-slate-900 text-sm">Satu Kategori Penuh</h4>
                                        <p class="text-[11px] text-slate-500">Berlaku untuk semua produk dalam kategori</p>
                                    </div>
                                </div>
                                <span class="w-4 h-4 rounded-full border flex items-center justify-center"
                                      :class="automationForm.scope === 'category' ? 'border-blue-500 bg-blue-500' : 'border-slate-300'">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white" x-show="automationForm.scope === 'category'"></span>
                                </span>
                            </div>
                        </div>

                    </div>

                    <!-- Picker -->
                    <div class="pt-2">
                        <div x-show="automationForm.scope === 'single_product'" class="space-y-2">
                            <label class="text-xs text-slate-700 font-semibold">Pilih Produk Target:</label>
                            <select x-model="automationForm.productId" @change="fetchPreview()"
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs text-slate-800 focus:outline-none focus:border-brand-500 font-mono shadow-sm">
                                <option value="">-- Pilih Satu Produk dari Daftar --</option>
                                @foreach($allStoreProducts as $sp)
                                    <option value="{{ $sp->id }}">{{ $sp->original_title }} (SKU: {{ $sp->external_product_id }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div x-show="automationForm.scope === 'category'" class="space-y-3">
                            <label class="text-xs text-slate-700 font-semibold">Pilih Kategori Target:</label>
                            <select x-model="automationForm.categoryId" @change="fetchPreview()"
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs text-slate-800 focus:outline-none focus:border-brand-500 font-mono shadow-sm">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $c)
                                    <option value="{{ $c['id'] }}">{{ $c['name'] }} ({{ $c['product_count'] ?? 0 }} Produk)</option>
                                @endforeach
                            </select>

                            <label class="flex items-center gap-2.5 cursor-pointer pt-1">
                                <input type="checkbox" x-model="automationForm.autoApplyNew" 
                                       class="w-4 h-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                                <span class="text-xs text-slate-600">
                                    <strong>Otomatisasi Berkelanjutan:</strong> Terapkan otomatis ke produk baru yang masuk ke kategori ini di masa mendatang.
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Impact Preview Card -->
                <div x-show="impactPreview.count > 0 || impactPreview.loading" 
                     class="p-5 rounded-2xl bg-orange-50/40 border border-brand-200 space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-brand-500 animate-ping"></span>
                            <span class="text-xs font-mono font-bold text-brand-700 uppercase tracking-wider">PRATINJAU DAMPAK MUATAN</span>
                        </div>
                        <span class="text-xs font-mono text-slate-500">Estimasi waktu: <strong class="text-slate-800" x-text="(impactPreview.count * 3) + ' detik'"></strong></span>
                    </div>

                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pt-2 border-t border-brand-100">
                        <div>
                            <span class="text-[11px] text-slate-500 uppercase font-mono block">Target Eksekusi:</span>
                            <h4 class="text-base font-bold text-slate-900" x-text="impactPreview.targetName"></h4>
                            <span class="text-xs text-slate-600 font-mono mt-0.5 block">
                                Cakupan: <strong class="text-brand-700" x-text="automationForm.scope === 'single_product' ? '1 Produk Spesifik' : 'Semua Produk dalam Kategori'"></strong>
                            </span>
                        </div>

                        <div class="px-4 py-2 rounded-xl bg-white border border-brand-200 text-center shadow-sm">
                            <span class="text-2xl font-black text-brand-600" x-text="impactPreview.count"></span>
                            <span class="text-[10px] text-slate-500 block font-mono font-bold">PAKET TERDAMPAK</span>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-200 flex items-center justify-end">
                    <button @click="executeAutomation()" 
                            :disabled="isSubmittingAutomation || (automationForm.scope === 'single_product' && !automationForm.productId) || (automationForm.scope === 'category' && !automationForm.categoryId)"
                            class="px-8 py-3.5 rounded-xl font-bold text-xs uppercase tracking-wider flex items-center gap-3 transition-all"
                            :class="(isSubmittingAutomation || (automationForm.scope === 'single_product' && !automationForm.productId) || (automationForm.scope === 'category' && !automationForm.categoryId))
                                ? 'bg-slate-200 text-slate-400 cursor-not-allowed'
                                : 'bg-brand-600 hover:bg-brand-500 text-white shadow-glow-brand cursor-pointer active:scale-95'">
                        <template x-if="isSubmittingAutomation">
                            <i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>
                        </template>
                        <template x-if="!isSubmittingAutomation">
                            <i data-lucide="zap" class="w-4 h-4"></i>
                        </template>
                        <span x-text="isSubmittingAutomation ? 'MEMBERANGKATKAN KURIR...' : 'KONFIRMASI & JALANKAN OTOMASI &rarr;'"></span>
                    </button>
                </div>

            </div>

            <!-- ========================================================================= -->
            <!-- REAL 3D ROAD DIORAMA PROGRESS & TELEMETRY STREAM -->
            <!-- ========================================================================= -->
            <div x-show="activeBatch" class="card-surface p-6 sm:p-8 rounded-3xl border border-brand-300 space-y-6 shadow-glow-brand">
                
                <div class="flex items-center justify-between font-mono text-xs flex-wrap gap-2">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-brand-500 animate-pulse"></span>
                        <span class="text-brand-700 font-bold">RUTE PENGIRIMAN 3D & TELEMETRI</span>
                        <span class="text-slate-500" x-text="'// BATCH: ' + (activeBatch?.id || 'CONNECTING')"></span>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="text-slate-600">PROGRESS: <strong class="text-slate-900" x-text="progress.processed + '/' + progress.total"></strong> PAKET</span>
                        <span class="text-brand-600 font-black text-sm" x-text="progress.percentage + '%'"></span>
                    </div>
                </div>

                <!-- 3D Road Highway Canvas -->
                <div class="h-44 w-full canvas-3d-container bg-slate-900 border border-slate-700 shadow-inner">
                    <div id="road-progress-canvas" class="w-full h-full"></div>
                </div>

                <!-- Checkpoints Labels with Rich Assets -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div class="p-3 rounded-2xl border transition-all flex items-center gap-3" :class="progress.percentage >= 25 ? 'bg-emerald-50 border-emerald-300 text-emerald-900 shadow-sm' : 'bg-white border-slate-200 text-slate-400'">
                        <img src="{{ asset('assets/images/courier_cargo.jpg') }}" alt="Gudang" class="w-12 h-12 rounded-xl object-cover border border-slate-200 shrink-0 shadow-sm">
                        <div class="min-w-0">
                            <span class="block font-bold text-xs font-mono truncate">POS 01: GUDANG</span>
                            <span class="text-[11px] text-slate-500 block truncate">Validasi Stok & Foto</span>
                        </div>
                    </div>
                    <div class="p-3 rounded-2xl border transition-all flex items-center gap-3" :class="progress.percentage >= 50 ? 'bg-emerald-50 border-emerald-300 text-emerald-900 shadow-sm' : 'bg-white border-slate-200 text-slate-400'">
                        <div class="w-12 h-12 rounded-xl bg-orange-100 text-brand-700 flex items-center justify-center border border-orange-200 shrink-0 relative overflow-hidden shadow-sm">
                            <i data-lucide="sparkles" class="w-6 h-6"></i>
                            <div class="absolute inset-x-0 h-0.5 bg-brand-500 scanner-line"></div>
                        </div>
                        <div class="min-w-0">
                            <span class="block font-bold text-xs font-mono truncate">POS 02: AI VISION</span>
                            <span class="text-[11px] text-slate-500 block truncate">Ekstraksi Poin USP</span>
                        </div>
                    </div>
                    <div class="p-3 rounded-2xl border transition-all flex items-center gap-3" :class="progress.percentage >= 75 ? 'bg-emerald-50 border-emerald-300 text-emerald-900 shadow-sm' : 'bg-white border-slate-200 text-slate-400'">
                        <img src="{{ asset('assets/images/courier_hero.jpg') }}" alt="Armada" class="w-12 h-12 rounded-xl object-cover border border-slate-200 shrink-0 shadow-sm">
                        <div class="min-w-0">
                            <span class="block font-bold text-xs font-mono truncate">POS 03: ARMADA</span>
                            <span class="text-[11px] text-slate-500 block truncate">Formula Judul SEO</span>
                        </div>
                    </div>
                    <div class="p-3 rounded-2xl border transition-all flex items-center gap-3" :class="progress.percentage >= 100 ? 'bg-emerald-50 border-emerald-300 text-emerald-900 shadow-sm' : 'bg-white border-slate-200 text-slate-400'">
                        <img src="{{ asset('assets/images/courier_celebrate.jpg') }}" alt="Selesai" class="w-12 h-12 rounded-xl object-cover border border-slate-200 shrink-0 shadow-sm">
                        <div class="min-w-0">
                            <span class="block font-bold text-xs font-mono truncate">POS 04: SUKSES</span>
                            <span class="text-[11px] text-slate-500 block truncate">Listing Live Synced</span>
                        </div>
                    </div>
                </div>

                <!-- Terminal Logs -->
                <div class="bg-slate-900 rounded-2xl border border-slate-800 p-4 max-h-44 overflow-y-auto font-mono text-xs space-y-1.5 text-slate-200"
                     x-ref="terminalBox">
                    <template x-for="log in logs" :key="log.id">
                        <div class="flex items-start gap-2">
                            <span class="text-slate-500 shrink-0" x-text="log.timestamp"></span>
                            <span class="shrink-0 font-bold"
                                  :class="{
                                      'text-emerald-400': log.type === 'success',
                                      'text-rose-400': log.type === 'error',
                                      'text-amber-300': log.type === 'info',
                                      'text-amber-400': log.type === 'warn'
                                  }" x-text="'[' + log.type.toUpperCase() + ']'"></span>
                            <span class="text-slate-200" x-text="log.message"></span>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Batches Table -->
            <div class="card-surface p-6 rounded-3xl border border-slate-200 space-y-4 shadow-soft-card">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Daftar Batch Otomasi Aktif & Riwayat</h3>
                        <p class="text-xs text-slate-500">Kelola atau batalkan batch otomasi yang sedang berjalan.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-200 text-slate-500 font-mono text-[11px] uppercase bg-slate-50/50">
                                <th class="py-3 px-4">Batch ID / Target</th>
                                <th class="py-3 px-4">Jenis Otomasi</th>
                                <th class="py-3 px-4">Cakupan</th>
                                <th class="py-3 px-4">Hasil / Progress</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-mono">
                            @forelse($recentBatches as $b)
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="py-3.5 px-4">
                                        <span class="font-bold text-slate-900 block">{{ $b->target_name ?? $b->category_name ?? 'Otomasi' }}</span>
                                        <span class="text-[10px] text-slate-500">{{ substr($b->id, 0, 8) }}</span>
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-700">
                                        {{ match($b->automation_type) { 'stock_sync' => 'Sinkronisasi Stok', 'price_optimization' => 'Optimasi Harga', default => 'AI Copy & SEO' } }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $b->scope_type === 'single_product' ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-blue-100 text-blue-800 border border-blue-200' }}">
                                            {{ $b->scope_type === 'single_product' ? '1 Produk' : 'Kategori' }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 font-bold text-slate-900">
                                        <span class="text-emerald-600">{{ $b->success_count }}</span> / {{ $b->total_products }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ match($b->status) {
                                            'completed' => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
                                            'processing' => 'bg-amber-100 text-amber-800 border border-amber-200 animate-pulse',
                                            'cancelled' => 'bg-rose-100 text-rose-800 border border-rose-200',
                                            default => 'bg-slate-100 text-slate-700 border border-slate-200'
                                        } }}">
                                            {{ $b->status }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-right">
                                        @if(in_array($b->status, ['processing', 'pending']))
                                            <button @click="confirmCancelBatch('{{ $b->id }}')" 
                                                    class="px-2.5 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-[10px] font-bold">
                                                Batalkan
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-slate-500">Belum ada batch otomasi terdaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- ===================================================================== -->
        <!-- TAB 4: KELOLA TOKO & KONEKSI MARKETPLACE -->
        <!-- ===================================================================== -->
        <div x-show="currentTab === 'stores'" x-transition class="space-y-6">
            
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-extrabold text-slate-900">Manajemen Toko Marketplace</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Kelola credential API dan status sinkronisasi multi-toko resmi Anda.</p>
                </div>
                <button @click="openAddStoreModal()" 
                        class="px-4 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-500 text-white text-xs font-bold flex items-center gap-2 shadow-glow-brand transition-all">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Hubungkan Toko Baru
                </button>
            </div>

            <!-- Stores Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($stores as $st)
                    <div class="card-surface p-6 rounded-3xl border transition-all duration-300 flex flex-col justify-between shadow-soft-card {{ $activeStore && $activeStore->id === $st->id ? 'border-brand-500 ring-2 ring-brand-500/20' : 'border-slate-200 hover:border-slate-300' }}">
                        <div class="space-y-4">
                            <!-- Mascot & Platform Header Banner -->
                            <div class="w-full h-24 rounded-2xl overflow-hidden relative border border-slate-200 shadow-sm group">
                                <img src="{{ match($st->platform) {
                                    'TOKOPEDIA' => asset('assets/images/mascot_tokopedia.jpg'),
                                    'SHOPEE' => asset('assets/images/mascot_shopee.jpg'),
                                    'LAZADA' => asset('assets/images/mascot_lazada.jpg'),
                                    default => asset('assets/images/courier_cargo.jpg')
                                } }}" alt="{{ $st->platform }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-slate-900/20 to-transparent flex items-end justify-between p-3">
                                    <span class="text-[11px] font-mono font-bold text-white uppercase">{{ $st->platform }} OFFICIAL CHANNEL</span>
                                    <span class="text-[10px] px-2 py-0.5 rounded-full font-bold bg-white/90 text-slate-800 backdrop-blur-sm">{{ $st->products()->count() }} SKU</span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-3 h-3 rounded-full shrink-0"
                                          style="background-color: {{ match($st->platform) { 'TOKOPEDIA' => '#03AC0E', 'SHOPEE' => '#EE4D2D', 'LAZADA' => '#0055FF', default => '#0F172A' } }}"></span>
                                    <span class="text-xs font-mono font-bold uppercase text-slate-700">{{ $st->platform }}</span>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase {{ $st->is_active ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $st->is_active ? 'TERHUBUNG' : 'NONAKTIF' }}
                                </span>
                            </div>

                            <div>
                                <h3 class="text-base font-extrabold text-slate-900">{{ $st->store_name }}</h3>
                                <p class="text-xs text-slate-500 font-mono mt-0.5">ID: {{ $st->id }}</p>
                            </div>

                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 space-y-2 text-xs font-mono">
                                <div class="flex items-center justify-between text-slate-600">
                                    <span>Total Katalog:</span>
                                    <strong class="text-slate-900 font-bold">{{ $st->products()->count() }} Produk</strong>
                                </div>
                                <div class="flex items-center justify-between text-slate-600">
                                    <span>Status Token:</span>
                                    <span class="text-emerald-700 font-semibold">Terenkripsi</span>
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-2">
                            <a href="?store_id={{ $st->id }}" 
                               class="px-3.5 py-2 rounded-xl text-xs font-bold transition-colors {{ $activeStore && $activeStore->id === $st->id ? 'bg-brand-600 text-white' : 'bg-slate-100 hover:bg-slate-200 text-slate-800' }}">
                                {{ $activeStore && $activeStore->id === $st->id ? 'Toko Aktif' : 'Pilih Toko Ini' }}
                            </a>
                            <button @click="confirmDisconnectStore('{{ $st->id }}', '{{ $st->store_name }}')" 
                                    class="p-2 text-slate-400 hover:text-rose-600 transition-colors" title="Putuskan Toko">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </main>

    <!-- ========================================================================= -->
    <!-- MODAL: DYNAMIC ADD STORE WITH REAL 3D FLOATING PACKAGE MATERIAL MORPH -->
    <!-- ========================================================================= -->
    <div x-show="showAddStoreModal" x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4"
         @keydown.escape.window="showAddStoreModal = false">
        <div class="card-surface bg-white border border-slate-200 w-full max-w-4xl max-h-[90vh] flex flex-col rounded-3xl shadow-2xl overflow-hidden"
             @click.away="showAddStoreModal = false">
            
            <div class="p-6 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-mono uppercase tracking-wider text-brand-600 font-bold">INTEGRASI CHANNEL BARU</span>
                    <h3 class="text-lg font-extrabold text-slate-900">Hubungkan Toko Marketplace</h3>
                </div>
                <button @click="showAddStoreModal = false" class="p-2 rounded-xl hover:bg-slate-100 text-slate-400 hover:text-slate-700">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form @submit.prevent="submitAddStore()" class="flex flex-col flex-1 overflow-hidden">
                <div class="p-6 overflow-y-auto space-y-6">
                    
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
                        
                        <!-- Left 7 Cols: Platform Selector & Quick Sandbox -->
                        <div class="lg:col-span-7 space-y-4">
                            <label class="text-xs font-mono uppercase text-slate-500 font-bold block">PILIH PLATFORM MARKETPLACE</label>
                            
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                                <template x-for="(plat, code) in marketplacePlatforms" :key="code">
                                    <button type="button" @click="selectPlatform(code)"
                                            class="p-3 rounded-2xl border text-left transition-all flex flex-col justify-between"
                                            :class="storeForm.platform === code ? 'border-brand-500 bg-orange-50/60 shadow-glow-brand ring-2 ring-brand-500/20' : 'border-slate-200 bg-slate-50 hover:border-slate-300'">
                                        <span class="w-3 h-3 rounded-full mb-2 block" :style="`background-color: ${plat.color}`"></span>
                                        <div>
                                            <h5 class="font-bold text-xs text-slate-900" x-text="plat.name"></h5>
                                            <span class="text-[9px] font-mono text-slate-500 block truncate" x-text="plat.badge"></span>
                                        </div>
                                    </button>
                                </template>
                            </div>

                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between gap-3">
                                <p class="text-xs text-slate-600" x-text="currentPlatformConfig?.description"></p>
                                <button type="button" @click="quickFillSandbox()" 
                                        class="px-3 py-1.5 rounded-xl bg-amber-100 hover:bg-amber-200 text-amber-900 border border-amber-300 text-[11px] font-bold font-mono shrink-0 transition-colors">
                                    ⚡ Isi Data Demo
                                </button>
                            </div>
                        </div>

                        <!-- Right 5 Cols: 3D Mascot Helper & REAL 3D FLOATING PACKAGE PROP -->
                        <div class="lg:col-span-5 flex flex-col gap-3">
                            <!-- 3D Mascot Helper Tip -->
                            <div class="p-3 rounded-2xl border flex items-center gap-3 transition-all"
                                 :class="{
                                     'bg-emerald-50 border-emerald-200 text-emerald-900': storeForm.platform === 'TOKOPEDIA',
                                     'bg-orange-50 border-orange-200 text-orange-900': storeForm.platform === 'SHOPEE',
                                     'bg-blue-50 border-blue-200 text-blue-900': storeForm.platform === 'LAZADA',
                                     'bg-slate-50 border-slate-200 text-slate-800': storeForm.platform === 'TIKTOK_SHOP'
                                 }">
                                <div class="w-12 h-12 rounded-xl overflow-hidden shrink-0 border border-white shadow-sm bg-white">
                                    <img :src="storeForm.platform === 'TOKOPEDIA' ? '{{ asset('assets/images/mascot_tokopedia.jpg') }}' : (storeForm.platform === 'SHOPEE' ? '{{ asset('assets/images/mascot_shopee.jpg') }}' : (storeForm.platform === 'LAZADA' ? '{{ asset('assets/images/mascot_lazada.jpg') }}' : '{{ asset('assets/images/courier_hero.jpg') }}'))"
                                         alt="Mascot Helper" class="w-full h-full object-cover">
                                </div>
                                <div class="text-[11px] leading-tight min-w-0">
                                    <span class="font-bold block truncate" x-text="storeForm.platform === 'TOKOPEDIA' ? 'Toped si Burung Hantu' : (storeForm.platform === 'SHOPEE' ? 'Bimo Kurir Shopee' : (storeForm.platform === 'LAZADA' ? 'Laz Lion Siaga' : 'Kurir Eskul AI'))"></span>
                                    <span class="text-slate-600 mt-0.5 block text-[10px] leading-snug" x-text="storeForm.platform === 'TOKOPEDIA' ? 'Siap bantu verifikasi FS ID & Shop ID Tokopedia!' : (storeForm.platform === 'SHOPEE' ? 'Siap kalkulasi HMAC SHA256 Partner Key Shopee!' : (storeForm.platform === 'LAZADA' ? 'Siap sinkronisasi App Key & Access Token Lazada!' : 'Koneksi multi-marketplace aman.'))"></span>
                                </div>
                            </div>

                            <!-- REAL 3D FLOATING PACKAGE PROP (PBR Material Morphing) -->
                            <div class="h-[155px] w-full canvas-3d-container bg-slate-900 border border-slate-700 relative flex items-center justify-center shadow-inner">
                                <div id="modal-package-canvas" class="w-full h-full"></div>
                                <div class="absolute bottom-2 text-center w-full pointer-events-none text-[10px] font-mono text-slate-300">
                                    <span>OBJEK PAKET 3D: <strong class="text-white uppercase" x-text="storeForm.platform"></strong> (Klik & Putar)</span>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Dynamic Input Fields from Config -->
                    <div class="space-y-4 pt-2 border-t border-slate-200">
                        <template x-for="field in currentPlatformConfig?.fields || []" :key="field.name">
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <label class="text-xs text-slate-800 font-semibold" x-text="field.label"></label>
                                    <span class="text-[10px] text-slate-400 font-mono" x-show="field.required">*Wajib</span>
                                </div>
                                <input :type="field.type === 'password' && showSecrets ? 'text' : field.type" 
                                       x-model="storeForm.fields[field.name]"
                                       :required="field.required"
                                       :placeholder="field.placeholder"
                                       class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-brand-500 focus:bg-white font-mono transition-colors">
                                <p class="text-[11px] text-slate-500 font-mono" x-text="field.help"></p>
                            </div>
                        </template>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="toggleSecret3d" x-model="showSecrets" class="w-4 h-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                        <label for="toggleSecret3d" class="text-xs text-slate-600 cursor-pointer">Tampilkan secret key / token</label>
                    </div>

                </div>

                <div class="p-6 border-t border-slate-200 flex items-center justify-between">
                    <button type="button" @click="showAddStoreModal = false" class="px-4 py-2 text-xs font-semibold text-slate-500 hover:text-slate-800">
                        Batal
                    </button>
                    <button type="submit" 
                            :disabled="isSubmittingStore"
                            class="px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs shadow-glow-brand flex items-center gap-2 transition-all">
                        <span x-show="isSubmittingStore" class="animate-spin"><i data-lucide="loader-2" class="w-4 h-4"></i></span>
                        <span x-text="isSubmittingStore ? 'Menghubungkan...' : 'Simpan & Otorisasi Toko'"></span>
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL: BEFORE-AFTER DIFF STUDIO -->
    <!-- ========================================================================= -->
    <div x-show="showDiffModal" x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4"
         @keydown.escape.window="showDiffModal = false">
        <div class="card-surface bg-white border border-slate-200 w-full max-w-4xl max-h-[90vh] flex flex-col rounded-3xl shadow-2xl overflow-hidden"
             @click.away="showDiffModal = false">
            
            <div class="p-6 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-mono uppercase tracking-wider text-brand-600 font-bold">STUDIO KOMPARASI BEFORE & AFTER</span>
                    <h3 class="text-lg font-extrabold text-slate-900" x-text="diffProduct?.original_title || 'Komparasi Listing'"></h3>
                </div>
                <button @click="showDiffModal = false" class="p-2 rounded-xl hover:bg-slate-100 text-slate-400 hover:text-slate-700">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <div class="p-6 overflow-y-auto space-y-6 text-xs font-mono">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                        <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                            <span class="font-bold text-slate-600 text-xs">01 // ORIGINAL MARKETPLACE</span>
                            <span class="px-2 py-0.5 rounded text-[10px] bg-slate-200 text-slate-700 font-bold">SEBELUM</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 uppercase block font-semibold">Judul Asli:</span>
                            <p class="text-slate-800 mt-1 font-sans text-sm font-semibold" x-text="diffProduct?.original_title"></p>
                        </div>
                        <div class="pt-2 border-t border-slate-200">
                            <span class="text-[10px] text-slate-400 uppercase block font-semibold">Deskripsi Asli:</span>
                            <pre class="text-slate-700 mt-1 font-sans text-xs whitespace-pre-wrap leading-relaxed max-h-60 overflow-y-auto" x-text="diffProduct?.original_desc"></pre>
                        </div>
                    </div>

                    <div class="p-5 rounded-2xl bg-orange-50/40 border border-brand-300 space-y-3 shadow-glow-brand">
                        <div class="flex items-center justify-between pb-2 border-b border-brand-200">
                            <span class="font-bold text-brand-700 text-xs">02 // AI MULTIMODAL OPTIMIZED</span>
                            <span class="px-2 py-0.5 rounded text-[10px] bg-brand-600 text-white font-bold">SESUDAH</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-brand-700 uppercase block font-semibold">Judul SEO Rekomendasi:</span>
                            <p class="text-emerald-700 mt-1 font-sans text-sm font-bold" x-text="diffProduct?.generated_title || '(Menunggu optimasi AI)'"></p>
                        </div>

                        <template x-if="diffProduct?.generated_usps && diffProduct.generated_usps.length">
                            <div class="pt-2 border-t border-brand-200">
                                <span class="text-[10px] text-brand-700 uppercase block font-semibold mb-1">Poin USP Ekstraksi Gambar:</span>
                                <div class="flex flex-wrap gap-1">
                                    <template x-for="usp in diffProduct.generated_usps" :key="usp">
                                        <span class="px-2 py-0.5 rounded bg-white text-brand-800 border border-brand-200 text-[10px] font-semibold" x-text="'&bull; ' + usp"></span>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <div class="pt-2 border-t border-brand-200">
                            <span class="text-[10px] text-brand-700 uppercase block font-semibold">Deskripsi Terstruktur:</span>
                            <pre class="text-slate-800 mt-1 font-sans text-xs whitespace-pre-wrap leading-relaxed max-h-60 overflow-y-auto" x-text="diffProduct?.generated_desc || '(Belum dibuat)'"></pre>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-6 border-t border-slate-200 flex items-center justify-between">
                <button @click="showDiffModal = false" class="px-4 py-2 text-xs font-semibold text-slate-500 hover:text-slate-800">Tutup</button>
                <div class="flex items-center gap-3">
                    <button @click="openEditModal(diffProduct.id); showDiffModal = false" 
                            class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-100 border border-slate-200 text-slate-800 text-xs font-bold flex items-center gap-1.5 transition-colors shadow-sm">
                        <i data-lucide="edit-3" class="w-4 h-4 text-brand-600"></i>
                        Edit Manual
                    </button>
                    <template x-if="diffProduct?.status === 'success'">
                        <button @click="confirmRevert(diffProduct.id); showDiffModal = false" 
                                class="px-4 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 border border-rose-200 text-rose-700 text-xs font-bold transition-colors">
                            Pulihkan Teks Asli (Revert)
                        </button>
                    </template>
                </div>
            </div>

        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL: EDIT PRODUCT CONTENT -->
    <!-- ========================================================================= -->
    <div x-show="showEditModal" x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4"
         @keydown.escape.window="showEditModal = false">
        <div class="card-surface bg-white border border-slate-200 w-full max-w-2xl max-h-[90vh] flex flex-col rounded-3xl shadow-2xl overflow-hidden"
             @click.away="showEditModal = false">
            
            <div class="p-6 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-mono uppercase tracking-wider text-brand-600 font-bold">EDITOR KONTEN INDIVIDUAL</span>
                    <h3 class="text-lg font-extrabold text-slate-900">Edit Judul & Deskripsi Produk</h3>
                </div>
                <button @click="showEditModal = false" class="p-2 rounded-xl hover:bg-slate-100 text-slate-400 hover:text-slate-700">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form @submit.prevent="submitEditContent()" class="flex flex-col flex-1 overflow-hidden">
                <div class="p-6 overflow-y-auto space-y-5">
                    <div class="space-y-1.5">
                        <label class="text-xs text-slate-800 font-semibold">Judul Produk (Marketplace Title):</label>
                        <input type="text" x-model="editForm.title" required
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-brand-500 focus:bg-white font-sans transition-colors">
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs text-slate-800 font-semibold">Poin USP Keunggulan (Satu per baris):</label>
                        <textarea x-model="editForm.usps" rows="3"
                                  class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs text-slate-900 focus:outline-none focus:border-brand-500 focus:bg-white font-sans leading-relaxed"></textarea>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs text-slate-800 font-semibold">Deskripsi Lengkap Produk:</label>
                        <textarea x-model="editForm.desc" rows="8" required
                                  class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs text-slate-900 focus:outline-none focus:border-brand-500 focus:bg-white font-sans leading-relaxed"></textarea>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs text-slate-800 font-semibold">SEO Keywords (Pisahkan dengan koma):</label>
                        <input type="text" x-model="editForm.keywords" 
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-brand-500 focus:bg-white font-sans">
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="syncCheck3d" x-model="editForm.syncToMarketplace" class="w-4 h-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                        <label for="syncCheck3d" class="text-xs text-slate-700 cursor-pointer">
                            Sinkronkan langsung perubahan ini ke listing marketplace
                        </label>
                    </div>
                </div>

                <div class="p-6 border-t border-slate-200 flex items-center justify-between">
                    <button type="button" @click="showEditModal = false" class="px-4 py-2 text-xs font-semibold text-slate-500 hover:text-slate-800">Batal</button>
                    <button type="submit" :disabled="isSubmittingEdit"
                            class="px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs shadow-glow-brand flex items-center gap-2 transition-all">
                        <span x-show="isSubmittingEdit" class="animate-spin"><i data-lucide="loader-2" class="w-4 h-4"></i></span>
                        <span x-text="isSubmittingEdit ? 'Menyimpan...' : 'Simpan & Perbarui Listing'"></span>
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL: GEMINI API KEY -->
    <!-- ========================================================================= -->
    <div x-show="showApiModal" x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4"
         @keydown.escape.window="showApiModal = false">
        <div class="card-surface bg-white border border-slate-200 w-full max-w-md p-6 rounded-3xl shadow-2xl space-y-5"
             @click.away="showApiModal = false">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-base text-slate-900">Google Gemini Multimodal Vision</h3>
                <button @click="showApiModal = false" class="text-slate-400 hover:text-slate-700"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <p class="text-xs text-slate-600">Koneksikan API key Google AI Studio Anda (<code class="text-brand-600 font-bold">gemini-1.5-flash</code>).</p>
            <form @submit.prevent="submitApiKey()" class="space-y-4">
                <input type="password" x-model="apiKeyInput" placeholder="AIzaSy..."
                       class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-900 font-mono focus:outline-none focus:border-brand-500 focus:bg-white">
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="showApiModal = false" class="px-4 py-2 text-xs font-semibold text-slate-500 hover:text-slate-800">Tutup</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs shadow-glow-brand transition-all">Simpan Key</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL: CONFIRM DIALOG -->
    <!-- ========================================================================= -->
    <div x-show="confirmDialog.show" x-cloak 
         class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4"
         @keydown.escape.window="confirmDialog.show = false">
        <div class="card-surface bg-white border border-slate-200 w-full max-w-sm p-6 rounded-3xl shadow-2xl space-y-4"
             @click.away="confirmDialog.show = false">
            <div class="flex items-center gap-3">
                <div class="p-2.5 rounded-xl bg-rose-50 text-rose-600 shrink-0"><i data-lucide="help-circle" class="w-6 h-6"></i></div>
                <div>
                    <h4 class="font-bold text-slate-900 text-sm" x-text="confirmDialog.title"></h4>
                    <p class="text-xs text-slate-600 mt-0.5" x-text="confirmDialog.message"></p>
                </div>
            </div>
            <div class="flex justify-end gap-2.5 pt-3 border-t border-slate-100">
                <button type="button" @click="confirmDialog.show = false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-xs font-semibold text-slate-700">Batal</button>
                <button type="button" @click="confirmDialog.onConfirm(); confirmDialog.show = false" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-xs font-bold text-white shadow-lg">Lanjutkan</button>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 3D ENGINE & APP LOGIC -->
    <!-- ========================================================================= -->
    <script>
        function eskul3DApp() {
            return {
                currentTab: 'overview',
                activeStoreId: '{{ $activeStore?->id }}',
                marketplacePlatforms: @json($marketplacePlatforms),
                currentPlatformConfig: null,

                // 3D Instances & States
                courierState: 'idle', // 'idle' | 'driving' | 'success' | 'error'
                heroScene: null,
                packageScene: null,
                roadScene: null,

                // Modals
                showDiffModal: false,
                showAddStoreModal: false,
                showEditModal: false,
                showApiModal: false,
                showSecrets: false,
                apiKeyInput: '{{ env('GEMINI_API_KEY', '') }}',

                toasts: [],
                confirmDialog: { show: false, title: '', message: '', onConfirm: () => {} },

                activeBatch: null,
                progress: { total: 0, processed: 0, success: 0, failed: 0, percentage: 0 },
                logs: [],
                eventSource: null,

                storeForm: { platform: 'TOKOPEDIA', fields: {} },
                isSubmittingStore: false,

                automationForm: {
                    type: 'ai_optimization',
                    scope: 'category',
                    categoryId: '{{ $categories[0]['id'] ?? '' }}',
                    productId: '{{ $allStoreProducts->first()?->id ?? '' }}',
                    autoApplyNew: true,
                },
                isSubmittingAutomation: false,
                impactPreview: { loading: false, count: 0, targetName: '' },

                diffProduct: null,
                editForm: { productId: '', title: '', desc: '', usps: '', keywords: '', syncToMarketplace: true },
                isSubmittingEdit: false,

                initApp() {
                    this.selectPlatform('TOKOPEDIA');
                    this.$nextTick(() => {
                        if (window.lucide) window.lucide.createIcons();
                        this.fetchPreview();

                        // Initialize Procedural 3D WebGL Engines
                        this.initHeroCourier3D();
                        this.initRoadProgress3D();
                    });
                },

                // Switch store by platform code directly from Mascot cards
                switchToPlatform(code) {
                    const stores = @json($stores);
                    const matchedStore = stores.find(s => s.platform === code);
                    if (matchedStore) {
                        window.location.href = '?store_id=' + matchedStore.id;
                    } else {
                        this.openAddStoreModal();
                        this.selectPlatform(code);
                    }
                },

                // =============================================================
                // 3D ENGINE 1: HERO COURIER & CARGO SCOOTER (Procedural WebGL)
                // =============================================================
                initHeroCourier3D() {
                    const container = document.getElementById('hero-3d-canvas');
                    if (!container || typeof THREE === 'undefined') return;

                    const width = container.clientWidth;
                    const height = container.clientHeight;

                    const scene = new THREE.Scene();
                    const camera = new THREE.PerspectiveCamera(40, width / height, 0.1, 100);
                    camera.position.set(0, 1.2, 4.0);
                    camera.lookAt(0, 0.5, 0);

                    const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true, powerPreference: 'high-performance' });
                    renderer.setSize(width, height);
                    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
                    renderer.shadowMap.enabled = true;
                    renderer.shadowMap.type = THREE.PCFSoftShadowMap;
                    container.innerHTML = '';
                    container.appendChild(renderer.domElement);

                    // Studio Lights (Clean warm studio look)
                    const ambient = new THREE.AmbientLight(0xffffff, 0.9);
                    scene.add(ambient);

                    const keyLight = new THREE.DirectionalLight(0xfff7ed, 1.3);
                    keyLight.position.set(3, 5, 3);
                    keyLight.castShadow = true;
                    scene.add(keyLight);

                    const rimLight = new THREE.PointLight(0xf59e0b, 1.5, 10);
                    rimLight.position.set(-3, 2, -2);
                    scene.add(rimLight);

                    // Ground shadow receiver plane
                    const shadowPlaneGeo = new THREE.PlaneGeometry(8, 8);
                    const shadowPlaneMat = new THREE.ShadowMaterial({ opacity: 0.18 });
                    const shadowPlane = new THREE.Mesh(shadowPlaneGeo, shadowPlaneMat);
                    shadowPlane.rotation.x = -Math.PI / 2;
                    shadowPlane.position.y = 0;
                    shadowPlane.receiveShadow = true;
                    scene.add(shadowPlane);

                    // =========================================================
                    // Procedural Low-Poly 3D Electric Cargo Scooter & Courier
                    // Warm Delivery Orange / Marketplace Color Matching
                    // =========================================================
                    const vehicleGroup = new THREE.Group();

                    // Materials: No purple! Warm delivery orange + dark metals
                    const chassisColor = '{{ $activeStore?->platform }}' === 'TOKOPEDIA' ? 0x03AC0E : ('{{ $activeStore?->platform }}' === 'LAZADA' ? 0x0055FF : 0xEA580C);
                    const chassisMat = new THREE.MeshStandardMaterial({ color: chassisColor, roughness: 0.3, metalness: 0.4 });
                    const metalMat = new THREE.MeshStandardMaterial({ color: 0x334155, roughness: 0.2, metalness: 0.8 });
                    const rubberMat = new THREE.MeshStandardMaterial({ color: 0x1e293b, roughness: 0.8 });
                    const parcelMat = new THREE.MeshStandardMaterial({ color: 0xF97316, roughness: 0.3, metalness: 0.1 });
                    const courierJacketMat = new THREE.MeshStandardMaterial({ color: 0xEA580C, roughness: 0.6 });
                    const helmetMat = new THREE.MeshStandardMaterial({ color: 0xffffff, roughness: 0.2, metalness: 0.1 });
                    const visorMat = new THREE.MeshStandardMaterial({ color: 0x0f172a, roughness: 0.1, metalness: 0.8 });

                    // 1. Scooter Chassis Base
                    const chassisGeo = new THREE.BoxGeometry(0.55, 0.15, 1.6);
                    const chassis = new THREE.Mesh(chassisGeo, chassisMat);
                    chassis.position.y = 0.35;
                    chassis.castShadow = true;
                    vehicleGroup.add(chassis);

                    // 2. Wheels (Front & Rear)
                    const wheelGeo = new THREE.CylinderGeometry(0.22, 0.22, 0.12, 16);
                    wheelGeo.rotateZ(Math.PI / 2);

                    const frontWheel = new THREE.Mesh(wheelGeo, rubberMat);
                    frontWheel.position.set(0, 0.22, 0.7);
                    frontWheel.castShadow = true;
                    vehicleGroup.add(frontWheel);

                    const rearWheel = new THREE.Mesh(wheelGeo, rubberMat);
                    rearWheel.position.set(0, 0.22, -0.65);
                    rearWheel.castShadow = true;
                    vehicleGroup.add(rearWheel);

                    // 3. Handlebar & Steering Stem
                    const stemGeo = new THREE.CylinderGeometry(0.03, 0.03, 0.9, 8);
                    const stem = new THREE.Mesh(stemGeo, metalMat);
                    stem.position.set(0, 0.75, 0.55);
                    stem.rotation.x = -0.15;
                    stem.castShadow = true;
                    vehicleGroup.add(stem);

                    const barGeo = new THREE.BoxGeometry(0.7, 0.04, 0.04);
                    const bar = new THREE.Mesh(barGeo, metalMat);
                    bar.position.set(0, 1.15, 0.5);
                    vehicleGroup.add(bar);

                    // Headlight
                    const lampGeo = new THREE.CylinderGeometry(0.07, 0.07, 0.05, 12);
                    lampGeo.rotateX(Math.PI / 2);
                    const lampMat = new THREE.MeshStandardMaterial({ color: 0xffffff, emissive: 0xfef08a });
                    const lamp = new THREE.Mesh(lampGeo, lampMat);
                    lamp.position.set(0, 1.05, 0.55);
                    vehicleGroup.add(lamp);

                    // 4. Rear Cargo Trunk & Parcel
                    const cargoBoxGeo = new THREE.BoxGeometry(0.55, 0.45, 0.55);
                    const cargoBox = new THREE.Mesh(cargoBoxGeo, metalMat);
                    cargoBox.position.set(0, 0.65, -0.55);
                    cargoBox.castShadow = true;
                    vehicleGroup.add(cargoBox);

                    const parcelGeo = new THREE.BoxGeometry(0.48, 0.35, 0.48);
                    const parcel = new THREE.Mesh(parcelGeo, parcelMat);
                    parcel.position.set(0, 1.0, -0.55);
                    parcel.castShadow = true;
                    vehicleGroup.add(parcel);

                    // 5. Courier Mascot (Body, Arms, Head, Helmet)
                    const courierGroup = new THREE.Group();

                    // Torso
                    const bodyGeo = new THREE.BoxGeometry(0.38, 0.48, 0.28);
                    const body = new THREE.Mesh(bodyGeo, courierJacketMat);
                    body.position.set(0, 0.95, -0.05);
                    body.castShadow = true;
                    courierGroup.add(body);

                    // Head & Helmet
                    const headGroup = new THREE.Group();
                    headGroup.position.set(0, 1.35, -0.02);

                    const helmetGeo = new THREE.SphereGeometry(0.20, 16, 16);
                    const helmet = new THREE.Mesh(helmetGeo, helmetMat);
                    helmet.castShadow = true;
                    headGroup.add(helmet);

                    const visorGeo = new THREE.BoxGeometry(0.24, 0.09, 0.14);
                    const visor = new THREE.Mesh(visorGeo, visorMat);
                    visor.position.set(0, 0.02, 0.12);
                    headGroup.add(visor);

                    courierGroup.add(headGroup);

                    // Arms
                    const armGeo = new THREE.BoxGeometry(0.08, 0.08, 0.5);
                    const leftArm = new THREE.Mesh(armGeo, courierJacketMat);
                    leftArm.position.set(-0.25, 1.05, 0.2);
                    leftArm.rotation.x = -0.3;
                    courierGroup.add(leftArm);

                    const rightArm = new THREE.Mesh(armGeo, courierJacketMat);
                    rightArm.position.set(0.25, 1.05, 0.2);
                    rightArm.rotation.x = -0.3;
                    courierGroup.add(rightArm);

                    vehicleGroup.add(courierGroup);

                    // Position vehicle in scene with subtle angle
                    vehicleGroup.rotation.y = Math.PI / 5;
                    scene.add(vehicleGroup);

                    // Mouse Parallax Engine
                    const mouse = { x: 0, y: 0, targetX: 0, targetY: 0 };
                    window.addEventListener('mousemove', (e) => {
                        mouse.targetX = (e.clientX / window.innerWidth) * 2 - 1;
                        mouse.targetY = -(e.clientY / window.innerHeight) * 2 + 1;
                    });

                    // Store references
                    this.heroScene = {
                        scene, camera, renderer, vehicleGroup, frontWheel, rearWheel, 
                        headGroup, parcel, mouse, clock: new THREE.Clock()
                    };

                    // Animation Loop
                    const animate = () => {
                        requestAnimationFrame(animate);
                        const delta = this.heroScene.clock.getDelta();
                        const time = this.heroScene.clock.getElapsedTime();

                        // Mouse lerp
                        mouse.x += (mouse.targetX - mouse.x) * 0.05;
                        mouse.y += (mouse.targetY - mouse.y) * 0.05;

                        // Head tracking cursor
                        headGroup.rotation.y = mouse.x * 0.6;
                        headGroup.rotation.x = -mouse.y * 0.4;

                        // State-driven behaviors
                        if (this.courierState === 'idle') {
                            vehicleGroup.position.y = Math.sin(time * 2.5) * 0.02;
                            parcel.rotation.y = 0;
                            parcel.scale.set(1, 1, 1);
                        } else if (this.courierState === 'driving') {
                            frontWheel.rotation.x += delta * 18;
                            rearWheel.rotation.x += delta * 18;
                            vehicleGroup.position.y = Math.sin(time * 16) * 0.025;
                            vehicleGroup.rotation.z = Math.sin(time * 8) * 0.02;
                        } else if (this.courierState === 'success') {
                            vehicleGroup.position.y = 0.05 + Math.abs(Math.sin(time * 4)) * 0.08;
                            parcel.scale.set(1.15, 1.15, 1.15);
                            parcel.rotation.y = time * 2;
                        } else if (this.courierState === 'error') {
                            vehicleGroup.rotation.z = -0.15;
                            headGroup.rotation.z = Math.sin(time * 3) * 0.15;
                        }

                        renderer.render(scene, camera);
                    };
                    animate();

                    // Resize handler
                    window.addEventListener('resize', () => {
                        if (!container) return;
                        const w = container.clientWidth;
                        const h = container.clientHeight;
                        camera.aspect = w / h;
                        camera.updateProjectionMatrix();
                        renderer.setSize(w, h);
                    });
                },

                setCourierState(state) {
                    this.courierState = state;
                },

                // =============================================================
                // 3D ENGINE 2: FLOATING 3D PACKAGE PROP (In Add Store Modal)
                // =============================================================
                initPackageModal3D() {
                    const container = document.getElementById('modal-package-canvas');
                    if (!container || typeof THREE === 'undefined') return;

                    const width = container.clientWidth || 320;
                    const height = container.clientHeight || 190;

                    const scene = new THREE.Scene();
                    const camera = new THREE.PerspectiveCamera(40, width / height, 0.1, 100);
                    camera.position.set(0, 0.2, 3.2);

                    const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
                    renderer.setSize(width, height);
                    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
                    container.innerHTML = '';
                    container.appendChild(renderer.domElement);

                    // Lights
                    const ambient = new THREE.AmbientLight(0xffffff, 1.0);
                    scene.add(ambient);

                    const light = new THREE.DirectionalLight(0xffffff, 1.6);
                    light.position.set(3, 4, 3);
                    scene.add(light);

                    // Package 3D Mesh
                    const boxGeo = new THREE.BoxGeometry(1.0, 0.8, 1.0);
                    const packageMat = new THREE.MeshStandardMaterial({
                        color: 0x03AC0E, // Tokopedia default green
                        roughness: 0.85,
                        metalness: 0.05
                    });
                    const packageMesh = new THREE.Mesh(boxGeo, packageMat);
                    scene.add(packageMesh);

                    // Cross tape stripes
                    const tapeMat = new THREE.MeshStandardMaterial({ color: 0xffffff, roughness: 0.4 });
                    const tape1Geo = new THREE.BoxGeometry(1.02, 0.82, 0.18);
                    const tape1 = new THREE.Mesh(tape1Geo, tapeMat);
                    packageMesh.add(tape1);

                    const tape2Geo = new THREE.BoxGeometry(0.18, 0.82, 1.02);
                    const tape2 = new THREE.Mesh(tape2Geo, tapeMat);
                    packageMesh.add(tape2);

                    this.packageScene = { scene, camera, renderer, packageMesh, packageMat, clock: new THREE.Clock() };

                    const animate = () => {
                        if (this.packageScene) {
                            requestAnimationFrame(animate);
                            const t = this.packageScene.clock.getElapsedTime();
                            packageMesh.rotation.y += 0.015;
                            packageMesh.rotation.x = Math.sin(t * 1.5) * 0.12;
                            packageMesh.position.y = Math.sin(t * 2.5) * 0.08;
                            renderer.render(scene, camera);
                        }
                    };
                    animate();

                    this.updatePackage3DMaterial(this.storeForm.platform);
                },

                updatePackage3DMaterial(platformCode) {
                    if (!this.packageScene || !this.packageScene.packageMat) return;

                    const mat = this.packageScene.packageMat;
                    const mesh = this.packageScene.packageMesh;

                    if (platformCode === 'TOKOPEDIA') {
                        mat.color.setHex(0x03AC0E);
                        mat.roughness = 0.85;
                        mat.metalness = 0.05;
                    } else if (platformCode === 'SHOPEE') {
                        mat.color.setHex(0xEE4D2D);
                        mat.roughness = 0.15;
                        mat.metalness = 0.10;
                    } else if (platformCode === 'LAZADA') {
                        mat.color.setHex(0x0055FF);
                        mat.roughness = 0.35;
                        mat.metalness = 0.85;
                    } else {
                        mat.color.setHex(0x0F172A);
                        mat.roughness = 0.5;
                    }

                    // 360 spin pulse on switch
                    if (mesh) {
                        mesh.rotation.y += Math.PI;
                    }
                },

                // =============================================================
                // 3D ENGINE 3: MINI ROAD DIORAMA PROGRESS (Studio Otomasi)
                // =============================================================
                initRoadProgress3D() {
                    const container = document.getElementById('road-progress-canvas');
                    if (!container || typeof THREE === 'undefined') return;

                    const width = container.clientWidth || 600;
                    const height = container.clientHeight || 176;

                    const scene = new THREE.Scene();
                    const camera = new THREE.PerspectiveCamera(35, width / height, 0.1, 100);
                    camera.position.set(0, 2.5, 4.5);
                    camera.lookAt(0, 0, 0);

                    const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
                    renderer.setSize(width, height);
                    container.innerHTML = '';
                    container.appendChild(renderer.domElement);

                    const ambient = new THREE.AmbientLight(0xffffff, 0.9);
                    scene.add(ambient);

                    const light = new THREE.DirectionalLight(0xffffff, 1.2);
                    light.position.set(2, 4, 3);
                    scene.add(light);

                    // 3D Road Plane
                    const roadGeo = new THREE.BoxGeometry(6.0, 0.08, 1.4);
                    const roadMat = new THREE.MeshStandardMaterial({ color: 0x1e293b, roughness: 0.9 });
                    const road = new THREE.Mesh(roadGeo, roadMat);
                    scene.add(road);

                    // Road stripes
                    for (let i = -2.4; i <= 2.4; i += 0.8) {
                        const stripeGeo = new THREE.BoxGeometry(0.4, 0.09, 0.06);
                        const stripeMat = new THREE.MeshStandardMaterial({ color: 0xffffff });
                        const stripe = new THREE.Mesh(stripeGeo, stripeMat);
                        stripe.position.set(i, 0.005, 0);
                        scene.add(stripe);
                    }

                    // 4 Waypoint Checkpoint Beacons
                    const beacons = [];
                    const beaconPositions = [-2.2, -0.7, 0.8, 2.2];
                    beaconPositions.forEach((x, idx) => {
                        const beaconGeo = new THREE.CylinderGeometry(0.1, 0.14, 0.35, 12);
                        const beaconMat = new THREE.MeshStandardMaterial({ color: 0x475569, emissive: 0x000000 });
                        const beacon = new THREE.Mesh(beaconGeo, beaconMat);
                        beacon.position.set(x, 0.2, 0.55);
                        scene.add(beacon);
                        beacons.push({ mesh: beacon, mat: beaconMat });
                    });

                    // Mini 3D Courier moving on X axis (-2.4 to +2.4)
                    // Warm delivery orange instead of purple!
                    const miniCourier = new THREE.Group();
                    const mcBody = new THREE.Mesh(new THREE.BoxGeometry(0.24, 0.2, 0.16), new THREE.MeshStandardMaterial({ color: 0xEA580C }));
                    mcBody.position.y = 0.16;
                    miniCourier.add(mcBody);

                    const mcParcel = new THREE.Mesh(new THREE.BoxGeometry(0.18, 0.15, 0.15), new THREE.MeshStandardMaterial({ color: 0xF97316 }));
                    mcParcel.position.set(-0.12, 0.28, 0);
                    miniCourier.add(mcParcel);

                    miniCourier.position.set(-2.4, 0.05, 0);
                    scene.add(miniCourier);

                    this.roadScene = { scene, camera, renderer, miniCourier, beacons };

                    const animate = () => {
                        requestAnimationFrame(animate);
                        // Update mini courier X position smoothly based on progress percentage
                        const targetX = -2.4 + ((this.progress.percentage / 100) * 4.8);
                        miniCourier.position.x += (targetX - miniCourier.position.x) * 0.1;

                        // Checkpoint lights activation
                        beacons.forEach((b, idx) => {
                            const threshold = (idx + 1) * 25;
                            if (this.progress.percentage >= threshold) {
                                b.mat.color.setHex(0x10b981);
                                b.mat.emissive.setHex(0x059669);
                            } else {
                                b.mat.color.setHex(0x475569);
                                b.mat.emissive.setHex(0x000000);
                            }
                        });

                        renderer.render(scene, camera);
                    };
                    animate();
                },

                // Notifications
                notify(message, type = 'success', title = '') {
                    const id = Math.random().toString(36).substring(7);
                    title = title || (type === 'success' ? 'Sukses' : (type === 'error' ? 'Gagal' : 'Informasi'));
                    this.toasts.push({ id, message, type, title });
                    this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
                    setTimeout(() => this.removeToast(id), 4500);
                },

                removeToast(id) {
                    this.toasts = this.toasts.filter(t => t.id !== id);
                },

                // Platform selection in Modal
                selectPlatform(code) {
                    this.storeForm.platform = code;
                    this.currentPlatformConfig = this.marketplacePlatforms[code] || null;
                    this.storeForm.fields = {};
                    if (this.currentPlatformConfig?.fields) {
                        this.currentPlatformConfig.fields.forEach(f => {
                            this.storeForm.fields[f.name] = '';
                        });
                    }
                    this.updatePackage3DMaterial(code);
                },

                quickFillSandbox() {
                    const sandbox = this.currentPlatformConfig?.sandbox;
                    if (sandbox) {
                        for (let k in sandbox) {
                            this.storeForm.fields[k] = sandbox[k];
                        }
                        this.notify('Data demo ' + this.currentPlatformConfig.name + ' berhasil diisi.', 'info');
                    }
                },

                openAddStoreModal() {
                    this.showAddStoreModal = true;
                    this.$nextTick(() => {
                        if (!this.packageScene) {
                            this.initPackageModal3D();
                        } else {
                            this.updatePackage3DMaterial(this.storeForm.platform);
                        }
                    });
                },

                async submitAddStore() {
                    this.isSubmittingStore = true;
                    try {
                        const res = await fetch('/api/marketplace/connect', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(this.storeForm)
                        });
                        const data = await res.json();
                        if (res.ok && data.success) {
                            this.notify('Toko ' + data.data.store_name + ' berhasil dihubungkan!', 'success');
                            this.showAddStoreModal = false;
                            setTimeout(() => {
                                window.location.href = '?store_id=' + data.data.id;
                            }, 800);
                        } else {
                            this.notify(data.message || 'Gagal menghubungkan toko', 'error', 'Error Validasi');
                        }
                    } catch (e) {
                        this.notify('Terjadi kesalahan jaringan.', 'error');
                    } finally {
                        this.isSubmittingStore = false;
                    }
                },

                confirmDisconnectStore(storeId, storeName) {
                    this.confirmDialog = {
                        show: true,
                        title: 'Putuskan Hubungan Toko?',
                        message: 'Toko "' + storeName + '" beserta seluruh credential akan dihapus dari sistem Eskul AI.',
                        onConfirm: async () => {
                            try {
                                const res = await fetch(`/api/marketplace/disconnect/${storeId}`, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                        'Accept': 'application/json'
                                    }
                                });
                                const data = await res.json();
                                if (res.ok && data.success) {
                                    this.notify('Toko berhasil diputuskan.', 'success');
                                    setTimeout(() => window.location.reload(), 600);
                                } else {
                                    this.notify(data.message || 'Gagal menghapus toko', 'error');
                                }
                            } catch (e) {
                                this.notify('Gagal berkomunikasi dengan server.', 'error');
                            }
                        }
                    };
                },

                // Dynamic live impact preview
                async fetchPreview() {
                    if (!this.activeStoreId) return;
                    this.impactPreview.loading = true;
                    try {
                        const params = new URLSearchParams({
                            store_id: this.activeStoreId,
                            scope: this.automationForm.scope,
                            category_id: this.automationForm.categoryId || '',
                            product_id: this.automationForm.productId || '',
                        });
                        const res = await fetch(`/api/automation/preview?${params.toString()}`);
                        const data = await res.json();
                        if (data.success) {
                            this.impactPreview = {
                                loading: false,
                                count: data.count,
                                targetName: data.target_name
                            };
                        }
                    } catch (e) {
                        this.impactPreview.loading = false;
                    }
                },

                // Execute batch automation with 3D road visualization
                async executeAutomation() {
                    if (!this.activeStoreId) {
                        this.notify('Pilih toko terlebih dahulu sebelum memulai otomasi.', 'warning');
                        return;
                    }
                    this.isSubmittingAutomation = true;
                    this.setCourierState('driving');

                    try {
                        const payload = {
                            store_id: this.activeStoreId,
                            automation_type: this.automationForm.type,
                            scope: this.automationForm.scope,
                            product_id: this.automationForm.productId,
                            category_id: this.automationForm.categoryId,
                            auto_apply_new: this.automationForm.autoApplyNew
                        };

                        const res = await fetch('/api/automation/start', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(payload)
                        });

                        const data = await res.json();

                        if (res.ok && data.success) {
                            this.activeBatch = data.batch;
                            this.progress = {
                                total: data.batch.total_products,
                                processed: 0,
                                success: 0,
                                failed: 0,
                                percentage: 0
                            };
                            this.logs = [];
                            this.notify('Batch otomasi dimulai. Membuka stream telemetri 3D...', 'info');

                            this.$nextTick(() => {
                                if (!this.roadScene) this.initRoadProgress3D();
                            });

                            this.startTelemetryStream(data.batch.id);
                        } else {
                            this.notify(data.message || 'Gagal memulai otomasi', 'error');
                            this.setCourierState('error');
                        }
                    } catch (e) {
                        this.notify('Koneksi jaringan terputus.', 'error');
                        this.setCourierState('error');
                    } finally {
                        this.isSubmittingAutomation = false;
                    }
                },

                // Telemetry SSE Stream
                startTelemetryStream(batchId) {
                    if (this.eventSource) {
                        this.eventSource.close();
                    }

                    this.eventSource = new EventSource(`/api/automation/stream/${batchId}`);

                    this.eventSource.onmessage = (e) => {
                        try {
                            const evt = JSON.parse(e.data);
                            this.handleStreamEvent(evt);
                        } catch (err) {
                            console.error('SSE JSON error', err);
                        }
                    };

                    this.eventSource.onerror = (err) => {
                        console.warn('SSE disconnected or completed');
                        if (this.eventSource) {
                            this.eventSource.close();
                            this.eventSource = null;
                        }
                    };
                },

                handleStreamEvent(evt) {
                    const now = new Date().toLocaleTimeString();

                    if (evt.event === 'progress') {
                        this.progress.processed = evt.processed;
                        this.progress.total = evt.total;
                        this.progress.percentage = evt.percentage;

                        this.logs.unshift({
                            id: Math.random(),
                            timestamp: now,
                            type: 'info',
                            message: `[${evt.product_sku}] ${evt.step}: ${evt.message}`
                        });
                    } else if (evt.event === 'product_success') {
                        this.progress.success++;
                        this.logs.unshift({
                            id: Math.random(),
                            timestamp: now,
                            type: 'success',
                            message: `Berhasil diproses & disinkronkan: ${evt.product_title}`
                        });
                    } else if (evt.event === 'product_failed') {
                        this.progress.failed++;
                        this.logs.unshift({
                            id: Math.random(),
                            timestamp: now,
                            type: 'error',
                            message: `Gagal diproses (${evt.product_sku}): ${evt.error}`
                        });
                    } else if (evt.event === 'batch_completed') {
                        this.setCourierState('success');
                        this.notify('Pengiriman selesai! Semua muatan teroptimasi.', 'success', 'Batch Selesai');
                        this.logs.unshift({
                            id: Math.random(),
                            timestamp: now,
                            type: 'success',
                            message: '--- SEMUA TUGAS OTOMASI SELESAI ---'
                        });
                        if (this.eventSource) {
                            this.eventSource.close();
                            this.eventSource = null;
                        }
                    } else if (evt.event === 'batch_cancelled') {
                        this.setCourierState('error');
                        this.notify('Batch dibatalkan oleh pengguna.', 'warning');
                        if (this.eventSource) {
                            this.eventSource.close();
                            this.eventSource = null;
                        }
                    }
                },

                confirmCancelBatch(batchId) {
                    this.confirmDialog = {
                        show: true,
                        title: 'Batalkan Batch Otomasi?',
                        message: 'Proses background yang belum berjalan akan dihentikan seketika.',
                        onConfirm: async () => {
                            try {
                                const res = await fetch(`/api/automation/cancel/${batchId}`, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                        'Accept': 'application/json'
                                    }
                                });
                                const data = await res.json();
                                if (res.ok && data.success) {
                                    this.notify('Batch berhasil dibatalkan.', 'warning');
                                    this.setCourierState('error');
                                    setTimeout(() => window.location.reload(), 1000);
                                }
                            } catch (e) {
                                this.notify('Gagal membatalkan batch.', 'error');
                            }
                        }
                    };
                },

                // Single product optimization trigger
                async triggerSingleProductOptimize(productId) {
                    this.notify('Memulai AI Vision multimodal untuk produk ini...', 'info');
                    this.setCourierState('driving');
                    try {
                        const res = await fetch('/api/automation/optimize-single', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ product_id: productId })
                        });
                        const data = await res.json();
                        if (res.ok && data.success) {
                            this.setCourierState('success');
                            this.notify('Produk berhasil dioptimasi dengan AI!', 'success');
                            setTimeout(() => window.location.reload(), 1200);
                        } else {
                            this.setCourierState('error');
                            this.notify(data.message || 'Gagal mengoptimasi produk', 'error');
                        }
                    } catch (e) {
                        this.setCourierState('error');
                        this.notify('Terjadi kesalahan jaringan.', 'error');
                    }
                },

                // Product Revert (Kembalikan ke asli, status reverted -> siap di-optimize ulang)
                confirmRevert(productId) {
                    this.confirmDialog = {
                        show: true,
                        title: 'Normalkan Kembali ke Teks Original?',
                        message: 'Judul dan deskripsi yang digenerate AI akan dikosongkan kembali ke data asli toko. Anda tetap bisa mengoptimasinya ulang kapan saja.',
                        onConfirm: async () => {
                            try {
                                const res = await fetch(`/api/products/${productId}/revert`, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                        'Accept': 'application/json'
                                    }
                                });
                                const data = await res.json();
                                if (res.ok && data.success) {
                                    this.notify('Listing dinormalkan ke original. Siap dioptimasi ulang.', 'success');
                                    setTimeout(() => window.location.reload(), 1000);
                                } else {
                                    this.notify(data.message || 'Gagal menormalkan produk', 'error');
                                }
                            } catch (e) {
                                this.notify('Gagal memproses revert.', 'error');
                            }
                        }
                    };
                },

                // Diff preview modal
                async openDiffModal(productId) {
                    try {
                        const res = await fetch(`/api/products/${productId}/diff`);
                        const data = await res.json();
                        if (data.success) {
                            this.diffProduct = data.product;
                            this.showDiffModal = true;
                            this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
                        } else {
                            this.notify('Gagal memuat data diff komparasi.', 'error');
                        }
                    } catch (e) {
                        this.notify('Terjadi kesalahan jaringan.', 'error');
                    }
                },

                // Edit product content
                async openEditModal(productId) {
                    try {
                        const res = await fetch(`/api/products/${productId}/diff`);
                        const data = await res.json();
                        if (data.success) {
                            const p = data.product;
                            this.editForm = {
                                productId: p.id,
                                title: p.generated_title || p.original_title,
                                desc: p.generated_desc || p.original_desc,
                                usps: Array.isArray(p.generated_usps) ? p.generated_usps.join('\n') : '',
                                keywords: Array.isArray(p.generated_keywords) ? p.generated_keywords.join(', ') : '',
                                syncToMarketplace: true,
                            };
                            this.showEditModal = true;
                        }
                    } catch (e) {
                        this.notify('Gagal memuat data editor.', 'error');
                    }
                },

                async submitEditContent() {
                    this.isSubmittingEdit = true;
                    try {
                        const res = await fetch('/api/products/update-content', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(this.editForm)
                        });
                        const data = await res.json();
                        if (res.ok && data.success) {
                            this.notify('Konten produk berhasil diperbarui!', 'success');
                            this.showEditModal = false;
                            setTimeout(() => window.location.reload(), 1000);
                        } else {
                            this.notify(data.message || 'Gagal menyimpan perubahan', 'error');
                        }
                    } catch (e) {
                        this.notify('Terjadi kesalahan jaringan.', 'error');
                    } finally {
                        this.isSubmittingEdit = false;
                    }
                },

                async submitApiKey() {
                    try {
                        const res = await fetch('/api/settings/gemini-key', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ key: this.apiKeyInput })
                        });
                        const data = await res.json();
                        if (res.ok && data.success) {
                            this.notify('Gemini API Key tersimpan!', 'success');
                            this.showApiModal = false;
                            setTimeout(() => window.location.reload(), 800);
                        } else {
                            this.notify(data.message || 'Gagal menyimpan key', 'error');
                        }
                    } catch (e) {
                        this.notify('Gagal berkomunikasi dengan server.', 'error');
                    }
                }

            };
        }
    </script>
</body>
</html>
