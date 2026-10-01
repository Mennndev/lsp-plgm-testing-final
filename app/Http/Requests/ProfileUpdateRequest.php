<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->user()?->id;
        $pendaftaranId = \App\Models\Pendaftaran::where('user_id', $userId)->value('id');

        return [
            'nama' => ['required', 'string', 'max:100'],
            'no_hp' => [
                'required',
                'string',
                'max:20',
                Rule::unique('users', 'no_hp')->ignore($userId),
            ],
            'tempat_lahir' => ['required', 'string', 'max:100'],
            'tanggal_lahir' => ['required', 'date', 'before:today'],
            'jenis_kelamin' => ['required', Rule::in(['Laki-laki', 'Perempuan'])],
            'no_ktp' => [
                'required',
                'digits:16',
                Rule::unique('pendaftarans', 'no_ktp')->ignore($pendaftaranId),
            ],
            'alamat' => ['required', 'string'],
            'kota' => ['required', 'string', 'max:100'],
            'provinsi' => ['required', 'string', 'max:100'],
            'pendidikan' => ['required', Rule::in(['SD', 'SMP', 'SMA/SMK', 'D1', 'D2', 'D3', 'S1', 'S2', 'S3'])],
            'pekerjaan' => [
                'required',
                Rule::in([
                    'Belum/Tidak Bekerja',
                    'Mengurus Rumah Tangga',
                    'Pelajar/Mahasiswa',
                    'Pensiunan',
                    'Pegawai Negeri Sipil (PNS)',
                    'Tentara Nasional Indonesia (TNI)',
                    'Kepolisian RI (POLRI)',
                    'Karyawan Swasta',
                    'Karyawan BUMN',
                    'Karyawan BUMD',
                    'Wiraswasta',
                    'Lainnya',
                ]),
            ],
            'instansi' => ['nullable', 'string', 'max:150'],
            'jabatan' => ['nullable', 'string', 'max:150'],
            'email_instansi' => ['nullable', 'email', 'max:150'],
            'alamat_instansi' => ['nullable', 'string', 'max:500'],
            'telepon_instansi' => ['nullable', 'string', 'max:30'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama wajib diisi.',
            'no_hp.required' => 'No. HP wajib diisi.',
            'no_hp.unique' => 'Nomor handphone sudah digunakan oleh akun lain.',
            'tempat_lahir.required' => 'Tempat lahir wajib diisi.',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            'tanggal_lahir.before' => 'Tanggal lahir harus sebelum hari ini.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'no_ktp.required' => 'NIK/No. KTP wajib diisi.',
            'no_ktp.digits' => 'NIK harus 16 digit angka.',
            'no_ktp.unique' => 'NIK sudah digunakan oleh akun lain.',
            'alamat.required' => 'Alamat wajib diisi.',
            'kota.required' => 'Kota/Kabupaten wajib diisi.',
            'provinsi.required' => 'Provinsi wajib diisi.',
            'pendidikan.required' => 'Pendidikan terakhir wajib diisi.',
            'pekerjaan.required' => 'Pekerjaan wajib diisi.',
            'email_instansi.email' => 'Email institusi/perusahaan harus berupa alamat email yang valid.',
        ];
    }
}
