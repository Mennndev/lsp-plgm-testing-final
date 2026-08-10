<?php

namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PendaftaranController extends Controller
{
    public function create()
    {
        return view('daftar');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'no_hp' => ['required', 'string', 'max:20', 'unique:users,no_hp'],
            'jenis_kelamin' => ['required', 'in:Laki-laki,Perempuan'],
            'tempat_lahir' => ['required', 'string', 'max:255'],
            'tanggal_lahir' => ['required', 'date', 'before:today'],
            'nik' => ['required', 'digits:16', 'unique:pendaftarans,no_ktp'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'setuju' => ['accepted'],
        ], [
            'email.unique' => 'Email sudah terdaftar. Silakan gunakan email lain atau login dengan akun yang sudah ada.',
            'no_hp.unique' => 'Nomor handphone sudah terdaftar. Silakan gunakan nomor handphone lain.',
            'nik.unique' => 'NIK sudah terdaftar. Satu NIK hanya dapat digunakan untuk satu akun Asesi.',
        ]);

        DB::transaction(function () use ($validated): void {
            $user = User::create([
                'nama' => $validated['nama'],
                'email' => $validated['email'],
                'no_hp' => $validated['no_hp'],
                'password' => Hash::make($validated['password']),
                'role' => 'user',
            ]);

            Pendaftaran::create([
                'user_id' => $user->id,
                'email' => $validated['email'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'tempat_lahir' => $validated['tempat_lahir'],
                'tanggal_lahir' => $validated['tanggal_lahir'],
                'alamat' => null,
                'kota' => null,
                'provinsi' => null,
                'pendidikan' => null,
                'pekerjaan' => null,
                'instansi' => null,
                'skema' => null,
                'jadwal' => null,
                'no_ktp' => $validated['nik'],
                'ktp_path' => null,
                // Tanda tangan tidak lagi diminta saat membuat akun. Tanda tangan
                // resmi disimpan pada APL-01 ketika Asesi mengajukan skema.
                'ttd_path' => null,
                'setuju' => true,
            ]);
        });

        return redirect()
            ->route('pendaftaran.create')
            ->with('success', 'Pendaftaran berhasil!');
    }
}
