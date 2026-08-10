<?php

namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

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
            'ttd_digital' => ['required', 'string', 'regex:/^data:image\/(png|jpe?g);base64,/i'],
            'setuju' => ['accepted'],
        ], [
            'email.unique' => 'Email sudah terdaftar. Silakan gunakan email lain atau login dengan akun yang sudah ada.',
            'no_hp.unique' => 'Nomor handphone sudah terdaftar. Silakan gunakan nomor handphone lain.',
            'nik.unique' => 'NIK sudah terdaftar. Satu NIK hanya dapat digunakan untuk satu akun Asesi.',
        ]);

        [$metadata, $encodedSignature] = explode(',', $validated['ttd_digital'], 2);
        $signature = base64_decode($encodedSignature, true);

        if ($signature === false) {
            throw ValidationException::withMessages([
                'ttd_digital' => 'Data tanda tangan tidak valid.',
            ]);
        }

        $extension = str_contains(strtolower($metadata), 'jpeg') || str_contains(strtolower($metadata), 'jpg')
            ? 'jpg'
            : 'png';
        $ttdPath = 'ttd/ttd_'.now()->format('YmdHis').'_'.Str::random(10).'.'.$extension;

        Storage::disk('public')->put($ttdPath, $signature);

        try {
            DB::transaction(function () use ($validated, $ttdPath): void {
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
                    'ttd_path' => $ttdPath,
                    'setuju' => true,
                ]);
            });
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($ttdPath);
            throw $exception;
        }

        return redirect()
            ->route('pendaftaran.create')
            ->with('success', 'Pendaftaran berhasil!');
    }
}
