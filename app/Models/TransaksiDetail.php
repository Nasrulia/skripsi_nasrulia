<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiDetail extends Model
{
    use HasFactory;

    protected $table = 'transaksi_detail';

    // Beri izin mass-assignment (Ini yang bikin error tadi)
    protected $fillable = [
        'transaksi_id', 
        'produk_id', 
        'jasa_id', 
        'jumlah', 
        'harga_satuan', 
        'subtotal'
    ];

    // Relasi balik ke Produk dan Jasa
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    public function jasa()
    {
        return $this->belongsTo(JasaServis::class, 'jasa_id');
    }

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id');
    }
}