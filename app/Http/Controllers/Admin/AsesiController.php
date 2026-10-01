<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use Illuminate\Http\Request;

class AsesiController extends Controller
{
    public function index(Request $request)
    {
        $query = Pendaftaran::with(['user', 'program']);

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));

            $query->where(function ($builder) use ($search) {
                $builder->where('email', 'like', "%{$search}%")
                    ->orWhere('skema', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('nama', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                $query->where('setuju', false);
            } elseif ($request->status === 'disetujui') {
                $query->where('setuju', true);
            }
        }

        $asesiList = $query->orderByDesc('created_at')->paginate(10)->withQueryString();
        $totalAsesi = Pendaftaran::count();
        $totalPending = Pendaftaran::where('setuju', false)->count();
        $totalDisetujui = Pendaftaran::where('setuju', true)->count();

        return view('admin.asesi.index', compact(
            'asesiList',
            'totalAsesi',
            'totalPending',
            'totalDisetujui'
        ));
    }

    public function show(string $id)
    {
        $asesi = Pendaftaran::with(['user', 'program'])->findOrFail($id);

        return view('admin.asesi.show', compact('asesi'));
    }
}
