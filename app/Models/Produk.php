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
        'foto',
        'deskripsi'
    ];

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