<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TeknisiManageController extends Controller
{
    public function index()
    {
        $teknisi = User::where('peran', 'teknisi')->latest()->get();
        return view('teknisi.manage', compact('teknisi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'no_whatsapp' => 'required|string|max:20|unique:users,no_whatsapp',
            'password' => 'required|string|min:8',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'no_whatsapp' => $request->no_whatsapp,
            'password' => Hash::make($request->password),
            'peran' => 'teknisi',
        ]);

        return redirect()->route('data-teknisi.index')->with('success', 'Teknisi baru berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $user = User::where('peran', 'teknisi')->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'no_whatsapp' => 'required|string|max:20|unique:users,no_whatsapp,' . $id,
            'password' => 'nullable|string|min:8',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'no_whatsapp' => $request->no_whatsapp,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('data-teknisi.index')->with('success', 'Data teknisi berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $user = User::where('peran', 'teknisi')->findOrFail($id);
        $user->delete();

        return redirect()->route('data-teknisi.index')->with('success', 'Teknisi berhasil dihapus!');
    }
}
