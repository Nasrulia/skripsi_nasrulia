<?php

return [
    /*
    |--------------------------------------------------------------------------
    | RajaOngkir API Key & Base URL
    |--------------------------------------------------------------------------
    */
    'api_key' => env('RAJAONGKIR_API_KEY', ''),
    'package_type' => env('RAJAONGKIR_PACKAGE_TYPE', 'starter'), // starter, basic, pro
    'base_url' => env('RAJAONGKIR_BASE_URL', 'https://api.rajaongkir.com/starter/'),

    /*
    |--------------------------------------------------------------------------
    | Toko Origin (Asal Pengiriman)
    |--------------------------------------------------------------------------
    | CV Nusantara Jaya Computer, Jl. Pahlawan No. 88 (Kampung Melayu), Banjarmasin
    | City ID 36 = Kota Banjarmasin, Province ID 12 = Kalimantan Selatan
    */
    'origin_city_id' => env('RAJAONGKIR_ORIGIN_CITY_ID', 36),
    'origin_province_id' => env('RAJAONGKIR_ORIGIN_PROVINCE_ID', 12),
    'origin_city_name' => env('RAJAONGKIR_ORIGIN_CITY_NAME', 'Banjarmasin'),
    'origin_province_name' => env('RAJAONGKIR_ORIGIN_PROVINCE_NAME', 'Kalimantan Selatan'),

    /*
    |--------------------------------------------------------------------------
    | Kurir Ekspedisi yang Didukung
    |--------------------------------------------------------------------------
    */
    'supported_couriers' => [
        'jne' => 'JNE Express',
        'pos' => 'POS Indonesia',
        'tiki' => 'TIKI',
        'sicepat' => 'SiCepat Ekspres',
    ],

    /*
    |--------------------------------------------------------------------------
    | Tarif Ongkos Packing Berdasarkan Ukuran Barang (Rp 15.000 - Rp 50.000)
    |--------------------------------------------------------------------------
    */
    'packing_rates' => [
        'kecil' => [
            'nama' => 'Ukuran Kecil (Aksesoris/Tinta/Part Kecil)',
            'biaya' => 15000,
            'deskripsi' => 'Lapisan Bubble Wrap 3 Lapis + Polymailer / Box Kecil Tebal',
        ],
        'sedang' => [
            'nama' => 'Ukuran Sedang (Keyboard/Headset/Komponen)',
            'biaya' => 25000,
            'deskripsi' => 'Lapisan Bubble Wrap Tebal + Kardus Double Wall + Lakban Fragile',
        ],
        'besar' => [
            'nama' => 'Ukuran Besar (Laptop/Monitor/Printer Standar)',
            'biaya' => 40000,
            'deskripsi' => 'Full Bubble Wrap + Kardus Tambahan + Proteksi Sudut + Stiker Asuransi/Fragile',
        ],
        'ekstra_besar' => [
            'nama' => 'Ukuran Ekstra Besar (PC Rakitan Full Set/Printer Besar)',
            'biaya' => 50000,
            'deskripsi' => 'Proteksi Kayu (Palet/Rangka) + Bubble Wrap Tebal + Busa Corner Guard',
        ],
    ],
];
