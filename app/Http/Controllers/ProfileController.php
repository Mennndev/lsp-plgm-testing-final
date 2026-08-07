<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Pendaftaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();
        $pendaftaran = Pendaftaran::where('user_id', $user->id)->latest()->first();

        return view('ProfileUser.edit', compact('user', 'pendaftaran'));
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        DB::transaction(function () use ($user, $validated): void {
            $user->update([
                'nama' => $validated['nama'],
                'no_hp' => $validated['no_hp'] ?? null,
            ]);

            Pendaftaran::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'email' => $user->email,
                    'tempat_lahir' => $validated['tempat_lahir'],
                    'tanggal_lahir' => $validated['tanggal_lahir'],
                    'jenis_kelamin' => $validated['jenis_kelamin'],
                    'no_ktp' => $validated['no_ktp'],
                    'alamat' => $validated['alamat'],
                    'kota' => $validated['kota'],
                    'provinsi' => $validated['provinsi'],
                    'pendidikan' => $validated['pendidikan'],
                    'pekerjaan' => $validated['pekerjaan'],
                    'instansi' => $validated['instansi'] ?? null,
                ]
            );
        });

        return redirect()->route('ProfileUser.edit')->with('status', 'profile-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        Auth::logout();
        $user->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = $request->user();

        if (! Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors([
                'current_password' => 'Password saat ini tidak sesuai.',
            ]);
        }

        $user->update(['password' => $validated['password']]);

        return back()->with('password-updated', true);
    }

    public function updateSignature(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ttd_file' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            'ttd_digital' => ['nullable', 'string', 'regex:/^data:image\/(png|jpe?g);base64,/i'],
        ]);

        if (! $request->hasFile('ttd_file') && empty($validated['ttd_digital'])) {
            throw ValidationException::withMessages([
                'ttd_file' => 'Pilih file atau buat tanda tangan digital.',
            ]);
        }

        $user = $request->user();
        $pendaftaran = Pendaftaran::where('user_id', $user->id)->latest()->first();

        if (! $pendaftaran) {
            return back()->withErrors([
                'ttd_file' => 'Tidak ditemukan data pendaftaran yang terhubung dengan akun ini.',
            ]);
        }

        $oldPath = $pendaftaran->ttd_path;
        $newPath = null;

        if ($request->hasFile('ttd_file')) {
            $newPath = $request->file('ttd_file')->store('ttd', 'public');
        } elseif (! empty($validated['ttd_digital'])) {
            [, $encoded] = explode(',', $validated['ttd_digital'], 2);
            $binary = base64_decode($encoded, true);

            if ($binary === false) {
                throw ValidationException::withMessages([
                    'ttd_digital' => 'Data tanda tangan tidak valid.',
                ]);
            }

            $newPath = 'ttd/ttd_'.now()->format('YmdHis').'_'.Str::random(10).'.png';
            Storage::disk('public')->put($newPath, $binary);
        }

        $pendaftaran->update(['ttd_path' => $newPath]);

        if ($oldPath && $oldPath !== $newPath) {
            Storage::disk('public')->delete($oldPath);
        }

        return back()->with('status', 'signature-updated');
    }
}
