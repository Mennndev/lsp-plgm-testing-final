<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePengajuanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $step = (int) $this->input('current_step', 1);

        $rules = [
            'current_step' => 'nullable|integer|min:1|max:4',

            // Program
            'program_pelatihan_id' => 'required|exists:program_pelatihans,id',

            // =====================
            // STEP 1 – APL 01
            // =====================
            'nama_lengkap' => 'required|string|max:255',
            'nik' => 'required|digits:16',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date|before:today',
            'jenis_kelamin' => ['required', Rule::in(['L','P'])],
            'kebangsaan' => 'nullable|string|max:100',
            'alamat_rumah' => 'required|string',
            'kode_pos' => 'nullable|string|max:10',
            'telepon_rumah' => 'nullable|string|max:20',
            'telepon_kantor' => 'nullable|string|max:20',
            'hp' => 'required|string|max:30',
            'email' => 'required|email|max:255',

            'kualifikasi_pendidikan' => 'nullable|string|max:100',
            'pekerjaan' => 'required|string|max:255',
            'nama_institusi' => 'nullable|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'alamat_kantor' => 'nullable|string',
            'fax' => 'nullable|string|max:20',
            'email_kantor' => 'nullable|email|max:255',

            'nama_sertifikat' => 'nullable|string|max:255',
            'nomor_sertifikat' => 'nullable|string|max:255',

            // STEP 2
            'tujuan_asesmen' => 'nullable|array',
            'tujuan_asesmen.*' => Rule::in(['PKT','RPL','RCC','Lainnya']),
            'bukti_penyertaan_dasar' => 'nullable|string',
            'bukti_administrasif' => 'nullable|string',
            'catatan' => 'nullable|string',

            'jenis_dokumen' => 'nullable|array',
            'jenis_dokumen.*' => Rule::in(['ktp','ijazah','sertifikat','cv','portfolio','foto','lainnya']),
            'dokumen' => 'nullable|array',
            'dokumen.*' => 'file|mimes:pdf,jpg,jpeg,png,doc,docx|max:2048',
        ];

        /**
         * STEP 3 – APL 02
         * hanya divalidasi jika user sudah sampai step 3
         */
        if ($step >= 3) {
            $rules['self_assessment'] = 'required|array|min:1';
            $rules['self_assessment.*'] = ['required', Rule::in(['K', 'BK'])];

            $rules['portfolio'] = 'nullable|array';
            $rules['portfolio.*.*'] = 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:2048';
            $rules['portfolio_deskripsi'] = 'nullable|array';
        }

        /**
         * STEP 4 – FINAL SUBMIT
         */
        if ($step >= 4) {
            $rules['agree'] = 'accepted';
            $rules['ttd_digital'] = 'required|string';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            // STEP 1
            'program_pelatihan_id.required' => 'Program pelatihan wajib dipilih.',
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'nik.required' => 'NIK wajib diisi.',
            'nik.digits' => 'NIK harus terdiri dari 16 digit.',
            'tanggal_lahir.before' => 'Tanggal lahir harus sebelum hari ini.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'alamat_rumah.required' => 'Alamat rumah wajib diisi.',
            'hp.required' => 'Nomor HP wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'pekerjaan.required' => 'Pekerjaan wajib dipilih.',

            // STEP 2
            'tujuan_asesmen.array' => 'Tujuan asesmen harus berupa pilihan yang valid.',
            'dokumen.*.mimes' => 'Format dokumen harus PDF / JPG / PNG / DOC.',
            'dokumen.*.max' => 'Ukuran dokumen maksimal 2MB.',

            // STEP 3
            'self_assessment.required' => 'Self assessment wajib diisi.',
            'self_assessment.array' => 'Self assessment tidak valid.',
            'portfolio.*.*.max' => 'Ukuran file portfolio maksimal 2MB.',

            // STEP 4
            'agree.accepted' => 'Anda harus menyetujui pernyataan.',
            'ttd_digital.required' => 'Tanda tangan digital wajib diisi.',
        ];
    }
}
