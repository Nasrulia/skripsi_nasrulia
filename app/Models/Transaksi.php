<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';

    protected $fillable = [
        'kode_transaksi',
        'user_id',
        'nama_pelanggan',
        'tipe',
        'total_bayar',
        'metode_pembayaran',
        'status',
        'metode_pengambilan',
        'ekspedisi_id',
        'jarak_km',
        'ongkir',
        'alamat_pengiriman',
        'bukti_bayar',
        'no_resi',
        'status_pengiriman',
        'estimasi_diambil',
        'nominal_dp',
        'batas_waktu_pengambilan',
    ];

    public function detail(): HasMany
    {
        return $this->hasMany(TransaksiDetail::class, 'transaksi_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function servisDetail(): HasMany
    {
        return $this->hasMany(ServisDetail::class, 'transaksi_id');
    }

    public function scopeLunas($query)
    {
        return $query->where('status', 'Lunas');
    }

    public function scopePenjualan($query)
    {
        return $query->where('tipe', 'penjualan');
    }

    public function scopeServis($query)
    {
        return $query->where('tipe', 'servis');
    }

    public function ekspedisi()
    {
        return $this->belongsTo(Ekspedisi::class);
    }
}
