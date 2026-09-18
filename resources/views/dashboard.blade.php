<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AutoCopy AI — Shop The Future / Intelligent Commerce</title>
    
    <!-- Google Fonts: Space Grotesk (Editorial Display), Inter (Body), JetBrains Mono (Technical/Metadata) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (Play CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'sans-serif'],
                        display: ['Space Grotesk', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    colors: {
                        canvas: {
                            base: '#F2F0EA',
                            secondary: '#E7E4DC',
                            dark: '#111111',
                            darkSurface: '#181818',
                            darkElevated: '#222222',
                        },
                        editorial: {
                            primary: '#111111',
                            secondary: '#5D5A54',
                            muted: '#8C887F',
                            lightText: '#F5F3ED',
                            lightMuted: '#A6A298',
                            border: '#D8D4CA',
                            darkBorder: '#2C2C2C',
                        },
                        accent: {
                            orange: '#FF4D00',
                            orangeHover: '#E04400',
                            orangeLight: 'rgba(255, 77, 0, 0.08)',
                        }
                    },
                    boxShadow: {
                        'editorial-sm': '0 2px 8px rgba(0,0,0,0.04)',
                        'editorial-md': '0 10px 30px rgba(0,0,0,0.06)',
                        'editorial-lg': '0 20px 50px rgba(0,0,0,0.1)',
                        'orange-glow': '0 0 30px rgba(255, 77, 0, 0.35)',
                    }
                }
            }
        }
    </script>
    
    <!-- Three.js CDN for Interactive 3D Art -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    
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
        
        /* Editorial Clean Scrollbar */
        ::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }
        ::-webkit-scrollbar-track {
            background: #F2F0EA;
        }
        ::-webkit-scrollbar-thumb {
            background: #D8D4CA;
            border-radius: 0px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #111111;
        }

        /* Subtle Organic Film Grain Overlay */
        .film-grain {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            pointer-events: none;
            z-index: 99;
            opacity: 0.028;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.8' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E");
        }

        /* Custom Interactive Studio Cursor */
        @media (min-width: 1024px) {
            body {
                cursor: default;
            }
            .custom-cursor {
                pointer-events: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 14px;
                height: 14px;
                border-radius: 50%;
                background-color: #FF4D00;
                transform: translate(-50%, -50%);
                transition: width 0.25s ease, height 0.25s ease, background-color 0.25s ease, transform 0.08s ease-out;
                z-index: 9999;
                mix-blend-mode: normal;
                display: flex;
                align-items: center;
                justify-content: center;
                font-family: 'Space Grotesk', sans-serif;
                font-size: 9px;
                font-weight: 700;
                letter-spacing: 0.05em;
                color: #FFFFFF;
            }
            .custom-cursor.is-hover {
                width: 64px;
                height: 64px;
                background-color: #111111;
                box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            }
            .custom-cursor.is-orange-hover {
                width: 72px;
                height: 72px;
                background-color: #FF4D00;
                box-shadow: 0 0 30px rgba(255, 77, 0, 0.4);
            }
        }

        /* Masked Text Reveal Animation */
        .text-mask-wrapper {
            overflow: hidden;
            display: inline-block;
        }
        .text-mask-child {
            transform: translateY(115%);
            transition: transform 0.9s cubic-bezier(0.16, 1, 0.3, 1);
            will-change: transform;
        }
        .text-mask-child.is-visible {
            transform: translateY(0%);
        }

        /* Editorial Line Reveal */
        .line-reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.8s cubic-bezier(0.16, 1, 0.3, 1), transform 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .line-reveal.in-view {
            opacity: 1;
            transform: translateY(0);
        }

        /* Custom Horizontal Scroll Bar hiding */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Custom Pagination */
        nav[role="navigation"] {
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: #5D5A54;
            font-size: 0.875rem;
            width: 100%;
        }
        nav[role="navigation"] span.relative,
        nav[role="navigation"] a.relative {
            background-color: #F2F0EA !important;
            border: 1px solid #D8D4CA !important;
            color: #111111 !important;
            border-radius: 0px;
            padding: 0.45rem 0.9rem;
            font-size: 0.75rem;
            font-family: 'JetBrains Mono', monospace;
            font-weight: 600;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        nav[role="navigation"] span[aria-current="page"] span {
            background-color: #111111 !important;
            color: #F5F3ED !important;
            font-weight: 700 !important;
            border-color: #111111 !important;
        }
        nav[role="navigation"] a:hover {
            background-color: #FF4D00 !important;
            color: #FFFFFF !important;
            border-color: #FF4D00 !important;
        }
    </style>
</head>
<body class="bg-[#F2F0EA] text-[#111111] min-h-screen font-sans antialiased selection:bg-[#FF4D00] selection:text-white relative overflow-x-hidden"
      x-data="creativeEcommerceApp()"
      x-init="initApp()"
      @mousemove="handleCursor($event)">

    <!-- Film Grain Overlay -->
    <div class="film-grain"></div>

    <!-- Interactive Custom Cursor (Desktop Only) -->
    <div id="custom-cursor" class="custom-cursor hidden lg:flex" :class="cursorClass">
        <span x-text="cursorText"></span>
    </div>

    <!-- ========================================================================= -->
    <!-- 1. MINIMAL EDITORIAL NAVIGATION BAR -->
    <!-- ========================================================================= -->
    <header class="sticky top-0 z-50 bg-[#F2F0EA]/90 backdrop-blur-md border-b border-[#D8D4CA] transition-all duration-300">
        <div class="max-w-[1440px] mx-auto px-6 sm:px-10 h-20 flex items-center justify-between">
            
            <!-- Left: Brand / Studio Identity -->
            <a href="{{ route('dashboard') }}" 
               @mouseenter="setCursor('AI', 'is-orange-hover')" 
               @mouseleave="resetCursor()"
               class="flex items-center gap-3 group">
                <div class="w-8 h-8 bg-[#111111] text-[#F5F3ED] flex items-center justify-center font-display font-bold text-xs tracking-wider group-hover:bg-[#FF4D00] transition-colors duration-300">
                    AC
                </div>
                <div class="flex flex-col">
                    <span class="font-display font-bold text-base tracking-tight uppercase group-hover:text-[#FF4D00] transition-colors">
                        AUTOCOPY / AI
                    </span>
                    <span class="text-[9px] font-mono tracking-widest uppercase text-[#8C887F]">
                        CREATIVE COMMERCE OS
                    </span>
                </div>
            </a>

            <!-- Center: Editorial Navigation Links -->
            <nav class="hidden md:flex items-center gap-8 font-mono text-xs tracking-wider uppercase font-semibold">
                <a href="#discovery" 
                   @mouseenter="setCursor('EXPLORE', 'is-hover')" 
                   @mouseleave="resetCursor()"
                   class="text-[#5D5A54] hover:text-[#FF4D00] transition-colors flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#FF4D00]"></span>
                    01 // DISCOVERY
                </a>
                <a href="#curated-catalog" 
                   @mouseenter="setCursor('VIEW', 'is-hover')" 
                   @mouseleave="resetCursor()"
                   class="text-[#5D5A54] hover:text-[#111111] transition-colors">
                    02 // CATALOG
                </a>
                <a href="#ai-workspace" 
                   @mouseenter="setCursor('ENGINE', 'is-orange-hover')" 
                   @mouseleave="resetCursor()"
                   class="text-[#5D5A54] hover:text-[#FF4D00] transition-colors">
                    03 // AI WORKSPACE
                </a>
                <a href="#studio-footer" 
                   @mouseenter="setCursor('INFO', 'is-hover')" 
                   @mouseleave="resetCursor()"
                   class="text-[#5D5A54] hover:text-[#111111] transition-colors">
                    04 // STUDIO
                </a>
            </nav>

            <!-- Right: Store Channel Switcher & Gemini API Status -->
            <div class="flex items-center gap-3 font-mono text-xs">
                
                <!-- Gemini Multimodal API Status -->
                <button @click="showApiModal = true" 
                        @mouseenter="setCursor('KEY', 'is-hover')" 
                        @mouseleave="resetCursor()"
                        class="h-9 px-3 border border-[#D8D4CA] hover:border-[#111111] bg-white flex items-center gap-2 transition-all active:scale-95">
                    <span class="w-2 h-2 rounded-full {{ $hasGeminiKey ? 'bg-[#FF4D00]' : 'bg-[#D8D4CA]' }}"></span>
                    <span class="hidden sm:inline text-[11px] font-semibold">GEMINI VISION</span>
                    <span class="text-[10px] text-[#5D5A54]">{{ $hasGeminiKey ? 'ACTIVE' : 'SETUP' }}</span>
                </button>

                <!-- Store Channel Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" 
                            @mouseenter="setCursor('STORE', 'is-hover')" 
                            @mouseleave="resetCursor()"
                            class="h-9 px-3.5 bg-[#111111] text-[#F5F3ED] hover:bg-[#FF4D00] flex items-center gap-2 transition-colors active:scale-95">
                        <span class="text-[10px] uppercase font-bold tracking-wider">{{ $activeStore ? $activeStore->platform : 'STORE' }}</span>
                        <span class="max-w-[100px] truncate text-[11px] font-medium hidden sm:inline">{{ $activeStore ? $activeStore->store_name : 'Select' }}</span>
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5 transition-transform" :class="{ 'rotate-180': open }"></i>
                    </button>

                    <div x-show="open" @click.away="open = false" x-cloak 
                         class="absolute right-0 mt-2 w-64 bg-[#111111] text-[#F5F3ED] border border-[#2C2C2C] shadow-editorial-lg p-2 z-50">
                        <div class="px-2 py-1.5 text-[9px] font-mono uppercase tracking-widest text-[#8C887F]">
                            CONNECTED STORE CHANNELS
                        </div>
                        <div class="space-y-1 mt-1">
                            @foreach($stores as $st)
                                <a href="?store_id={{ $st->id }}" 
                                   class="flex items-center justify-between px-3 py-2 text-xs font-mono transition-colors {{ $activeStore && $activeStore->id === $st->id ? 'bg-[#FF4D00] text-white font-bold' : 'text-[#A6A298] hover:bg-[#1E1E1E] hover:text-white' }}">
                                    <span class="truncate">{{ $st->store_name }}</span>
                                    <span class="text-[9px] px-1 py-0.5 uppercase bg-black/40">{{ $st->platform }}</span>
                                </a>
                            @endforeach
                        </div>
                        <div class="border-t border-[#2C2C2C] mt-2 pt-2">
                            <button @click="open = false; showConnectModal = true" 
                                    class="w-full flex items-center gap-2 px-2.5 py-1.5 text-xs text-[#FF4D00] hover:bg-[#FF4D00]/10 font-bold transition-colors">
                                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                                CONNECT NEW CHANNEL
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Bag/Cart Indicator (Editorial touch) -->
                <div class="h-9 px-3 border border-[#D8D4CA] bg-white hidden lg:flex items-center font-bold text-xs tracking-wider">
                    CART ({{ $totalProducts }})
                </div>

            </div>
        </div>
    </header>

    <!-- ========================================================================= -->
    <!-- 2. HERO SECTION: ASYMMETRICAL EDITORIAL COMPOSITION + 3D INTERACTIVE ART -->
    <!-- ========================================================================= -->
    <section class="max-w-[1440px] mx-auto px-6 sm:px-10 pt-12 pb-24 border-b border-[#D8D4CA]">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <!-- Left 7 Cols: Massive Headline & Editorial Statement -->
            <div class="lg:col-span-7 space-y-8">
                
                <!-- Eyebrow Badge -->
                <div class="flex items-center gap-3">
                    <span class="px-2.5 py-1 bg-[#111111] text-[#F5F3ED] font-mono text-[10px] tracking-widest uppercase font-bold">
                        AI COMMERCE / 01
                    </span>
                    <span class="text-xs font-mono text-[#5D5A54] uppercase tracking-wider">
                        AUTONOMOUS CATALOG CREATION
                    </span>
                </div>

                <!-- Oversized Editorial Typography with Masked Animation -->
                <h1 class="font-display font-extrabold text-5xl sm:text-6xl lg:text-7xl xl:text-8xl tracking-tighter leading-[0.92] text-[#111111] uppercase">
                    <div class="text-mask-wrapper">
                        <div class="text-mask-child" :class="{ 'is-visible': isLoaded }">
                            SHOP
                        </div>
                    </div><br>
                    <div class="text-mask-wrapper">
                        <div class="text-mask-child" :class="{ 'is-visible': isLoaded }" style="transition-delay: 150ms;">
                            THE
                        </div>
                    </div><br>
                    <div class="text-mask-wrapper">
                        <div class="text-mask-child text-[#FF4D00]" :class="{ 'is-visible': isLoaded }" style="transition-delay: 300ms;">
                            FUTURE.
                        </div>
                    </div>
                </h1>

                <!-- Subtitle -->
                <p class="text-base sm:text-lg text-[#5D5A54] max-w-xl font-normal leading-relaxed">
                    An intelligent commerce experience built around multimodal vision discovery, automated SEO synthesis, and high-conversion merchandising.
                </p>

                <!-- Action Controls (Magnetic / Studio Style) -->
                <div class="flex flex-wrap items-center gap-4 pt-4 font-mono text-xs font-bold uppercase tracking-wider">
                    <a href="#curated-catalog" 
                       @mouseenter="setCursor('VIEW', 'is-orange-hover')" 
                       @mouseleave="resetCursor()"
                       class="h-14 px-8 bg-[#111111] hover:bg-[#FF4D00] text-[#F5F3ED] flex items-center gap-3 transition-all duration-300 shadow-editorial-md group">
                        <span>EXPLORE PRODUCTS</span>
                        <span class="transition-transform duration-300 group-hover:translate-x-2">&rarr;</span>
                    </a>
                    
                    <a href="#ai-workspace" 
                       @mouseenter="setCursor('RUN', 'is-hover')" 
                       @mouseleave="resetCursor()"
                       class="h-14 px-7 border-2 border-[#111111] hover:border-[#FF4D00] hover:text-[#FF4D00] text-[#111111] bg-transparent flex items-center gap-3 transition-all duration-300">
                        <i data-lucide="sparkles" class="w-4 h-4 text-[#FF4D00]"></i>
                        <span>AI CATALOG ENGINE</span>
                    </a>
                </div>

                <!-- Quick Metadata Row -->
                <div class="grid grid-cols-3 gap-6 pt-6 border-t border-[#D8D4CA] font-mono text-xs">
                    <div>
                        <span class="text-[10px] text-[#8C887F] uppercase block">ACTIVE INVENTORY</span>
                        <span class="font-display font-bold text-xl text-[#111111]">{{ $totalProducts }} SKUs</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-[#8C887F] uppercase block">AI MERCHANDISED</span>
                        <span class="font-display font-bold text-xl text-[#FF4D00]">{{ $optimizedProducts }} Items</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-[#8C887F] uppercase block">SYNC RATE</span>
                        <span class="font-display font-bold text-xl text-[#111111]">{{ $successRate }}%</span>
                    </div>
                </div>

            </div>

            <!-- Right 5 Cols: Interactive 3D WebGL Sculpture Canvas -->
            <div class="lg:col-span-5 relative h-[420px] sm:h-[500px] lg:h-[580px] bg-[#E7E4DC] border border-[#D8D4CA] flex items-center justify-center overflow-hidden group shadow-editorial-lg"
                 @mouseenter="setCursor('ROTATE', 'is-orange-hover')"
                 @mouseleave="resetCursor()">
                
                <!-- 3D WebGL Canvas Container -->
                <div id="three-hero-container" class="w-full h-full cursor-grab active:cursor-grabbing"></div>

                <!-- 3D Viewport Coordinates / Technical Corner Metadata -->
                <div class="absolute top-4 left-4 font-mono text-[9px] text-[#5D5A54] uppercase tracking-widest pointer-events-none">
                    FIG. 01 // 3D CHROME AI ARTIFACT
                </div>
                <div class="absolute top-4 right-4 font-mono text-[9px] text-[#FF4D00] uppercase font-bold tracking-widest pointer-events-none flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-[#FF4D00] animate-ping"></span>
                    INTERACTIVE WEBGL
                </div>
                <div class="absolute bottom-4 left-4 right-4 flex items-center justify-between font-mono text-[9px] text-[#8C887F] uppercase pointer-events-none">
                    <span>DRAG TO ROTATE 3D CORE</span>
                    <span>SPECULAR: CHROME IRIDESCENT</span>
                </div>
            </div>

        </div>
    </section>

    <!-- ========================================================================= -->
    <!-- 3. HORIZONTAL SCROLL / DISCOVERY SHOWCASE ("BUILT FOR DISCOVERY") -->
    <!-- ========================================================================= -->
    <section id="discovery" class="py-20 bg-[#111111] text-[#F5F3ED] overflow-hidden">
        <div class="max-w-[1440px] mx-auto px-6 sm:px-10 mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div>
                <span class="text-xs font-mono text-[#FF4D00] uppercase tracking-widest block font-bold mb-2">
                    EXPERIENCE SYSTEM
                </span>
                <h2 class="font-display font-extrabold text-3xl sm:text-5xl uppercase tracking-tight text-[#F5F3ED]">
                    BUILT FOR DISCOVERY.
                </h2>
            </div>
            <p class="text-xs sm:text-sm font-mono text-[#A6A298] max-w-md leading-relaxed">
                Scroll horizontally to explore our multimodal vision pipeline, visual intelligence, and automated marketplace syndication.
            </p>
        </div>

        <!-- Horizontal Scrollable Container -->
        <div class="flex gap-6 overflow-x-auto no-scrollbar px-6 sm:px-10 pb-6 snap-x">
            
            <!-- Slide 1: Multimodal Vision -->
            <div class="min-w-[320px] sm:min-w-[420px] bg-[#181818] border border-[#2C2C2C] p-8 flex flex-col justify-between h-[360px] snap-start hover:border-[#FF4D00] transition-colors group"
                 @mouseenter="setCursor('SCAN', 'is-orange-hover')" 
                 @mouseleave="resetCursor()">
                <div>
                    <span class="text-[#FF4D00] font-mono text-xs font-bold block mb-4">01 // MULTIMODAL VISION</span>
                    <h3 class="font-display font-bold text-2xl text-white uppercase leading-snug">
                        ANALYZING TEXTURE & MATERIAL COMPOSITION
                    </h3>
                    <p class="text-xs text-[#A6A298] font-mono mt-3 leading-relaxed">
                        Computer vision extracts exact fabric grain, leather finishes, and structural components directly from unedited product photography.
                    </p>
                </div>
                <div class="flex items-center justify-between font-mono text-[10px] text-[#8C887F] border-t border-[#2C2C2C] pt-4">
                    <span>ACCURACY: 96.4%</span>
                    <span class="text-[#FF4D00] font-bold">GEMINI 1.5 FLASH &rarr;</span>
                </div>
            </div>

            <!-- Slide 2: Large Typography Divider Card -->
            <div class="min-w-[280px] sm:min-w-[340px] bg-[#FF4D00] text-white p-8 flex flex-col justify-between h-[360px] snap-start">
                <span class="font-mono text-xs font-bold uppercase tracking-widest text-black/70">AUTONOMOUS</span>
                <div class="font-display font-extrabold text-4xl uppercase leading-none tracking-tighter">
                    HANDS-FREE<br>
                    STORE<br>
                    SYNC.
                </div>
                <div class="font-mono text-[10px] text-white/80 uppercase">
                    TIKTOK SHOP &bull; SHOPEE &bull; TOKOPEDIA
                </div>
            </div>

            <!-- Slide 3: SEO Title Generator -->
            <div class="min-w-[320px] sm:min-w-[420px] bg-[#181818] border border-[#2C2C2C] p-8 flex flex-col justify-between h-[360px] snap-start hover:border-[#FF4D00] transition-colors group"
                 @mouseenter="setCursor('SEO', 'is-orange-hover')" 
                 @mouseleave="resetCursor()">
                <div>
                    <span class="text-[#FF4D00] font-mono text-xs font-bold block mb-4">02 // ALGORITHMIC SEO</span>
                    <h3 class="font-display font-bold text-2xl text-white uppercase leading-snug">
                        HIGH-CONVERSION 120-CHAR MARKETPLACE TITLES
                    </h3>
                    <p class="text-xs text-[#A6A298] font-mono mt-3 leading-relaxed">
                        Synthesizes search volume keywords with product specifics to ensure maximum organic ranking across marketplace search feeds.
                    </p>
                </div>
                <div class="flex items-center justify-between font-mono text-[10px] text-[#8C887F] border-t border-[#2C2C2C] pt-4">
                    <span>LATENCY: ~1.2s</span>
                    <span class="text-[#FF4D00] font-bold">1-CLICK ROLLBACK &rarr;</span>
                </div>
            </div>

            <!-- Slide 4: Real-time Telemetry -->
            <div class="min-w-[320px] sm:min-w-[420px] bg-[#181818] border border-[#2C2C2C] p-8 flex flex-col justify-between h-[360px] snap-start hover:border-[#FF4D00] transition-colors group"
                 @mouseenter="setCursor('SSE', 'is-orange-hover')" 
                 @mouseleave="resetCursor()">
                <div>
                    <span class="text-[#FF4D00] font-mono text-xs font-bold block mb-4">03 // SSE STREAMING</span>
                    <h3 class="font-display font-bold text-2xl text-white uppercase leading-snug">
                        REAL-TIME PROGRESS TELEMETRY ENGINE
                    </h3>
                    <p class="text-xs text-[#A6A298] font-mono mt-3 leading-relaxed">
                        Server-Sent Events deliver live terminal diagnostics and progressive percentage batch updates with zero browser polling.
                    </p>
                </div>
                <div class="flex items-center justify-between font-mono text-[10px] text-[#8C887F] border-t border-[#2C2C2C] pt-4">
                    <span>PROTOCOL: HTTP/2 SSE</span>
                    <span class="text-[#FF4D00] font-bold">TERMINAL LIVE &rarr;</span>
                </div>
            </div>

        </div>
    </section>

    <!-- ========================================================================= -->
    <!-- 4. ASYMMETRICAL CURATED PRODUCT CATALOG ("CURATED INTELLIGENCE") -->
    <!-- ========================================================================= -->
    <section id="curated-catalog" class="max-w-[1440px] mx-auto px-6 sm:px-10 py-24 border-b border-[#D8D4CA]">
        
        <!-- Section Header & Filter Toolbar -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-8 pb-12 border-b border-[#D8D4CA]">
            <div class="space-y-2">
                <div class="flex items-center gap-2 font-mono text-xs text-[#FF4D00] uppercase font-bold tracking-widest">
                    <span class="w-2 h-2 bg-[#FF4D00]"></span>
                    EDITORIAL CATALOG
                </div>
                <h2 class="font-display font-extrabold text-4xl sm:text-5xl uppercase tracking-tighter text-[#111111]">
                    CURATED INTELLIGENCE.
                </h2>
                <p class="text-sm font-mono text-[#5D5A54]">
                    Displaying {{ $products->total() }} total catalog items synchronized with {{ $activeStore ? $activeStore->store_name : 'Default Store' }}.
                </p>
            </div>

            <!-- Search & Filter Controls -->
            <form method="GET" action="{{ route('dashboard') }}" class="flex flex-wrap sm:flex-nowrap items-center gap-3 font-mono text-xs">
                @if($activeStore)
                    <input type="hidden" name="store_id" value="{{ $activeStore->id }}">
                @endif
                
                <!-- Search Box -->
                <div class="relative w-full sm:w-64">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="SEARCH PRODUCT / SKU..."
                           class="h-12 w-full bg-white border border-[#D8D4CA] focus:border-[#111111] px-4 text-xs font-mono text-[#111111] placeholder-[#8C887F] focus:outline-none transition-colors">
                </div>

                <!-- Status Filter -->
                <select name="status" onchange="this.form.submit()" 
                        class="h-12 bg-white border border-[#D8D4CA] focus:border-[#111111] px-4 text-xs font-mono text-[#111111] focus:outline-none transition-colors cursor-pointer">
                    <option value="">ALL STATUSES</option>
                    <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>AI OPTIMIZED</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>PENDING</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>FAILED</option>
                    <option value="reverted" {{ request('status') === 'reverted' ? 'selected' : '' }}>REVERTED</option>
                </select>

                @if(request('search') || request('status'))
                    <a href="{{ route('dashboard', $activeStore ? ['store_id' => $activeStore->id] : []) }}" 
                       class="h-12 px-4 bg-[#111111] text-white flex items-center justify-center hover:bg-[#FF4D00] transition-colors"
                       title="Clear Search Filters">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </a>
                @endif
            </form>
        </div>

        <!-- Asymmetrical Product Grid Layout -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-8 pt-12">
            
            @forelse($products as $index => $product)
                @php
                    $imgs = is_array($product->image_urls) ? $product->image_urls : [];
                    $primaryImg = $imgs[0] ?? 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600';
                    // Asymmetrical Column Spans
                    $colSpan = ($index % 5 == 0) ? 'lg:col-span-8' : (($index % 5 == 1) ? 'lg:col-span-4' : 'lg:col-span-4');
                    $isHeroCard = ($index % 5 == 0);
                @endphp

                <div class="{{ $colSpan }} bg-white border border-[#D8D4CA] hover:border-[#111111] p-6 sm:p-8 flex flex-col justify-between transition-all duration-300 hover:shadow-editorial-lg group"
                     @mouseenter="setCursor('VIEW', 'is-orange-hover')" 
                     @mouseleave="resetCursor()">
                    
                    <div class="space-y-6">
                        
                        <!-- Top Metadata Row: SKU & AI Status Badge -->
                        <div class="flex items-center justify-between font-mono text-[11px] border-b border-[#D8D4CA] pb-3">
                            <span class="text-[#8C887F] font-bold tracking-wider">{{ $product->external_product_id }}</span>
                            
                            <!-- Semantic Status Badges -->
                            @if($product->status === 'success')
                                <span class="px-2 py-0.5 bg-[#FF4D00] text-white font-bold uppercase tracking-wider text-[10px]">
                                    AI OPTIMIZED
                                </span>
                            @elseif($product->status === 'processing')
                                <span class="px-2 py-0.5 bg-[#111111] text-white font-bold uppercase tracking-wider text-[10px] animate-pulse">
                                    PROCESSING
                                </span>
                            @elseif($product->status === 'failed')
                                <span class="px-2 py-0.5 bg-red-600 text-white font-bold uppercase tracking-wider text-[10px]">
                                    FAILED
                                </span>
                            @elseif($product->status === 'reverted')
                                <span class="px-2 py-0.5 bg-[#5D5A54] text-white font-bold uppercase tracking-wider text-[10px]">
                                    REVERTED
                                </span>
                            @else
                                <span class="px-2 py-0.5 bg-[#E7E4DC] text-[#111111] font-bold uppercase tracking-wider text-[10px]">
                                    PENDING
                                </span>
                            @endif
                        </div>

                        <!-- Product Image Showcase with Camera Viewfinder Framing & Zoom -->
                        <div class="w-full {{ $isHeroCard ? 'h-72 sm:h-96' : 'h-60 sm:h-72' }} bg-[#F2F0EA] overflow-hidden relative border border-[#D8D4CA]">
                            <img src="{{ $primaryImg }}" alt="{{ $product->original_title }}" 
                                 class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105" loading="lazy">
                            
                            <span class="absolute bottom-3 right-3 bg-black/80 text-white font-mono text-[10px] px-2 py-1 uppercase">
                                {{ count($imgs) }} PHOTOS
                            </span>
                            <span class="absolute top-3 left-3 bg-white/90 text-[#111111] font-mono text-[10px] px-2 py-1 uppercase font-bold">
                                {{ $product->category_name }}
                            </span>
                        </div>

                        <!-- Product Title & Details -->
                        <div class="space-y-2">
                            <h3 class="font-display font-bold {{ $isHeroCard ? 'text-2xl sm:text-3xl' : 'text-xl' }} uppercase tracking-tight text-[#111111] group-hover:text-[#FF4D00] transition-colors leading-tight">
                                {{ $product->generated_title ?: $product->original_title }}
                            </h3>
                            <p class="text-xs font-mono text-[#5D5A54]">
                                Channel: {{ $product->store ? $product->store->platform : 'TIKTOK_SHOP' }} &bull; Updated {{ $product->updated_at->diffForHumans() }}
                            </p>
                        </div>

                        <!-- AI Extracted USPs (If available) -->
                        @if($product->generated_usps && is_array($product->generated_usps) && count($product->generated_usps) > 0)
                            <div class="bg-[#F2F0EA] p-4 border border-[#D8D4CA] space-y-1.5 font-mono text-xs">
                                <span class="text-[10px] uppercase font-bold text-[#FF4D00] tracking-widest block">AI EXTRACTED USPs:</span>
                                @foreach(array_slice($product->generated_usps, 0, 2) as $usp)
                                    <div class="flex items-center gap-2 text-[#111111] truncate">
                                        <span class="text-[#FF4D00] font-bold">&check;</span>
                                        <span class="truncate">{{ $usp }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="bg-[#F2F0EA] p-4 border border-dashed border-[#D8D4CA] text-center font-mono text-xs text-[#8C887F]">
                                Pending AI Multimodal Analysis
                            </div>
                        @endif

                    </div>

                    <!-- Action Controls: Preview Diff & Rollback -->
                    <div class="mt-8 pt-4 border-t border-[#D8D4CA] flex items-center justify-between gap-3 font-mono text-xs font-bold">
                        <button @click="openDiffModal('{{ $product->id }}')" 
                                class="h-11 flex-1 bg-[#111111] hover:bg-[#FF4D00] text-white flex items-center justify-center gap-2 transition-colors uppercase">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                            PREVIEW DIFF
                        </button>

                        @if($product->status === 'success')
                            <button @click="revertSingleProduct('{{ $product->id }}')" 
                                    class="h-11 w-11 border border-[#D8D4CA] hover:border-red-600 hover:text-red-600 bg-white text-[#5D5A54] flex items-center justify-center transition-colors shrink-0"
                                    title="Rollback to original text">
                                <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                            </button>
                        @endif
                    </div>

                </div>
            @empty
                <div class="col-span-full py-24 text-center bg-white border border-[#D8D4CA] p-12">
                    <h3 class="font-display font-extrabold text-2xl uppercase text-[#111111]">NO CATALOG DATA FOUND</h3>
                    <p class="font-mono text-xs text-[#5D5A54] mt-2">Connect a marketplace store channel or reset your search parameters.</p>
                </div>
            @endforelse

        </div>

        <!-- Pagination -->
        <div class="mt-12">
            {{ $products->links() }}
        </div>

    </section>

    <!-- ========================================================================= -->
    <!-- 5. AI INTERACTIVE WORKSPACE SECTION ("YOUR STORE. YOUR INTELLIGENCE.") -->
    <!-- ========================================================================= -->
    <section id="ai-workspace" class="bg-[#111111] text-[#F5F3ED] py-24 border-b border-[#2C2C2C]">
        <div class="max-w-[1440px] mx-auto px-6 sm:px-10 space-y-16">
            
            <!-- Section Header -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-end border-b border-[#2C2C2C] pb-12">
                <div class="lg:col-span-8 space-y-3">
                    <span class="text-xs font-mono text-[#FF4D00] uppercase font-bold tracking-widest block">
                        AUTONOMOUS ENGINE
                    </span>
                    <h2 class="font-display font-extrabold text-4xl sm:text-6xl uppercase tracking-tighter text-white">
                        YOUR STORE.<br>
                        YOUR INTELLIGENCE.
                    </h2>
                </div>
                <div class="lg:col-span-4">
                    <p class="font-mono text-xs sm:text-sm text-[#A6A298] leading-relaxed">
                        Batch automate product copywriting, visual feature extraction, and marketplace synchronization in one continuous pipeline.
                    </p>
                </div>
            </div>

            <!-- Interactive AI Workstation Console -->
            <div class="bg-[#181818] border border-[#2C2C2C] p-8 sm:p-12 shadow-editorial-lg space-y-8">
                
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-8 border-b border-[#2C2C2C]">
                    <div>
                        <h3 class="font-display font-bold text-2xl uppercase text-white">BULK CATEGORY OPTIMIZER</h3>
                        <p class="font-mono text-xs text-[#A6A298] mt-1">Select an active category to execute Gemini Multimodal Vision analysis.</p>
                    </div>

                    <!-- Category Selector & Primary Trigger -->
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
                        <select x-model="selectedCategory" 
                                class="h-12 bg-[#111111] border border-[#2C2C2C] text-[#F5F3ED] px-4 font-mono text-xs uppercase focus:outline-none focus:border-[#FF4D00] transition-colors cursor-pointer min-w-[260px]">
                            <option value="">-- SELECT CATEGORY --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat['id'] }}">
                                    {{ $cat['name'] }} ({{ $cat['product_count'] ?? 0 }} ITEMS)
                                </option>
                            @endforeach
                        </select>

                        <button @click="startBatchAutomation()" 
                                :disabled="!selectedCategory || isProcessing"
                                class="h-12 px-8 font-mono font-bold text-xs uppercase tracking-wider flex items-center justify-center gap-3 transition-all duration-300"
                                :class="(!selectedCategory || isProcessing) 
                                    ? 'bg-[#222222] text-[#5D5A54] border border-[#2C2C2C] cursor-not-allowed' 
                                    : 'bg-[#FF4D00] hover:bg-[#E04400] text-white shadow-orange-glow cursor-pointer active:scale-95'">
                            <template x-if="isProcessing">
                                <i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>
                            </template>
                            <template x-if="!isProcessing">
                                <i data-lucide="sparkles" class="w-4 h-4"></i>
                            </template>
                            <span x-text="isProcessing ? 'OPTIMIZING CATALOG...' : 'RUN AI OPTIMIZATION &rarr;'"></span>
                        </button>
                    </div>
                </div>

                <!-- 5-Phase Active Visual Pipeline -->
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
                    <div class="p-4 bg-[#111111] border border-[#2C2C2C] font-mono text-center space-y-1">
                        <span class="text-[10px] text-[#FF4D00] font-bold uppercase block">STEP 01</span>
                        <span class="text-xs text-white font-bold uppercase block">IMAGE SCAN</span>
                        <span class="text-[10px] text-[#8C887F] block">Multi-angle visual check</span>
                    </div>
                    <div class="p-4 bg-[#111111] border border-[#2C2C2C] font-mono text-center space-y-1">
                        <span class="text-[10px] text-[#FF4D00] font-bold uppercase block">STEP 02</span>
                        <span class="text-xs text-white font-bold uppercase block">USP EXTRACT</span>
                        <span class="text-[10px] text-[#8C887F] block">True materials & specs</span>
                    </div>
                    <div class="p-4 bg-[#111111] border border-[#2C2C2C] font-mono text-center space-y-1">
                        <span class="text-[10px] text-[#FF4D00] font-bold uppercase block">STEP 03</span>
                        <span class="text-xs text-white font-bold uppercase block">SEO TITLE</span>
                        <span class="text-[10px] text-[#8C887F] block">Algorithmic formula</span>
                    </div>
                    <div class="p-4 bg-[#111111] border border-[#2C2C2C] font-mono text-center space-y-1">
                        <span class="text-[10px] text-[#FF4D00] font-bold uppercase block">STEP 04</span>
                        <span class="text-xs text-white font-bold uppercase block">COPY MERCH</span>
                        <span class="text-[10px] text-[#8C887F] block">Structured bullets</span>
                    </div>
                    <div class="p-4 bg-[#111111] border border-[#2C2C2C] font-mono text-center space-y-1 col-span-2 sm:col-span-1">
                        <span class="text-[10px] text-[#FF4D00] font-bold uppercase block">STEP 05</span>
                        <span class="text-xs text-white font-bold uppercase block">STORE SYNC</span>
                        <span class="text-[10px] text-[#8C887F] block">Marketplace API update</span>
                    </div>
                </div>

                <!-- Real-time Progress Bar & SSE Terminal -->
                <div x-show="isProcessing || activeBatch" x-cloak class="pt-6 border-t border-[#2C2C2C] space-y-4">
                    <div class="flex items-center justify-between font-mono text-xs">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-[#FF4D00] animate-ping"></span>
                            <span class="text-[#FF4D00] font-bold">LIVE TELEMETRY STREAM</span>
                            <span class="text-[#8C887F]" x-text="'// BATCH: ' + (activeBatch?.id || 'CONNECTING...')"></span>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-[#A6A298]">PROGRESS: <strong class="text-white" x-text="progress.processed + '/' + progress.total"></strong> SKUs</span>
                            <span class="text-[#FF4D00] font-bold text-sm" x-text="progress.percentage + '%'"></span>
                        </div>
                    </div>

                    <!-- Progress Line Track -->
                    <div class="w-full bg-[#111111] h-2 overflow-hidden border border-[#2C2C2C]">
                        <div class="bg-[#FF4D00] h-full transition-all duration-300" :style="`width: ${progress.percentage}%`"></div>
                    </div>

                    <!-- Terminal Box -->
                    <div class="bg-[#111111] border border-[#2C2C2C] p-4 max-h-48 overflow-y-auto font-mono text-xs space-y-1.5 text-[#A6A298]"
                         x-ref="terminalBox">
                        <template x-for="log in logs" :key="log.id">
                            <div class="flex items-start gap-2">
                                <span class="text-[#8C887F] shrink-0" x-text="log.timestamp"></span>
                                <span class="shrink-0 font-bold"
                                      :class="{
                                          'text-[#FF4D00]': log.type === 'success',
                                          'text-red-500': log.type === 'error',
                                          'text-white': log.type === 'info',
                                          'text-amber-400': log.type === 'warn'
                                      }" x-text="'[' + log.type.toUpperCase() + ']'"></span>
                                <span class="text-white" x-text="log.message"></span>
                            </div>
                        </template>
                        <div x-show="logs.length === 0" class="text-[#8C887F] italic">
                            Waiting for batch execution trigger...
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ========================================================================= -->
    <!-- 6. DRAMATIC OVERSIZED EDITORIAL FOOTER -->
    <!-- ========================================================================= -->
    <footer id="studio-footer" class="bg-[#111111] text-[#F5F3ED] pt-24 pb-16">
        <div class="max-w-[1440px] mx-auto px-6 sm:px-10 space-y-16">
            
            <!-- Oversized Footer Typography -->
            <div class="border-b border-[#2C2C2C] pb-16">
                <span class="text-xs font-mono text-[#FF4D00] uppercase font-bold tracking-widest block mb-4">
                    STUDIO INTELLIGENCE
                </span>
                <h2 class="font-display font-extrabold text-5xl sm:text-7xl lg:text-9xl uppercase tracking-tighter text-white leading-none">
                    LET'S BUILD<br>
                    <span class="text-[#FF4D00]">WHAT'S NEXT.</span>
                </h2>
            </div>

            <!-- Footer Links & Studio Colophon -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 font-mono text-xs">
                
                <div class="space-y-3">
                    <span class="text-[10px] text-[#8C887F] uppercase tracking-widest block">PRODUCT</span>
                    <p class="text-[#A6A298] leading-relaxed">
                        AutoCopy AI is an editorial commerce operating system powered by Google Gemini Multimodal Vision and real-time marketplace adapters.
                    </p>
                </div>

                <div class="space-y-3">
                    <span class="text-[10px] text-[#8C887F] uppercase tracking-widest block">ARCHITECTURE</span>
                    <ul class="space-y-1.5 text-[#A6A298]">
                        <li>PHP 8.2+ / Laravel 11</li>
                        <li>Google Gemini 1.5 Flash</li>
                        <li>Server-Sent Events (SSE)</li>
                        <li>Three.js WebGL Core</li>
                    </ul>
                </div>

                <div class="space-y-3">
                    <span class="text-[10px] text-[#8C887F] uppercase tracking-widest block">CHANNELS</span>
                    <ul class="space-y-1.5 text-[#A6A298]">
                        <li>TikTok Shop Partner</li>
                        <li>Shopee Open API</li>
                        <li>Tokopedia Seller API</li>
                        <li>Custom Webhook Adapter</li>
                    </ul>
                </div>

                <div class="space-y-3">
                    <span class="text-[10px] text-[#8C887F] uppercase tracking-widest block">CREDITS</span>
                    <p class="text-[#A6A298]">
                        Designed with creative technology and high-end digital studio craftsmanship.
                    </p>
                    <span class="text-[#FF4D00] font-bold block mt-2">&copy; {{ date('Y') }} AUTOCOPY AI</span>
                </div>

            </div>
        </div>
    </footer>

    <!-- ========================================================================= -->
    <!-- 7. MODALS: CONTENT DIFF STUDIO, CONNECT STORE, GEMINI API CONFIG -->
    <!-- ========================================================================= -->

    <!-- MODAL 1: Before-After Content Diff Comparison Studio -->
    <div x-show="showDiffModal" x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 backdrop-blur-md p-4"
         @keydown.escape.window="showDiffModal = false">
        <div class="bg-[#111111] text-[#F5F3ED] border border-[#2C2C2C] w-full max-w-5xl max-h-[90vh] flex flex-col shadow-editorial-lg overflow-hidden"
             @click.away="showDiffModal = false">
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between px-8 py-5 border-b border-[#2C2C2C] bg-[#181818]">
                <div>
                    <h3 class="font-display font-bold text-lg uppercase text-white">CONTENT DIFF COMPARISON</h3>
                    <p class="font-mono text-xs text-[#8C887F]" x-text="'SKU ID: ' + (activeProduct?.external_id || '')"></p>
                </div>
                <button @click="showDiffModal = false" class="p-2 hover:bg-[#222222] text-[#A6A298] hover:text-white transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Modal Content Body -->
            <div class="p-8 overflow-y-auto space-y-8">
                <template x-if="activeProduct">
                    <div class="space-y-8">
                        
                        <!-- Visual Photos Gallery -->
                        <div class="space-y-2">
                            <span class="text-[10px] font-mono uppercase tracking-widest text-[#8C887F]">ANALYSED PRODUCT PHOTOGRAPHY:</span>
                            <div class="flex items-center gap-4 overflow-x-auto pb-2">
                                <template x-for="(img, idx) in activeProduct.image_urls" :key="idx">
                                    <div class="w-28 h-28 bg-[#181818] border border-[#2C2C2C] overflow-hidden shrink-0">
                                        <img :src="img" class="w-full h-full object-cover">
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Diff Columns -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            
                            <!-- Left: Original Listing -->
                            <div class="p-6 bg-[#181818] border border-[#2C2C2C] space-y-4">
                                <span class="px-2 py-0.5 bg-[#2C2C2C] text-[#A6A298] font-mono text-[10px] uppercase font-bold">
                                    01 // ORIGINAL LISTING
                                </span>
                                <div>
                                    <label class="text-[10px] font-mono text-[#8C887F] uppercase block">ORIGINAL TITLE</label>
                                    <p class="text-xs font-mono text-white mt-1 p-3 bg-[#111111] border border-[#2C2C2C]" x-text="activeProduct.original_title"></p>
                                </div>
                                <div>
                                    <label class="text-[10px] font-mono text-[#8C887F] uppercase block">ORIGINAL DESCRIPTION</label>
                                    <p class="text-xs font-mono text-[#A6A298] mt-1 p-3 bg-[#111111] border border-[#2C2C2C] max-h-56 overflow-y-auto whitespace-pre-line" x-text="activeProduct.original_desc"></p>
                                </div>
                            </div>

                            <!-- Right: AI Optimized Listing -->
                            <div class="p-6 bg-[#181818] border border-[#FF4D00] space-y-4">
                                <span class="px-2 py-0.5 bg-[#FF4D00] text-white font-mono text-[10px] uppercase font-bold">
                                    02 // AI MULTIMODAL OPTIMIZED
                                </span>
                                <div>
                                    <label class="text-[10px] font-mono text-[#FF4D00] uppercase block">NEW SEO TITLE (MAX 120 CHARS)</label>
                                    <p class="text-xs font-mono text-white font-bold mt-1 p-3 bg-[#111111] border border-[#FF4D00]" x-text="activeProduct.generated_title || '(Pending optimization)'"></p>
                                </div>

                                <!-- Extracted USPs -->
                                <div x-show="activeProduct.generated_usps">
                                    <label class="text-[10px] font-mono text-[#FF4D00] uppercase block">EXTRACTED USPs</label>
                                    <ul class="mt-1 space-y-1.5 p-3 bg-[#111111] border border-[#2C2C2C] font-mono text-xs">
                                        <template x-for="(usp, i) in activeProduct.generated_usps" :key="i">
                                            <li class="flex items-center gap-2 text-white">
                                                <span class="text-[#FF4D00] font-bold">&check;</span>
                                                <span x-text="usp"></span>
                                            </li>
                                        </template>
                                    </ul>
                                </div>

                                <div>
                                    <label class="text-[10px] font-mono text-[#FF4D00] uppercase block">STRUCTURED DESCRIPTION</label>
                                    <p class="text-xs font-mono text-[#A6A298] mt-1 p-3 bg-[#111111] border border-[#2C2C2C] max-h-56 overflow-y-auto whitespace-pre-line" x-text="activeProduct.generated_desc || '(Pending optimization)'"></p>
                                </div>

                                <!-- SEO Keywords -->
                                <div x-show="activeProduct.seo_keywords">
                                    <label class="text-[10px] font-mono text-[#FF4D00] uppercase block">TARGET KEYWORDS</label>
                                    <div class="mt-1 flex flex-wrap gap-1.5">
                                        <template x-for="(kw, k) in activeProduct.seo_keywords" :key="k">
                                            <span class="text-[10px] font-mono px-2 py-0.5 bg-[#FF4D00]/10 text-[#FF4D00] border border-[#FF4D00]/30" x-text="'#' + kw"></span>
                                        </template>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </template>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-between px-8 py-5 border-t border-[#2C2C2C] bg-[#181818] font-mono text-xs">
                <button @click="revertActiveProduct()" 
                        class="h-10 px-4 border border-red-500 text-red-400 hover:bg-red-500 hover:text-white uppercase font-bold transition-colors">
                    REVERT TO ORIGINAL
                </button>
                <button @click="showDiffModal = false" 
                        class="h-10 px-6 bg-white text-[#111111] hover:bg-[#FF4D00] hover:text-white uppercase font-bold transition-colors">
                    CLOSE STUDIO
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL 2: Hubungkan Toko Baru -->
    <div x-show="showConnectModal" x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 backdrop-blur-md p-4">
        <div class="bg-[#111111] text-[#F5F3ED] border border-[#2C2C2C] w-full max-w-md p-8 shadow-editorial-lg space-y-6"
             @click.away="showConnectModal = false">
            <div class="flex items-center justify-between border-b border-[#2C2C2C] pb-4">
                <h3 class="font-display font-bold text-base uppercase text-white">CONNECT MARKETPLACE CHANNEL</h3>
                <button @click="showConnectModal = false" class="text-[#8C887F] hover:text-white">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form @submit.prevent="submitConnectStore()" class="space-y-4 font-mono text-xs">
                <div>
                    <label class="text-[10px] text-[#8C887F] uppercase block">PLATFORM</label>
                    <select x-model="connectForm.platform" 
                            class="w-full mt-1.5 bg-[#181818] border border-[#2C2C2C] text-white px-3 py-2.5 focus:outline-none focus:border-[#FF4D00]">
                        <option value="SHOPEE">Shopee Open API</option>
                        <option value="TIKTOK_SHOP">TikTok Shop Partner</option>
                        <option value="TOKOPEDIA">Tokopedia Seller API</option>
                    </select>
                </div>

                <div>
                    <label class="text-[10px] text-[#8C887F] uppercase block">STORE DISPLAY NAME</label>
                    <input type="text" x-model="connectForm.store_name" required placeholder="e.g. Official Fashion Store"
                           class="w-full mt-1.5 bg-[#181818] border border-[#2C2C2C] text-white px-3 py-2.5 focus:outline-none focus:border-[#FF4D00]">
                </div>

                <div>
                    <label class="text-[10px] text-[#8C887F] uppercase block">SHOP ID / PARTNER IDENTIFIER</label>
                    <input type="text" x-model="connectForm.platform_store_id" required placeholder="e.g. OFFICIAL-9922"
                           class="w-full mt-1.5 bg-[#181818] border border-[#2C2C2C] text-white px-3 py-2.5 focus:outline-none focus:border-[#FF4D00]">
                </div>

                <div class="pt-4 flex justify-end gap-3 border-t border-[#2C2C2C]">
                    <button type="button" @click="showConnectModal = false" class="h-10 px-4 text-[#A6A298] hover:text-white uppercase font-bold">
                        CANCEL
                    </button>
                    <button type="submit" class="h-10 px-6 bg-[#FF4D00] hover:bg-[#E04400] text-white uppercase font-bold transition-colors">
                        AUTHORIZE
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 3: Gemini API Key Config Modal -->
    <div x-show="showApiModal" x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 backdrop-blur-md p-4">
        <div class="bg-[#111111] text-[#F5F3ED] border border-[#2C2C2C] w-full max-w-md p-8 shadow-editorial-lg space-y-6"
             @click.away="showApiModal = false">
            <div class="flex items-center justify-between border-b border-[#2C2C2C] pb-4">
                <h3 class="font-display font-bold text-base uppercase text-white">GEMINI VISION CONFIGURATION</h3>
                <button @click="showApiModal = false" class="text-[#8C887F] hover:text-white">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <p class="font-mono text-xs text-[#A6A298] leading-relaxed">
                Connect your Google Gemini API key to activate the live multimodal computer vision pipeline (<code class="text-[#FF4D00]">gemini-1.5-flash</code>).
            </p>

            <form @submit.prevent="submitApiKey()" class="space-y-4 font-mono text-xs">
                <div>
                    <label class="text-[10px] text-[#8C887F] uppercase block">GEMINI API KEY</label>
                    <input type="password" x-model="apiKeyInput" placeholder="AIzaSy..."
                           class="w-full mt-1.5 bg-[#181818] border border-[#2C2C2C] text-white px-3 py-2.5 focus:outline-none focus:border-[#FF4D00]">
                </div>

                <div class="pt-4 flex justify-end gap-3 border-t border-[#2C2C2C]">
                    <button type="button" @click="showApiModal = false" class="h-10 px-4 text-[#A6A298] hover:text-white uppercase font-bold">
                        CANCEL
                    </button>
                    <button type="submit" class="h-10 px-6 bg-[#FF4D00] hover:bg-[#E04400] text-white uppercase font-bold transition-colors">
                        SAVE CONFIG
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 8. JAVASCRIPT: ALPINE.JS APP LOGIC + THREE.JS 3D SCULPTURE -->
    <!-- ========================================================================= -->
    <script>
        function creativeEcommerceApp() {
            return {
                isLoaded: false,
                cursorText: '',
                cursorClass: '',

                // Business Logic State
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
                        setTimeout(() => {
                            this.isLoaded = true;
                        }, 50);

                        if (window.lucide) {
                            window.lucide.createIcons();
                        }

                        this.initThreeJsHero();
                    });
                },

                handleCursor(e) {
                    const cursor = document.getElementById('custom-cursor');
                    if (cursor) {
                        cursor.style.left = e.clientX + 'px';
                        cursor.style.top = e.clientY + 'px';
                    }
                },

                setCursor(text, className) {
                    this.cursorText = text;
                    this.cursorClass = className;
                },

                resetCursor() {
                    this.cursorText = '';
                    this.cursorClass = '';
                },

                initThreeJsHero() {
                    const container = document.getElementById('three-hero-container');
                    if (!container || typeof THREE === 'undefined') return;

                    const width = container.clientWidth;
                    const height = container.clientHeight;

                    const scene = new THREE.Scene();
                    const camera = new THREE.PerspectiveCamera(45, width / height, 0.1, 1000);
                    camera.position.z = 5;

                    const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
                    renderer.setSize(width, height);
                    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
                    container.appendChild(renderer.domElement);

                    // Create Geometric Chrome Sculpture (Dual Icosahedron + Torus knot combination)
                    const geometry = new THREE.IcosahedronGeometry(1.6, 1);
                    const material = new THREE.MeshPhysicalMaterial({
                        color: 0x111111,
                        emissive: 0x221100,
                        roughness: 0.1,
                        metalness: 0.9,
                        clearcoat: 1.0,
                        clearcoatRoughness: 0.1,
                        wireframe: false,
                    });
                    const mesh = new THREE.Mesh(geometry, material);
                    scene.add(mesh);

                    // Outer Orange Wireframe Halo
                    const wireGeometry = new THREE.IcosahedronGeometry(1.85, 1);
                    const wireMaterial = new THREE.MeshBasicMaterial({
                        color: 0xFF4D00,
                        wireframe: true,
                        transparent: true,
                        opacity: 0.35,
                    });
                    const wireMesh = new THREE.Mesh(wireGeometry, wireMaterial);
                    scene.add(wireMesh);

                    // Lighting
                    const ambientLight = new THREE.AmbientLight(0xffffff, 0.9);
                    scene.add(ambientLight);

                    const pointLight1 = new THREE.PointLight(0xFF4D00, 3, 50);
                    pointLight1.position.set(4, 4, 4);
                    scene.add(pointLight1);

                    const pointLight2 = new THREE.PointLight(0xffffff, 2, 50);
                    pointLight2.position.set(-4, -4, 3);
                    scene.add(pointLight2);

                    // Mouse Drag & Movement Interaction
                    let isDragging = false;
                    let previousMousePosition = { x: 0, y: 0 };

                    container.addEventListener('mousedown', (e) => {
                        isDragging = true;
                        previousMousePosition = { x: e.clientX, y: e.clientY };
                    });

                    window.addEventListener('mouseup', () => {
                        isDragging = false;
                    });

                    window.addEventListener('mousemove', (e) => {
                        if (isDragging) {
                            const deltaX = e.clientX - previousMousePosition.x;
                            const deltaY = e.clientY - previousMousePosition.y;

                            mesh.rotation.y += deltaX * 0.008;
                            mesh.rotation.x += deltaY * 0.008;
                            wireMesh.rotation.y += deltaX * 0.008;
                            wireMesh.rotation.x += deltaY * 0.008;

                            previousMousePosition = { x: e.clientX, y: e.clientY };
                        }
                    });

                    // Animation Loop
                    const animate = () => {
                        requestAnimationFrame(animate);

                        if (!isDragging) {
                            mesh.rotation.x += 0.003;
                            mesh.rotation.y += 0.005;
                            wireMesh.rotation.x -= 0.002;
                            wireMesh.rotation.y -= 0.004;
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
                    this.appendLog("Initiating multimodal vision engine...", "info");

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
                            this.appendLog(data.message || 'Failed to start batch', 'error');
                            this.isProcessing = false;
                            return;
                        }

                        this.activeBatch = { id: data.batch_id };
                        this.progress.total = data.total_products;
                        this.progress.processed = 0;
                        this.progress.percentage = 0;

                        this.appendLog(`Batch registered (${data.total_products} SKUs). Connecting real-time SSE stream...`, 'success');

                        // Connect SSE Stream
                        this.connectStream(data.batch_id);

                    } catch (e) {
                        this.appendLog("Server connection error: " + e.message, "error");
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

                        if (data.jobs && data.jobs.length > 0) {
                            const latest = data.jobs.find(j => j.status === 'success' || j.status === 'failed');
                            if (latest) {
                                this.appendLog(`SKU [${latest.product_id.substring(0,8)}] STATUS: ${latest.status.toUpperCase()} — ${latest.generated_title || latest.title}`, latest.status === 'success' ? 'success' : 'warn');
                            }
                        }
                    });

                    this.eventSource.addEventListener('finished', (e) => {
                        const data = JSON.parse(e.data);
                        this.progress.percentage = 100;
                        this.appendLog(`🎉 Optimization complete! Processed ${data.success_count} SKUs (${data.failed_count} failed).`, 'success');
                        this.isProcessing = false;
                        this.eventSource.close();
                        setTimeout(() => {
                            window.location.reload();
                        }, 2500);
                    });

                    this.eventSource.onerror = (err) => {
                        console.warn("SSE stream closed/reconnected.", err);
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
                        alert("Failed to load diff preview: " + e.message);
                    }
                },

                async revertActiveProduct() {
                    if (!this.activeProduct) return;
                    if (!confirm("Restore this product to its original marketplace title & description?")) return;

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
                            alert("Listing restored to original version!");
                            window.location.reload();
                        }
                    } catch (e) {
                        alert("Failed to revert: " + e.message);
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
                        alert("Failed to connect store: " + e.message);
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
                        alert("Failed to save API key: " + e.message);
                    }
                }
            }
        }
    </script>
</body>
</html>
