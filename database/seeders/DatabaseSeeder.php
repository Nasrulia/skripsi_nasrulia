<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Ekspedisi;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  public function run(): void
  {
    $this->call([
        AturanChatbotSeeder::class,
        KomponenRakitanSeeder::class,
        ProdukAsusSeeder::class,
    ]);

    User::firstOrCreate(
        ['email' => 'admin@gmail.com'],
        ['name' => 'Admin NJK', 'password' => bcrypt('password'), 'peran' => 'admin']
    );

    User::firstOrCreate(
        ['email' => 'kasir@gmail.com'],
        ['name' => 'Kasir NJK', 'password' => bcrypt('password'), 'peran' => 'kasir']
    );

    User::firstOrCreate(
        ['email' => 'user@gmail.com'],
        ['name' => 'Pelanggan', 'password' => bcrypt('password'), 'peran' => 'pelanggan']
    );

    User::firstOrCreate(
        ['email' => 'teknisi@gmail.com'],
        ['name' => 'Teknisi NJK', 'password' => bcrypt('password'), 'peran' => 'teknisi']
    );

    Ekspedisi::firstOrCreate(
        ['nama_ekspedisi' => 'JNE'],
        ['ongkir_per_km' => 2000]
    );

    Ekspedisi::firstOrCreate(
        ['nama_ekspedisi' => 'TIKI'],
        ['ongkir_per_km' => 2500]
    );

    Ekspedisi::firstOrCreate(
        ['nama_ekspedisi' => 'SiCepat'],
        ['ongkir_per_km' => 1500]
    );
  }
}
