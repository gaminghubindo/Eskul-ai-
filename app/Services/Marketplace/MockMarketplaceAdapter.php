<?php

namespace App\Services\Marketplace;

use App\Models\Store;
use Illuminate\Support\Facades\Log;

class MockMarketplaceAdapter implements MarketplaceAdapterInterface
{
    public function getAuthUrl(string $redirectUri): string
    {
        return "/oauth/mock/authorize?redirect_uri=" . urlencode($redirectUri);
    }

    public function handleCallback(string $code): array
    {
        return [
            'access_token' => 'mock_sec_token_' . bin2hex(random_bytes(16)),
            'refresh_token' => 'mock_ref_token_' . bin2hex(random_bytes(16)),
            'expires_in' => 86400 * 30,
            'shop_id' => 'OFFICIAL-' . rand(1000, 9999),
        ];
    }

    public function fetchCategories(Store $store): array
    {
        return [
            ['id' => 'cat_fashion_pria', 'name' => 'Fashion & Pakaian Pria', 'product_count' => 6],
            ['id' => 'cat_elektronik', 'name' => 'Elektronik & Smart Gadget', 'product_count' => 6],
            ['id' => 'cat_kecantikan', 'name' => 'Skincare & Perawatan Wajah', 'product_count' => 6],
            ['id' => 'cat_sepatu', 'name' => 'Sepatu & Sneakers Lifestyle', 'product_count' => 6],
        ];
    }

    public function fetchProductsByCategory(Store $store, string $categoryId, int $page = 1, int $pageSize = 50): array
    {
        $catalog = $this->getMockProductCatalog();
        $filtered = array_filter($catalog, fn($item) => $item['category_id'] === $categoryId);
        return array_values($filtered);
    }

    public function updateProduct(Store $store, string $externalProductId, array $payload): bool
    {
        Log::info("MockMarketplaceAdapter: Successfully synced updated copy for product {$externalProductId} to Store {$store->store_name}");
        return true;
    }

    /**
     * Seed catalog with realistic products and high-resolution images.
     */
    public function getMockProductCatalog(): array
    {
        return [
            // Kategori: Fashion Pria
            [
                'external_product_id' => 'EXT-FSH-001',
                'category_id' => 'cat_fashion_pria',
                'category_name' => 'Fashion & Pakaian Pria',
                'original_title' => 'kemeja pria lengan panjang polos bahan katun oxford slim fit warna navy',
                'original_desc' => "Kemeja polos pria lengan panjang\nBahan katun oxford tebal dan adem\nJahitan rapi, cocok untuk kuliah, kerja kantoran dan acara formal\nSize M L XL ready",
                'image_urls' => [
                    'https://images.unsplash.com/photo-1602810318383-e386cc2a3ccf?w=600&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?w=600&auto=format&fit=crop&q=80',
                ],
            ],
            [
                'external_product_id' => 'EXT-FSH-002',
                'category_id' => 'cat_fashion_pria',
                'category_name' => 'Fashion & Pakaian Pria',
                'original_title' => 'jaket pria parasut bomber anti angin outdoor hoodie casual keren',
                'original_desc' => "Jaket bomber pria bahan taslan waterproof ringan\nLapisan dalam furing lembut menyerap keringat\nAda saku dalam untuk hp\nCocok untuk naik motor dan harian",
                'image_urls' => [
                    'https://images.unsplash.com/photo-1544441893-675973e31985?w=600&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1551028719-00167b16eac5?w=600&auto=format&fit=crop&q=80',
                ],
            ],
            [
                'external_product_id' => 'EXT-FSH-003',
                'category_id' => 'cat_fashion_pria',
                'category_name' => 'Fashion & Pakaian Pria',
                'original_title' => 'kaos polos cotton combed 30s premium pria distro hitam lengan pendek',
                'original_desc' => "Kaos polos pria hitam combed 30s gramasi pas tidak menerawang\nNyaman dan lembut di kulit, anti gerah\nStandar distro Bandung",
                'image_urls' => [
                    'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=600&auto=format&fit=crop&q=80',
                ],
            ],
            [
                'external_product_id' => 'EXT-FSH-004',
                'category_id' => 'cat_fashion_pria',
                'category_name' => 'Fashion & Pakaian Pria',
                'original_title' => 'celana chino panjang pria reguler fit stretch melar abu dark grey',
                'original_desc' => "Celana chino panjang bahan katun twill stretch elastis\nPotongan rapi, fleksibel saat bergerak\nKantong samping dan belakang fungsional",
                'image_urls' => [
                    'https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?w=600&auto=format&fit=crop&q=80',
                ],
            ],
            [
                'external_product_id' => 'EXT-FSH-005',
                'category_id' => 'cat_fashion_pria',
                'category_name' => 'Fashion & Pakaian Pria',
                'original_title' => 'hoodie jumper oversize pria wanita unisex fleece tebal sage green',
                'original_desc' => "Hoodie jumper sweater bahan cotton fleece gramasi 280\nSangat hangat, lembut tidak berbulu\nWarna sage green estetik",
                'image_urls' => [
                    'https://images.unsplash.com/photo-1556905055-8f358a7a47b2?w=600&auto=format&fit=crop&q=80',
                ],
            ],
            [
                'external_product_id' => 'EXT-FSH-006',
                'category_id' => 'cat_fashion_pria',
                'category_name' => 'Fashion & Pakaian Pria',
                'original_title' => 'dompet pria kulit lipat pendek casual kartu banyak warna coklat vintage',
                'original_desc' => "Dompet lipat pria bahan kulit sintetis premium tebal\nBanyak slot kartu dan kompartemen uang kertas luas\nJahitan tepi dobel kuat",
                'image_urls' => [
                    'https://images.unsplash.com/photo-1627123424574-724758594e93?w=600&auto=format&fit=crop&q=80',
                ],
            ],

            // Kategori: Elektronik & Smart Gadget
            [
                'external_product_id' => 'EXT-ELE-001',
                'category_id' => 'cat_elektronik',
                'category_name' => 'Elektronik & Smart Gadget',
                'original_title' => 'tws bluetooth 5.3 wireless earphone earbud bass suara jernih mic gaming',
                'original_desc' => "TWS bluetooth versi 5.3 koneksi stabil tanpa delay\nBaterai tahan hingga 6 jam nonstop + casing 300mAh\nBass nendang cocok untuk musik dan meeting online",
                'image_urls' => [
                    'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?w=600&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1606220588913-b3aacb4d2f46?w=600&auto=format&fit=crop&q=80',
                ],
            ],
            [
                'external_product_id' => 'EXT-ELE-002',
                'category_id' => 'cat_elektronik',
                'category_name' => 'Elektronik & Smart Gadget',
                'original_title' => 'smartwatch pria wanita layar amoled jam tangan pintar anti air ip68',
                'original_desc' => "Smartwatch modern dengan sensor detak jantung, monitor tidur, dan 100+ mode olahraga\nNotifikasi pesan WhatsApp masuk real-time\nStrap silikon empuk",
                'image_urls' => [
                    'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600&auto=format&fit=crop&q=80',
                ],
            ],
            [
                'external_product_id' => 'EXT-ELE-003',
                'category_id' => 'cat_elektronik',
                'category_name' => 'Elektronik & Smart Gadget',
                'original_title' => 'powerbank 20000mah fast charging 22.5w type c dual usb led display display',
                'original_desc' => "Powerbank kapasitas besar 20000mAh support Quick Charge 3.0 & Power Delivery\nBisa cas 3 perangkat bersamaan, indikator baterai LED digital akurat",
                'image_urls' => [
                    'https://images.unsplash.com/photo-1609592807901-5259cf3c75d4?w=600&auto=format&fit=crop&q=80',
                ],
            ],
            [
                'external_product_id' => 'EXT-ELE-004',
                'category_id' => 'cat_elektronik',
                'category_name' => 'Elektronik & Smart Gadget',
                'original_title' => 'keyboard wireless mechanical 68 keys rgb bluetooth rechargeable gaming kerja',
                'original_desc' => "Keyboard mekanikal mini layout 68 tombol\nSwitch tactile empuk dengan backlight RGB menarik\nBisa connect via Bluetooth, Wireless 2.4G, dan kabel Type-C",
                'image_urls' => [
                    'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=600&auto=format&fit=crop&q=80',
                ],
            ],
            [
                'external_product_id' => 'EXT-ELE-005',
                'category_id' => 'cat_elektronik',
                'category_name' => 'Elektronik & Smart Gadget',
                'original_title' => 'speaker bluetooth portable mini bass bulat outdoor anti air tahan 8 jam',
                'original_desc' => "Speaker wireless portabel suara lantang 360 derajat surround bass\nBahan body karet tahan benturan dan cipratan air\nDilengkapi tali gantung",
                'image_urls' => [
                    'https://images.unsplash.com/photo-1545454675-3531b543be5d?w=600&auto=format&fit=crop&q=80',
                ],
            ],
            [
                'external_product_id' => 'EXT-ELE-006',
                'category_id' => 'cat_elektronik',
                'category_name' => 'Elektronik & Smart Gadget',
                'original_title' => 'stand holder hp meja lipat aluminium adjustable rotasi 360 derajat',
                'original_desc' => "Holder handphone dan tablet bahan full aluminium kokoh tidak goyang\nBisa diputar 360 derajat dan diatur sudut kemiringan sesuai kenyamanan",
                'image_urls' => [
                    'https://images.unsplash.com/photo-1586105251261-72a756497a11?w=600&auto=format&fit=crop&q=80',
                ],
            ],

            // Kategori: Skincare & Perawatan Wajah
            [
                'external_product_id' => 'EXT-SKN-001',
                'category_id' => 'cat_kecantikan',
                'category_name' => 'Skincare & Perawatan Wajah',
                'original_title' => 'serum wajah niacinamide 10% zinc mencerahkan kulit kusam bekas jerawat 20ml',
                'original_desc' => "Serum perawatan wajah intensif mencerahkan warna kulit tidak merata\nTekstur ringan cepat meresap tanpa rasa lengket\nMenyamarkan noda hitam dan merawat pori-pori",
                'image_urls' => [
                    'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?w=600&auto=format&fit=crop&q=80',
                ],
            ],
            [
                'external_product_id' => 'EXT-SKN-002',
                'category_id' => 'cat_kecantikan',
                'category_name' => 'Skincare & Perawatan Wajah',
                'original_title' => 'sunscreen gel spf 50 pa++++ ringan tanpa whitecast proteksi uv 50ml',
                'original_desc' => "Tabir surya tekstur gel air watery sejuk di kulit\nPerlindungan maksimal dari sinar UVA UVB dan blue light\nNon-comedogenic cocok untuk semua tipe kulit",
                'image_urls' => [
                    'https://images.unsplash.com/photo-1556228720-195a672e8a03?w=600&auto=format&fit=crop&q=80',
                ],
            ],
            [
                'external_product_id' => 'EXT-SKN-003',
                'category_id' => 'cat_kecantikan',
                'category_name' => 'Skincare & Perawatan Wajah',
                'original_title' => 'moisturizer ceramide gel pelembab perbaiki skin barrier menenangkan kemerahan',
                'original_desc' => "Pelembab wajah formula 5X Ceramide menjaga kelembapan hingga 24 jam\nMeredakan kulit sensitif kemerahan dan memperkuat lapisan pertahanan kulit",
                'image_urls' => [
                    'https://images.unsplash.com/photo-1608248597359-2ff586221c5f?w=600&auto=format&fit=crop&q=80',
                ],
            ],
            [
                'external_product_id' => 'EXT-SKN-004',
                'category_id' => 'cat_kecantikan',
                'category_name' => 'Skincare & Perawatan Wajah',
                'original_title' => 'gentle facial cleanser pembersih wajah low ph tea tree salicylic acid 100ml',
                'original_desc' => "Sabun cuci muka lembut dengan busa halus tanpa membuat kulit tertarik kering\nMembersihkan minyak berlebih dan debu polusi hingga ke pori terdalam",
                'image_urls' => [
                    'https://images.unsplash.com/photo-1556228722-d0b5ed7cd315?w=600&auto=format&fit=crop&q=80',
                ],
            ],
            [
                'external_product_id' => 'EXT-SKN-005',
                'category_id' => 'cat_kecantikan',
                'category_name' => 'Skincare & Perawatan Wajah',
                'original_title' => 'lip serum tint melembabkan bibir kering pecah warna pink alami glossy 5ml',
                'original_desc' => "Serum bibir dengan ekstrak jojoba oil dan vitamin E\nMemberi rona alami bibir sehat merona dan melembabkan sepanjang hari",
                'image_urls' => [
                    'https://images.unsplash.com/photo-1586495777744-4413f21062fa?w=600&auto=format&fit=crop&q=80',
                ],
            ],
            [
                'external_product_id' => 'EXT-SKN-006',
                'category_id' => 'cat_kecantikan',
                'category_name' => 'Skincare & Perawatan Wajah',
                'original_title' => 'clay mask mugwort pori bersih komedo terangkat glowing masker wajah 50g',
                'original_desc' => "Masker bilas ekstrak mugwort alami membersihkan pori tersumbat dan menenangkan jerawat aktif\nKulit terasa bersih halus dan segar seketika",
                'image_urls' => [
                    'https://images.unsplash.com/photo-1567928815114-1e0e84b2fa1f?w=600&auto=format&fit=crop&q=80',
                ],
            ],

            // Kategori: Sepatu & Sneakers
            [
                'external_product_id' => 'EXT-SNK-001',
                'category_id' => 'cat_sepatu',
                'category_name' => 'Sepatu & Sneakers Lifestyle',
                'original_title' => 'sneakers pria casual kanvas putih sol karet anti licin sepatu jalan santai',
                'original_desc' => "Sepatu sneakers kanvas putih klasik desain minimalis\nInsole empuk tidak bikin kaki lecet, outsole karet tebal tahan aus",
                'image_urls' => [
                    'https://images.unsplash.com/photo-1549298916-b41d501d3772?w=600&auto=format&fit=crop&q=80',
                ],
            ],
            [
                'external_product_id' => 'EXT-SNK-002',
                'category_id' => 'cat_sepatu',
                'category_name' => 'Sepatu & Sneakers Lifestyle',
                'original_title' => 'sepatu lari running shoes pria breathable mesh ringan empuk sport jogging',
                'original_desc' => "Sepatu olahraga lari bahan mesh berpori menjaga sirkulasi udara\nBantalan midsole rebound empuk meredam guncangan langkah kaki",
                'image_urls' => [
                    'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600&auto=format&fit=crop&q=80',
                ],
            ],
            [
                'external_product_id' => 'EXT-SNK-003',
                'category_id' => 'cat_sepatu',
                'category_name' => 'Sepatu & Sneakers Lifestyle',
                'original_title' => 'sandal slide pria casual eva empuk anti slip waterproof outdoor santai hitam',
                'original_desc' => "Sandal selop bahan full EVA phylon sangat ringan dan elastis\nTahan air mudah dibersihkan dan nyaman dipakai di segala cuaca",
                'image_urls' => [
                    'https://images.unsplash.com/photo-1603808033192-082d6919d3e1?w=600&auto=format&fit=crop&q=80',
                ],
            ],
            [
                'external_product_id' => 'EXT-SNK-004',
                'category_id' => 'cat_sepatu',
                'category_name' => 'Sepatu & Sneakers Lifestyle',
                'original_title' => 'sepatu slip on pria rajut knit elastis tanpa tali casual kerja kuliah abu',
                'original_desc' => "Sepatu slip on praktis tinggal pakai tanpa tali\nBahan rajutan stretch mengikuti bentuk kaki, sirkulasi udara lancar",
                'image_urls' => [
                    'https://images.unsplash.com/photo-1560769629-975ec94e6a86?w=600&auto=format&fit=crop&q=80',
                ],
            ],
            [
                'external_product_id' => 'EXT-SNK-005',
                'category_id' => 'cat_sepatu',
                'category_name' => 'Sepatu & Sneakers Lifestyle',
                'original_title' => 'sepatu boots pria kulit sintetis tali tinggi casual touring vintage cokelat',
                'original_desc' => "Sepatu boots gaya vintage bahan kulit sintetis tebal dan kuat\nSol grip bergerigi tangguh di medan basah dan bebatuan",
                'image_urls' => [
                    'https://images.unsplash.com/photo-1520639888713-7851133b1ed0?w=600&auto=format&fit=crop&q=80',
                ],
            ],
            [
                'external_product_id' => 'EXT-SNK-006',
                'category_id' => 'cat_sepatu',
                'category_name' => 'Sepatu & Sneakers Lifestyle',
                'original_title' => 'kaos kaki pria semata kaki katun breathable anti bau 3 pasang pack hitam putih',
                'original_desc' => "Kaos kaki model pendek semata kaki bahan katun spandek elastis\nMenyerap keringat dengan baik dan nyaman dipakai seharian bersama sneakers",
                'image_urls' => [
                    'https://images.unsplash.com/photo-1582966772680-860e372bb558?w=600&auto=format&fit=crop&q=80',
                ],
            ],
        ];
    }
}
