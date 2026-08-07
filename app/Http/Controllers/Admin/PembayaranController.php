<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    private const ITEMS_PER_PAGE = 20;

    public function index(Request $request)
    {
        $query = Pembayaran::with(['user', 'pengajuan.program']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pembayaranList = $query
            ->orderByRaw("FIELD(status, 'processing', 'pending', 'success', 'failed', 'expired', 'refunded')")
            ->orderByDesc('updated_at')
            ->paginate(self::ITEMS_PER_PAGE)
            ->withQueryString();

        return view('admin.pembayaran.index', compact('pembayaranList'));
    }

    public function show($id)
    {
        $pembayaran = Pembayaran::with(['user', 'pengajuan.program'])->findOrFail($id);

        return view('admin.pembayaran.show', compact('pembayaran'));
    }

    public function verify(Request $request, $id)
    {
        return redirect()->route('admin.pembayaran.show', $id)
            ->with('error', 'Pembayaran Midtrans diverifikasi otomatis dari status transaksi.');
    }

    public function reject(Request $request, $id)
    {
        return redirect()->route('admin.pembayaran.show', $id)
            ->with('error', 'Status pembayaran Midtrans tidak dapat ditolak secara manual.');
    }
}
