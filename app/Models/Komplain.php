<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Komplain extends Model
{
    protected $table = 'komplain';

    protected $fillable = [
        'transaksi_id',
        'kode_transaksi',
        'user_id',
        'nama_pelanggan',
        'no_whatsapp',
        'deskripsi',
        'tipe',
        'status',
    ];

    public function transaksi(): BelongsTo
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
