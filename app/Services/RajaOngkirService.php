<?php

namespace App\Services;

use App\Models\Produk;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RajaOngkirService
{
    protected string $apiKey;
    protected string $baseUrl;
    protected int $originCityId;
    protected string $originCityName;

    public function __construct()
    {
        $this->apiKey = (string) config('rajaongkir.api_key', '');
        $this->baseUrl = rtrim((string) config('rajaongkir.base_url', 'https://api.rajaongkir.com/starter/'), '/') . '/';
        $this->originCityId = (int) config('rajaongkir.origin_city_id', 36); // Banjarmasin
        $this->originCityName = (string) config('rajaongkir.origin_city_name', 'Banjarmasin');
    }

    /**
     * Mengambil daftar semua provinsi Indonesia.
     */
    public function getProvinces(): array
    {
        return Cache::remember('rajaongkir_provinces', 60 * 60 * 24 * 7, function () {
            if (!empty($this->apiKey)) {
                try {
                    $response = Http::withHeaders(['key' => $this->apiKey])
                        ->timeout(6)
                        ->get($this->baseUrl . 'province');

                    if ($response->successful()) {
                        $data = $response->json('rajaongkir.results');
                        if (is_array($data) && count($data) > 0) {
                            return $data;
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning('RajaOngkir getProvinces API error, using fallback: ' . $e->getMessage());
                }
            }

            return $this->getFallbackProvinces();
        });
    }

    /**
     * Mengambil daftar kota/kabupaten berdasarkan ID Provinsi.
     */
    public function getCities(int $provinceId): array
    {
        return Cache::remember("rajaongkir_cities_{$provinceId}", 60 * 60 * 24 * 7, function () use ($provinceId) {
            if (!empty($this->apiKey)) {
                try {
                    $response = Http::withHeaders(['key' => $this->apiKey])
                        ->timeout(6)
                        ->get($this->baseUrl . 'city', ['province' => $provinceId]);

                    if ($response->successful()) {
                        $data = $response->json('rajaongkir.results');
                        if (is_array($data) && count($data) > 0) {
                            return $data;
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning("RajaOngkir getCities({$provinceId}) error, using fallback: " . $e->getMessage());
                }
            }

            return $this->getFallbackCities($provinceId);
        });
    }

    /**
     * Menghitung ongkos kirim dari Kota Banjarmasin ke kota tujuan.
     */
    public function calculateCost(int $destinationCityId, int $weightGram, string $courier = 'jne'): array
    {
        $weightGram = max(1000, $weightGram);
        $courier = strtolower($courier);

        if (!empty($this->apiKey)) {
            try {
                $response = Http::withHeaders(['key' => $this->apiKey])
                    ->timeout(8)
                    ->post($this->baseUrl . 'cost', [
                        'origin' => $this->originCityId,
                        'destination' => $destinationCityId,
                        'weight' => $weightGram,
                        'courier' => $courier,
                    ]);

                if ($response->successful()) {
                    $results = $response->json('rajaongkir.results');
                    if (!empty($results) && isset($results[0]['costs'])) {
                        $services = [];
                        foreach ($results[0]['costs'] as $c) {
                            $costDetail = $c['cost'][0] ?? null;
                            if ($costDetail) {
                                $services[] = [
                                    'courier_code' => $courier,
                                    'courier_name' => $results[0]['name'] ?? strtoupper($courier),
                                    'service' => $c['service'],
                                    'description' => $c['description'],
                                    'cost' => (int) $costDetail['value'],
                                    'etd' => !empty($costDetail['etd']) ? str_replace(' HARI', '', $costDetail['etd']) . ' hari' : '2-4 hari',
                                ];
                            }
                        }

                        if (!empty($services)) {
                            return [
                                'success' => true,
                                'source' => 'rajaongkir_api',
                                'courier' => $results[0]['name'] ?? strtoupper($courier),
                                'services' => $services,
                            ];
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::warning("RajaOngkir calculateCost error for {$courier} to city {$destinationCityId}: " . $e->getMessage());
            }
        }

        return [
            'success' => true,
            'source' => 'fallback_calculation',
            'courier' => strtoupper($courier),
            'services' => $this->generateFallbackCosts($destinationCityId, $weightGram, $courier),
        ];
    }

    /**
     * Menghitung total berat produk di keranjang (dalam Gram, min 1000g).
     */
    public function calculateTotalWeight(array $cartItems): int
    {
        $totalWeight = 0;

        foreach ($cartItems as $id => $details) {
            $product = Produk::find($id);
            $itemWeight = $product ? (int) ($product->berat_gram ?: 1000) : 1000;
            $qty = isset($details['jumlah']) ? (int) $details['jumlah'] : 1;
            $totalWeight += ($itemWeight * $qty);
        }

        return max(1000, $totalWeight);
    }

    /**
     * Menghitung ongkos packing berdasarkan ukuran terbesar item di keranjang.
     * Range: Rp 15.000 - Rp 50.000
     */
    public function calculatePackingCost(array $cartItems): array
    {
        $packingRates = config('rajaongkir.packing_rates');
        $priority = ['kecil' => 1, 'sedang' => 2, 'besar' => 3, 'ekstra_besar' => 4];
        
        $highestTier = 'kecil';
        $highestVal = 1;

        foreach ($cartItems as $id => $details) {
            $product = Produk::with('kategori')->find($id);
            $tier = 'kecil';

            if ($product) {
                if (!empty($product->ukuran_packing)) {
                    $tier = $product->ukuran_packing;
                } else {
                    $cat = strtoupper($product->kategori->nama_kategori ?? '');
                    $name = strtoupper($product->nama_produk ?? '');

                    if (str_contains($cat, 'RAKITAN') || str_contains($name, 'RAKITAN')) {
                        $tier = 'ekstra_besar';
                    } elseif (str_contains($cat, 'LAPTOP') || str_contains($cat, 'PRINTER') || str_contains($name, 'MONITOR')) {
                        $tier = 'besar';
                    } elseif (str_contains($cat, 'KEYBOARD') || str_contains($name, 'HEADSET') || str_contains($name, 'MOTHERBOARD')) {
                        $tier = 'sedang';
                    } else {
                        $tier = 'kecil';
                    }
                }
            }

            if (($priority[$tier] ?? 1) > $highestVal) {
                $highestVal = $priority[$tier];
                $highestTier = $tier;
            }
        }

        $config = $packingRates[$highestTier] ?? $packingRates['kecil'];

        return [
            'tier' => $highestTier,
            'nama' => $config['nama'],
            'biaya' => (int) $config['biaya'],
            'deskripsi' => $config['deskripsi'],
        ];
    }

    /**
     * Fallback tarif jika API RajaOngkir offline atau belum diberi API Key.
     */
    protected function generateFallbackCosts(int $destinationCityId, int $weightGram, string $courier): array
    {
        $weightKg = ceil($weightGram / 1000);
        
        // Klasifikasi zona tarif dari Banjarmasin (Kota ID 36)
        $baseRate = 22000; // default tarif antar pulau reguler

        // Banjarmasin & Sekitarnya (Kalimantan Selatan: Banjarbaru, Martapura, Barito Kuala, dll)
        if (in_array($destinationCityId, [36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48])) {
            $baseRate = ($destinationCityId == 36) ? 10000 : 15000;
        } 
        // Kalimantan Tengah, Timur, Barat, Utara (ID 180-230 perkiraan)
        elseif ($destinationCityId >= 180 && $destinationCityId <= 230) {
            $baseRate = 26000;
        } 
        // Jawa & Bali (DKI Jakarta: 151-155, Surabaya: 444, Bandung: 22-23, dll)
        elseif (in_array($destinationCityId, [151, 152, 153, 154, 155, 444, 22, 23, 107, 108, 398, 399, 501, 17])) {
            $baseRate = 32000;
        } 
        // Sumatera, Sulawesi, NTB, NTT
        elseif ($destinationCityId > 450 || $destinationCityId < 35) {
            $baseRate = 42000;
        }

        $services = [];

        switch ($courier) {
            case 'pos':
                $services[] = [
                    'courier_code' => 'pos',
                    'courier_name' => 'POS Indonesia',
                    'service' => 'Pos Reguler',
                    'description' => 'Pos Reguler Nusantara',
                    'cost' => (int) ($baseRate * $weightKg),
                    'etd' => '2-3 hari',
                ];
                $services[] = [
                    'courier_code' => 'pos',
                    'courier_name' => 'POS Indonesia',
                    'service' => 'Pos Next Day',
                    'description' => 'Pos Kilat Khusus / Next Day',
                    'cost' => (int) (($baseRate + 16000) * $weightKg),
                    'etd' => '1-2 hari',
                ];
                break;

            case 'tiki':
                $services[] = [
                    'courier_code' => 'tiki',
                    'courier_name' => 'TIKI',
                    'service' => 'ECO',
                    'description' => 'Economy Service',
                    'cost' => (int) (max(10000, ($baseRate - 4000)) * $weightKg),
                    'etd' => '3-4 hari',
                ];
                $services[] = [
                    'courier_code' => 'tiki',
                    'courier_name' => 'TIKI',
                    'service' => 'REG',
                    'description' => 'Regular Service',
                    'cost' => (int) ($baseRate * $weightKg),
                    'etd' => '2-3 hari',
                ];
                $services[] = [
                    'courier_code' => 'tiki',
                    'courier_name' => 'TIKI',
                    'service' => 'ONS',
                    'description' => 'Over Night Services',
                    'cost' => (int) (($baseRate + 18000) * $weightKg),
                    'etd' => '1 hari',
                ];
                break;

            case 'sicepat':
                $services[] = [
                    'courier_code' => 'sicepat',
                    'courier_name' => 'SiCepat Ekspres',
                    'service' => 'SIUNTUNG',
                    'description' => 'SiCepat Reguler Hemat',
                    'cost' => (int) ($baseRate * $weightKg),
                    'etd' => '2-3 hari',
                ];
                $services[] = [
                    'courier_code' => 'sicepat',
                    'courier_name' => 'SiCepat Ekspres',
                    'service' => 'BEST',
                    'description' => 'Besok Sampai Tujuan',
                    'cost' => (int) (($baseRate + 17000) * $weightKg),
                    'etd' => '1 hari',
                ];
                break;

            case 'jne':
            default:
                $services[] = [
                    'courier_code' => 'jne',
                    'courier_name' => 'JNE Express',
                    'service' => 'OKE',
                    'description' => 'Ongkos Kirim Ekonomis',
                    'cost' => (int) (max(10000, ($baseRate - 4000)) * $weightKg),
                    'etd' => '3-5 hari',
                ];
                $services[] = [
                    'courier_code' => 'jne',
                    'courier_name' => 'JNE Express',
                    'service' => 'REG',
                    'description' => 'Layanan Reguler',
                    'cost' => (int) ($baseRate * $weightKg),
                    'etd' => '2-3 hari',
                ];
                $services[] = [
                    'courier_code' => 'jne',
                    'courier_name' => 'JNE Express',
                    'service' => 'YES',
                    'description' => 'Yakin Esok Sampai',
                    'cost' => (int) (($baseRate + 20000) * $weightKg),
                    'etd' => '1 hari',
                ];
                break;
        }

        return $services;
    }

    /**
     * Fallback data 34 Provinsi Indonesia.
     */
    protected function getFallbackProvinces(): array
    {
        return [
            ['province_id' => '1', 'province' => 'Bali'],
            ['province_id' => '2', 'province' => 'Bangka Belitung'],
            ['province_id' => '3', 'province' => 'Banten'],
            ['province_id' => '4', 'province' => 'Bengkulu'],
            ['province_id' => '5', 'province' => 'DI Yogyakarta'],
            ['province_id' => '6', 'province' => 'DKI Jakarta'],
            ['province_id' => '7', 'province' => 'Gorontalo'],
            ['province_id' => '8', 'province' => 'Jambi'],
            ['province_id' => '9', 'province' => 'Jawa Barat'],
            ['province_id' => '10', 'province' => 'Jawa Tengah'],
            ['province_id' => '11', 'province' => 'Jawa Timur'],
            ['province_id' => '12', 'province' => 'Kalimantan Barat'],
            ['province_id' => '13', 'province' => 'Kalimantan Selatan'],
            ['province_id' => '14', 'province' => 'Kalimantan Tengah'],
            ['province_id' => '15', 'province' => 'Kalimantan Timur'],
            ['province_id' => '16', 'province' => 'Kalimantan Utara'],
            ['province_id' => '17', 'province' => 'Kepulauan Riau'],
            ['province_id' => '18', 'province' => 'Lampung'],
            ['province_id' => '19', 'province' => 'Maluku'],
            ['province_id' => '20', 'province' => 'Maluku Utara'],
            ['province_id' => '21', 'province' => 'Nanggroe Aceh Darussalam (NAD)'],
            ['province_id' => '22', 'province' => 'Nusa Tenggara Barat (NTB)'],
            ['province_id' => '23', 'province' => 'Nusa Tenggara Timur (NTT)'],
            ['province_id' => '24', 'province' => 'Papua'],
            ['province_id' => '25', 'province' => 'Papua Barat'],
            ['province_id' => '26', 'province' => 'Riau'],
            ['province_id' => '27', 'province' => 'Sulawesi Barat'],
            ['province_id' => '28', 'province' => 'Sulawesi Selatan'],
            ['province_id' => '29', 'province' => 'Sulawesi Tengah'],
            ['province_id' => '30', 'province' => 'Sulawesi Tenggara'],
            ['province_id' => '31', 'province' => 'Sulawesi Utara'],
            ['province_id' => '32', 'province' => 'Sumatera Barat'],
            ['province_id' => '33', 'province' => 'Sumatera Selatan'],
            ['province_id' => '34', 'province' => 'Sumatera Utara'],
        ];
    }

    /**
     * Fallback data kota/kabupaten per provinsi.
     */
    protected function getFallbackCities(int $provinceId): array
    {
        $allCities = [
            // Kalimantan Selatan (13)
            13 => [
                ['city_id' => '36', 'province_id' => '13', 'province' => 'Kalimantan Selatan', 'type' => 'Kota', 'city_name' => 'Banjarmasin', 'postal_code' => '70111'],
                ['city_id' => '37', 'province_id' => '13', 'province' => 'Kalimantan Selatan', 'type' => 'Kota', 'city_name' => 'Banjarbaru', 'postal_code' => '70712'],
                ['city_id' => '38', 'province_id' => '13', 'province' => 'Kalimantan Selatan', 'type' => 'Kabupaten', 'city_name' => 'Banjar (Martapura)', 'postal_code' => '70611'],
                ['city_id' => '39', 'province_id' => '13', 'province' => 'Kalimantan Selatan', 'type' => 'Kabupaten', 'city_name' => 'Barito Kuala', 'postal_code' => '70511'],
                ['city_id' => '40', 'province_id' => '13', 'province' => 'Kalimantan Selatan', 'type' => 'Kabupaten', 'city_name' => 'Tapin', 'postal_code' => '71111'],
                ['city_id' => '41', 'province_id' => '13', 'province' => 'Kalimantan Selatan', 'type' => 'Kabupaten', 'city_name' => 'Hulu Sungai Selatan (Kandangan)', 'postal_code' => '71211'],
                ['city_id' => '42', 'province_id' => '13', 'province' => 'Kalimantan Selatan', 'type' => 'Kabupaten', 'city_name' => 'Hulu Sungai Tengah (Barabai)', 'postal_code' => '71311'],
                ['city_id' => '43', 'province_id' => '13', 'province' => 'Kalimantan Selatan', 'type' => 'Kabupaten', 'city_name' => 'Hulu Sungai Utara (Amuntai)', 'postal_code' => '71411'],
                ['city_id' => '44', 'province_id' => '13', 'province' => 'Kalimantan Selatan', 'type' => 'Kabupaten', 'city_name' => 'Tabalong (Tanjung)', 'postal_code' => '71511'],
                ['city_id' => '45', 'province_id' => '13', 'province' => 'Kalimantan Selatan', 'type' => 'Kabupaten', 'city_name' => 'Tanah Laut (Pelaihari)', 'postal_code' => '70811'],
                ['city_id' => '46', 'province_id' => '13', 'province' => 'Kalimantan Selatan', 'type' => 'Kabupaten', 'city_name' => 'Tanah Bumbu (Batulicin)', 'postal_code' => '72211'],
                ['city_id' => '47', 'province_id' => '13', 'province' => 'Kalimantan Selatan', 'type' => 'Kabupaten', 'city_name' => 'Kotabaru', 'postal_code' => '72111'],
                ['city_id' => '48', 'province_id' => '13', 'province' => 'Kalimantan Selatan', 'type' => 'Kabupaten', 'city_name' => 'Balangan (Paringin)', 'postal_code' => '71611'],
            ],
            // DKI Jakarta (6)
            6 => [
                ['city_id' => '151', 'province_id' => '6', 'province' => 'DKI Jakarta', 'type' => 'Kota', 'city_name' => 'Jakarta Barat', 'postal_code' => '11220'],
                ['city_id' => '152', 'province_id' => '6', 'province' => 'DKI Jakarta', 'type' => 'Kota', 'city_name' => 'Jakarta Pusat', 'postal_code' => '10540'],
                ['city_id' => '153', 'province_id' => '6', 'province' => 'DKI Jakarta', 'type' => 'Kota', 'city_name' => 'Jakarta Selatan', 'postal_code' => '12000'],
                ['city_id' => '154', 'province_id' => '6', 'province' => 'DKI Jakarta', 'type' => 'Kota', 'city_name' => 'Jakarta Timur', 'postal_code' => '13330'],
                ['city_id' => '155', 'province_id' => '6', 'province' => 'DKI Jakarta', 'type' => 'Kota', 'city_name' => 'Jakarta Utara', 'postal_code' => '14140'],
            ],
            // Jawa Timur (11)
            11 => [
                ['city_id' => '444', 'province_id' => '11', 'province' => 'Jawa Timur', 'type' => 'Kota', 'city_name' => 'Surabaya', 'postal_code' => '60119'],
                ['city_id' => '256', 'province_id' => '11', 'province' => 'Jawa Timur', 'type' => 'Kota', 'city_name' => 'Malang', 'postal_code' => '65112'],
                ['city_id' => '419', 'province_id' => '11', 'province' => 'Jawa Timur', 'type' => 'Kabupaten', 'city_name' => 'Sidoarjo', 'postal_code' => '61219'],
                ['city_id' => '133', 'province_id' => '11', 'province' => 'Jawa Timur', 'type' => 'Kabupaten', 'city_name' => 'Gresik', 'postal_code' => '61115'],
                ['city_id' => '178', 'province_id' => '11', 'province' => 'Jawa Timur', 'type' => 'Kabupaten', 'city_name' => 'Jember', 'postal_code' => '68113'],
                ['city_id' => '195', 'province_id' => '11', 'province' => 'Jawa Timur', 'type' => 'Kota', 'city_name' => 'Kediri', 'postal_code' => '64125'],
            ],
            // Jawa Barat (9)
            9 => [
                ['city_id' => '22', 'province_id' => '9', 'province' => 'Jawa Barat', 'type' => 'Kota', 'city_name' => 'Bandung', 'postal_code' => '40111'],
                ['city_id' => '54', 'province_id' => '9', 'province' => 'Jawa Barat', 'type' => 'Kota', 'city_name' => 'Bekasi', 'postal_code' => '17121'],
                ['city_id' => '78', 'province_id' => '9', 'province' => 'Jawa Barat', 'type' => 'Kota', 'city_name' => 'Bogor', 'postal_code' => '16111'],
                ['city_id' => '115', 'province_id' => '9', 'province' => 'Jawa Barat', 'type' => 'Kota', 'city_name' => 'Depok', 'postal_code' => '16411'],
                ['city_id' => '108', 'province_id' => '9', 'province' => 'Jawa Barat', 'type' => 'Kota', 'city_name' => 'Cirebon', 'postal_code' => '45111'],
            ],
            // Jawa Tengah (10)
            10 => [
                ['city_id' => '398', 'province_id' => '10', 'province' => 'Jawa Tengah', 'type' => 'Kota', 'city_name' => 'Semarang', 'postal_code' => '50135'],
                ['city_id' => '501', 'province_id' => '10', 'province' => 'Jawa Tengah', 'type' => 'Kota', 'city_name' => 'Surakarta (Solo)', 'postal_code' => '57113'],
                ['city_id' => '249', 'province_id' => '10', 'province' => 'Jawa Tengah', 'type' => 'Kota', 'city_name' => 'Magelang', 'postal_code' => '56133'],
                ['city_id' => '344', 'province_id' => '10', 'province' => 'Jawa Tengah', 'type' => 'Kota', 'city_name' => 'Pekalongan', 'postal_code' => '51112'],
                ['city_id' => '473', 'province_id' => '10', 'province' => 'Jawa Tengah', 'type' => 'Kota', 'city_name' => 'Tegal', 'postal_code' => '52114'],
                ['city_id' => '41', 'province_id' => '10', 'province' => 'Jawa Tengah', 'type' => 'Kabupaten', 'city_name' => 'Banyumas (Purwokerto)', 'postal_code' => '53114'],
            ],
            // DI Yogyakarta (5)
            5 => [
                ['city_id' => '501', 'province_id' => '5', 'province' => 'DI Yogyakarta', 'type' => 'Kota', 'city_name' => 'Yogyakarta', 'postal_code' => '55111'],
                ['city_id' => '419', 'province_id' => '5', 'province' => 'DI Yogyakarta', 'type' => 'Kabupaten', 'city_name' => 'Sleman', 'postal_code' => '55511'],
                ['city_id' => '39', 'province_id' => '5', 'province' => 'DI Yogyakarta', 'type' => 'Kabupaten', 'city_name' => 'Bantul', 'postal_code' => '55711'],
                ['city_id' => '135', 'province_id' => '5', 'province' => 'DI Yogyakarta', 'type' => 'Kabupaten', 'city_name' => 'Gunung Kidul', 'postal_code' => '55811'],
                ['city_id' => '210', 'province_id' => '5', 'province' => 'DI Yogyakarta', 'type' => 'Kabupaten', 'city_name' => 'Kulon Progo', 'postal_code' => '55611'],
            ],
            // Banten (3)
            3 => [
                ['city_id' => '455', 'province_id' => '3', 'province' => 'Banten', 'type' => 'Kota', 'city_name' => 'Tangerang', 'postal_code' => '15111'],
                ['city_id' => '456', 'province_id' => '3', 'province' => 'Banten', 'type' => 'Kota', 'city_name' => 'Tangerang Selatan', 'postal_code' => '15310'],
                ['city_id' => '418', 'province_id' => '3', 'province' => 'Banten', 'type' => 'Kota', 'city_name' => 'Serang', 'postal_code' => '42111'],
                ['city_id' => '106', 'province_id' => '3', 'province' => 'Banten', 'type' => 'Kota', 'city_name' => 'Cilegon', 'postal_code' => '42417'],
            ],
            // Bali (1)
            1 => [
                ['city_id' => '114', 'province_id' => '1', 'province' => 'Bali', 'type' => 'Kota', 'city_name' => 'Denpasar', 'postal_code' => '80111'],
                ['city_id' => '17', 'province_id' => '1', 'province' => 'Bali', 'type' => 'Kabupaten', 'city_name' => 'Badung (Kuta)', 'postal_code' => '80351'],
                ['city_id' => '128', 'province_id' => '1', 'province' => 'Bali', 'type' => 'Kabupaten', 'city_name' => 'Gianyar (Ubud)', 'postal_code' => '80511'],
            ],
            // Kalimantan Tengah (14)
            14 => [
                ['city_id' => '326', 'province_id' => '14', 'province' => 'Kalimantan Tengah', 'type' => 'Kota', 'city_name' => 'Palangka Raya', 'postal_code' => '73111'],
                ['city_id' => '189', 'province_id' => '14', 'province' => 'Kalimantan Tengah', 'type' => 'Kabupaten', 'city_name' => 'Kapuas (Kuala Kapuas)', 'postal_code' => '73511'],
                ['city_id' => '201', 'province_id' => '14', 'province' => 'Kalimantan Tengah', 'type' => 'Kabupaten', 'city_name' => 'Kotawaringin Barat (Pangkalan Bun)', 'postal_code' => '74111'],
                ['city_id' => '202', 'province_id' => '14', 'province' => 'Kalimantan Tengah', 'type' => 'Kabupaten', 'city_name' => 'Kotawaringin Timur (Sampit)', 'postal_code' => '74311'],
            ],
            // Kalimantan Timur (15)
            15 => [
                ['city_id' => '387', 'province_id' => '15', 'province' => 'Kalimantan Timur', 'type' => 'Kota', 'city_name' => 'Samarinda', 'postal_code' => '75111'],
                ['city_id' => '19', 'province_id' => '15', 'province' => 'Kalimantan Timur', 'type' => 'Kota', 'city_name' => 'Balikpapan', 'postal_code' => '76111'],
                ['city_id' => '89', 'province_id' => '15', 'province' => 'Kalimantan Timur', 'type' => 'Kota', 'city_name' => 'Bontang', 'postal_code' => '75311'],
                ['city_id' => '216', 'province_id' => '15', 'province' => 'Kalimantan Timur', 'type' => 'Kabupaten', 'city_name' => 'Kutai Kartanegara (Tenggarong)', 'postal_code' => '75511'],
            ],
            // Kalimantan Barat (12)
            12 => [
                ['city_id' => '365', 'province_id' => '12', 'province' => 'Kalimantan Barat', 'type' => 'Kota', 'city_name' => 'Pontianak', 'postal_code' => '78111'],
                ['city_id' => '424', 'province_id' => '12', 'province' => 'Kalimantan Barat', 'type' => 'Kota', 'city_name' => 'Singkawang', 'postal_code' => '79111'],
                ['city_id' => '213', 'province_id' => '12', 'province' => 'Kalimantan Barat', 'type' => 'Kabupaten', 'city_name' => 'Kubu Raya', 'postal_code' => '78311'],
            ],
            // Sulawesi Selatan (28)
            28 => [
                ['city_id' => '248', 'province_id' => '28', 'province' => 'Sulawesi Selatan', 'type' => 'Kota', 'city_name' => 'Makassar', 'postal_code' => '90111'],
                ['city_id' => '328', 'province_id' => '28', 'province' => 'Sulawesi Selatan', 'type' => 'Kota', 'city_name' => 'Parepare', 'postal_code' => '91111'],
                ['city_id' => '327', 'province_id' => '28', 'province' => 'Sulawesi Selatan', 'type' => 'Kota', 'city_name' => 'Palopo', 'postal_code' => '91911'],
                ['city_id' => '130', 'province_id' => '28', 'province' => 'Sulawesi Selatan', 'type' => 'Kabupaten', 'city_name' => 'Gowa (Sungguminasa)', 'postal_code' => '92111'],
            ],
            // Sumatera Utara (34)
            34 => [
                ['city_id' => '278', 'province_id' => '34', 'province' => 'Sumatera Utara', 'type' => 'Kota', 'city_name' => 'Medan', 'postal_code' => '20111'],
                ['city_id' => '59', 'province_id' => '34', 'province' => 'Sumatera Utara', 'type' => 'Kota', 'city_name' => 'Binjai', 'postal_code' => '20711'],
                ['city_id' => '346', 'province_id' => '34', 'province' => 'Sumatera Utara', 'type' => 'Kota', 'city_name' => 'Pematang Siantar', 'postal_code' => '21111'],
            ],
        ];

        if (isset($allCities[$provinceId])) {
            return $allCities[$provinceId];
        }

        // Generic fallback for any other province
        return [
            ['city_id' => (string) ($provinceId * 10 + 1), 'province_id' => (string) $provinceId, 'province' => 'Provinsi ' . $provinceId, 'type' => 'Kota', 'city_name' => 'Kota Utama', 'postal_code' => '99999'],
            ['city_id' => (string) ($provinceId * 10 + 2), 'province_id' => (string) $provinceId, 'province' => 'Provinsi ' . $provinceId, 'type' => 'Kabupaten', 'city_name' => 'Kabupaten Pusat', 'postal_code' => '99998'],
        ];
    }
}
