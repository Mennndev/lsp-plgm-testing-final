<?php

namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PendaftaranController extends Controller
{
    public function create()
    {
        return view('daftar');
    }

    public function store(Request $request)
    {
        // VALIDASI
        $validated = $request->validate([
            // User
            'nama'              => ['required', 'string', 'max:255'],
            'email'             => ['required', 'email', 'max:255', 'unique:users,email'],
            'no_hp'             => ['required', 'string', 'max:20'],

            // Pendaftaran
            'jenis_kelamin'     => ['required', 'in:Laki-laki,Perempuan'],
            'tempat_lahir'      => ['required', 'string', 'max:255'],
            'tanggal_lahir'     => ['required', 'date'],

            'nik'               => ['required', 'digits:16'],

            'password'          => ['required', 'string', 'min:6', 'confirmed'],

            'ttd_digital'       => ['required', 'string'],
            'setuju'            => ['accepted'],
        ]);

        // 1) BUAT USER DULU
        $user = User::create([
            'nama'      => $validated['nama'],
            'email'     => $validated['email'],
            'no_hp'     => $validated['no_hp'],
            'password'  => Hash::make($validated['password']),
            'role'      => 'user',      // sesuai enum di tabel users
        ]);

        // 2) SIMPAN TTD (base64) → file → kolom ttd_path
        $ttdPath = null;
        if (!empty($validated['ttd_digital'])) {
            [$meta, $data] = explode(',', $validated['ttd_digital']);
            $binary = base64_decode($data);

            $filename = 'ttd_' . time() . '_' . Str::random(10) . '.png';
            $ttdPath = 'ttd/' . $filename;

            Storage::disk('public')->put($ttdPath, $binary);
        }

        // 3) SIMPAN KE TABEL PENDAFTARANS
        Pendaftaran::create([
            'user_id'       => $user->id,
            'email'         => $validated['email'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'tempat_lahir'  => $validated['tempat_lahir'],
            'tanggal_lahir' => $validated['tanggal_lahir'],
            'alamat'        => null,  // Moved to profile
            'kota'          => null,  // Moved to profile
            'provinsi'      => null,  // Moved to profile

            'pendidikan'    => null,  // Moved to profile
            'pekerjaan'     => null,  // Moved to profile
            'instansi'      => null,  // Moved to profile

            // karena field skema & jadwal tidak dipakai di form, biarkan null
            'skema'         => null,
            'jadwal'        => null,

            'no_ktp'        => $validated['nik'],  // Stores NIK (16-digit national ID). Column name 'no_ktp' retained for backward compatibility
            'ktp_path'      => null,  // KTP upload removed
            'ttd_path'      => $ttdPath,
            'setuju'        => 1, // karena sudah divalidasi "accepted"
        ]);

        return redirect()
            ->route('pendaftaran.create')
            ->with('success', 'Pendaftaran berhasil!');
    }
}
