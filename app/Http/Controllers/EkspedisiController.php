<?php

namespace App\Http\Controllers;

use App\Models\Ekspedisi;
use Illuminate\Http\Request;

class EkspedisiController extends Controller
{
    public function index()
    {
        $ekspedisi = Ekspedisi::latest()->get();
        return view('ekspedisi.index', compact('ekspedisi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_ekspedisi' => 'required|string|max:255',
            'ongkir_per_km' => 'nullable|numeric|min:0',
        ]);

        $data = $request->all();
        $data['ongkir_per_km'] = $data['ongkir_per_km'] ?? 0;

        Ekspedisi::create($data);

        return redirect()->route('ekspedisi.index')->with('success', 'Ekspedisi berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_ekspedisi' => 'required|string|max:255',
            'ongkir_per_km' => 'nullable|numeric|min:0',
        ]);

        $ekspedisi = Ekspedisi::findOrFail($id);
        $data = $request->all();
        $data['ongkir_per_km'] = $data['ongkir_per_km'] ?? 0;
        $ekspedisi->update($data);

        return redirect()->route('ekspedisi.index')->with('success', 'Ekspedisi berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $ekspedisi = Ekspedisi::findOrFail($id);
        $ekspedisi->delete();

        return redirect()->route('ekspedisi.index')->with('success', 'Ekspedisi berhasil dihapus!');
    }
}
