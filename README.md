# 🛍️ AutoCopy AI — E-Commerce Auto-Copywriter & Store Automation

AutoCopy AI adalah platform cerdas berbasis Laravel dan Google Gemini Multimodal Vision yang dirancang untuk mengotomatisasi pembuatan copywriting produk, ekstraksi Unique Selling Point (USP), optimalisasi judul ramah SEO marketplace, serta sinkronisasi katalog secara *hands-free* ke platform e-commerce terkemuka (Shopee, TikTok Shop, Tokopedia).

---

## 🚀 Fitur Utama

- **Multimodal Vision Copywriting**: Menganalisis visual foto produk (multi-image) dan deskripsi mentah menggunakan **Google Gemini 1.5 Flash** untuk mendeteksi fitur nyata, material, dan keunggulan produk tanpa halusinasi klaim.
- **Auto SEO Title Generator**: Menyusun judul berdaya konversi tinggi sesuai formula algoritma pencarian marketplace (maksimal 120 karakter).
- **Extracted USP & Structured Bullets**: Menyajikan poin keunggulan unik (USP) serta deskripsi persuasif dengan format terstruktur rapi.
- **Multi-Store Management**: Menghubungkan banyak toko dari berbagai channel penjualan (Shopee Open API, TikTok Shop Partner, Tokopedia Seller API).
- **Bulk Category Automation**: Eksekusi optimasi massal produk per kategori hanya dengan 1-klik.
- **Real-Time Streaming Progress (SSE)**: Visualisasi progres eksekusi live terminal log & progress bar via Server-Sent Events.
- **Before vs After Diff Comparison**: Preview perbandingan visual dan teks sebelum vs sesudah optimasi AI.
- **1-Click Rollback / Revert**: Fitur pengembalian data ke versi teks original kapan saja jika diperlukan.
- **Intelligent Fallback Engine**: Sistem tetap dapat beroperasi dan mendemonstrasikan hasil optimasi berkualitas tinggi meskipun tanpa API Key eksternal.

---

## 🛠️ Tech Stack & Arsitektur

- **Backend**: PHP 8.2+ / Laravel 11+
- **Frontend**: Blade, Tailwind CSS, Alpine.js, Lucide Icons
- **Database**: SQLite / MySQL
- **AI Engine**: Google Gemini API (`gemini-1.5-flash`)
- **Real-time Protocol**: Server-Sent Events (SSE)
- **Patterns**: Adapter Pattern (`ShopeeAdapter`, `TikTokShopAdapter`, `MockMarketplaceAdapter`, `MarketplaceManager`)

---

## 📦 Instalasi & Menjalankan Project

### 1. Clone Repository
```bash
git clone https://github.com/gaminghubindo/Eskul-ai-.git
cd Eskul-ai-
```

### 2. Install Dependency
```bash
composer install
npm install
```

### 3. Konfigurasi Environment (.env)
Salin file `.env.example` menjadi `.env`:
```bash
cp .env.example .env
php artisan key:generate
```

Isi konfigurasi Gemini API Key (opsional jika ingin memakai model live):
```env
GEMINI_API_KEY=your_gemini_api_key_here
GEMINI_MODEL=gemini-1.5-flash
```

### 4. Database Migration & Seeding
```bash
php artisan migrate --seed
```

### 5. Jalankan Server Lokal
```bash
php artisan serve
```
Akses aplikasi melalui browser di `http://127.0.0.1:8000`.

---

## 📄 Lisensi
Open-source di bawah lisensi [MIT](LICENSE).
