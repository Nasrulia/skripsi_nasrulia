<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Komplain;
use Illuminate\Support\Facades\Auth;

class KomplainController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Komplain::with(['transaksi', 'user'])->latest();

        // Filter berdasarkan peran/role
        if ($user->peran === 'kasir') {
            $query->where('tipe', 'penjualan');
        } elseif ($user->peran === 'teknisi') {
            $query->where('tipe', 'servis');
        }
        // Admin bisa melihat semua tipe komplain

        // Filter berdasarkan status jika ada
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $komplain = $query->paginate(10)->withQueryString();

        return view('komplain.index', compact('komplain'));
    }

    public function selesai($id)
    {
        $user = Auth::user();
        $komplain = Komplain::findOrFail($id);

        // Validasi hak akses berdasarkan peran
        if ($user->peran === 'kasir' && $komplain->tipe !== 'penjualan') {
            abort(403, 'Anda tidak memiliki akses untuk menyelesaikan komplain ini.');
        }

        if ($user->peran === 'teknisi' && $komplain->tipe !== 'servis') {
            abort(403, 'Anda tidak memiliki akses untuk menyelesaikan komplain ini.');
        }

        $komplain->update(['status' => 'selesai']);

        return redirect()->route('komplain.index')->with('success', 'Komplain berhasil diselesaikan!');
    }
}
