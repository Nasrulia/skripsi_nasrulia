<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    /**
     * Tandai notifikasi sebagai dibaca.
     */
    public function markAsRead(Request $request, $id)
    {
        $notifikasi = Notifikasi::findOrFail($id);
        $notifikasi->is_read = true;
        $notifikasi->save();

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        if ($request->has('go_to_link') && $notifikasi->link) {
            $link = $notifikasi->link;
            if (!str_contains($link, 'kode=') && preg_match('/TRX-[A-Z0-9_-]+/i', $notifikasi->pesan, $matches)) {
                $trxCode = strtoupper($matches[0]);
                $separator = str_contains($link, '?') ? '&' : '?';
                $link .= $separator . 'kode=' . $trxCode;
            }
            return redirect($link);
        }

        return redirect()->back()->with('success', 'Notifikasi berhasil ditandai dibaca.');
    }

    /**
     * Tandai semua notifikasi sebagai dibaca.
     */
    public function markAllAsRead()
    {
        Notifikasi::where('is_read', false)->update(['is_read' => true]);

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Semua notifikasi ditandai dibaca.');
    }
}
