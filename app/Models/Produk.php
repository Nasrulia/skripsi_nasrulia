<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk';

    protected $fillable = [
        'kategori_id',
        'merk',
        'nama_produk',
        'stok',
        'harga_beli',
        'harga_jual',
        'berat_gram',
        'ukuran_packing',
        'foto',
        'deskripsi'
    ];

    public function getNominalPackingAttribute(): int
    {
        $rates = [
            'kecil' => 15000,
            'sedang' => 25000,
            'besar' => 40000,
            'ekstra_besar' => 50000,
        ];

        return $rates[$this->ukuran_packing] ?? 15000;
    }

    // Ini fungsi relasi yang bikin error tadi karena hilang
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function detail()
    {
        return $this->hasMany(TransaksiDetail::class, 'produk_id');
    }
}