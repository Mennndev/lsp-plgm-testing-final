<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StorePengajuanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'current_step' => 'nullable|integer|min:1|max:6',
            'program_pelatihan_id' => 'required|exists:program_pelatihans,id',

            'nama_lengkap' => 'required|string|max:255',
            'nik' => 'required|digits:16',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date|before:today',
            'jenis_kelamin' => ['required', Rule::in(['L', 'P'])],
            'kebangsaan' => 'nullable|string|max:100',
            'alamat_rumah' => 'required|string',
            'kode_pos' => 'nullable|string|max:10',
            'telepon_rumah' => 'nullable|string|max:20',
            'hp' => 'required|string|max:30',
            'email' => 'required|email|max:255',

            'kualifikasi_pendidikan' => 'nullable|string|max:100',
            'pekerjaan' => 'required|string|max:255',

            // Jika Asesi belum/tidak bekerja, detail kantor tidak relevan dan tidak divalidasi.
            'nama_institusi' => 'exclude_if:pekerjaan,Belum/Tidak Bekerja|nullable|string|max:255',
            'jabatan' => 'exclude_if:pekerjaan,Belum/Tidak Bekerja|nullable|string|max:255',
            'alamat_kantor' => 'exclude_if:pekerjaan,Belum/Tidak Bekerja|nullable|string',
            'telepon_kantor' => 'exclude_if:pekerjaan,Belum/Tidak Bekerja|nullable|string|max:20',
            'fax' => 'exclude_if:pekerjaan,Belum/Tidak Bekerja|nullable|string|max:20',
            'email_kantor' => 'exclude_if:pekerjaan,Belum/Tidak Bekerja|nullable|email|max:255',

            'nama_sertifikat' => 'nullable|string|max:255',
            'nomor_sertifikat' => 'nullable|string|max:255',

            // APL-01: pemohon memilih tepat satu tujuan asesmen.
            // Tetap disimpan sebagai array satu elemen agar kompatibel dengan kolom JSON/data lama.
            'tujuan_asesmen' => 'required|array|size:1',
            'tujuan_asesmen.*' => ['required', Rule::in(['Sertifikasi', 'Sertifikasi Ulang'])],
            'bukti_penyertaan_dasar' => 'nullable|string',
            'bukti_administrasif' => 'nullable|string',
            'catatan' => 'nullable|string',

            'jenis_dokumen' => 'nullable|array',
            'jenis_dokumen.*' => Rule::in(['ktp', 'ijazah', 'sertifikat', 'cv', 'portfolio', 'foto', 'lainnya']),
            'dokumen' => 'nullable|array',
            'dokumen.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:2048',

            'persyaratan_dasar' => 'nullable|array',
            'persyaratan_dasar.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:2048',
            'bukti_administratif' => 'nullable|array',
            'bukti_administratif.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:2048',
            'bukti_portofolio' => 'nullable|array',
            'bukti_portofolio.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:2048',

            // APL-02 disederhanakan menjadi penilaian mandiri per Unit Kompetensi.
            // K/BK di sini adalah penilaian mandiri Asesi, bukan hasil akhir Asesor.
            'unit_assessment' => 'required|array|min:1',
            'unit_assessment.*.kode_unit' => 'required|string|max:100',
            'unit_assessment.*.status' => ['required', Rule::in(['K', 'BK'])],
            'unit_evidence' => 'nullable|array',
            'unit_evidence.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:2048',

            // Field lama tetap diterima secara opsional agar data/halaman lama tidak langsung rusak.
            'bukti_kompetensi' => 'nullable|array',
            'bukti_kompetensi.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:2048',
            'portfolio' => 'nullable|array',
            'portfolio.*.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:2048',
            'portfolio_deskripsi' => 'nullable|array',

            'agree' => 'accepted',
            'ttd_digital' => ['required', 'string', 'regex:/^data:image\/(png|jpeg);base64,/i'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if ($validator->errors()->has('program_pelatihan_id') || $validator->errors()->has('unit_assessment')) {
                return;
            }

            $programId = (int) $this->input('program_pelatihan_id');

            $expectedUnitCodes = DB::table('unit_kompetensis')
                ->where('program_pelatihan_id', $programId)
                ->pluck('kode_unit')
                ->map(fn ($kode) => (string) $kode)
                ->sort()
                ->values();

            if ($expectedUnitCodes->isEmpty()) {
                $validator->errors()->add('unit_assessment', 'Skema belum memiliki Unit Kompetensi yang dapat dinilai.');
                return;
            }

            $submittedUnitCodes = collect($this->input('unit_assessment', []))
                ->pluck('kode_unit')
                ->filter(fn ($kode) => is_string($kode) && $kode !== '')
                ->map(fn ($kode) => (string) $kode)
                ->values();

            if ($submittedUnitCodes->duplicates()->isNotEmpty()) {
                $validator->errors()->add('unit_assessment', 'Unit Kompetensi tidak boleh dinilai lebih dari satu kali.');
                return;
            }

            $submittedUnitCodes = $submittedUnitCodes->sort()->values();

            if ($submittedUnitCodes->all() !== $expectedUnitCodes->all()) {
                $validator->errors()->add(
                    'unit_assessment',
                    'Seluruh Unit Kompetensi pada skema wajib dinilai K atau BK dan tidak boleh berasal dari skema lain.'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
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
            'email_kantor.email' => 'Email institusi/perusahaan harus berupa alamat email yang valid.',
            'tujuan_asesmen.required' => 'Tujuan asesmen wajib dipilih.',
            'tujuan_asesmen.size' => 'Pilih satu tujuan asesmen.',
            'tujuan_asesmen.*.in' => 'Tujuan asesmen tidak valid.',
            'dokumen.*.mimes' => 'Format dokumen harus PDF, JPG, PNG, DOC, atau DOCX.',
            'dokumen.*.max' => 'Ukuran dokumen maksimal 2MB.',
            'unit_assessment.required' => 'Penilaian mandiri Unit Kompetensi wajib diisi.',
            'unit_assessment.array' => 'Penilaian mandiri Unit Kompetensi tidak valid.',
            'unit_assessment.*.kode_unit.required' => 'Kode Unit Kompetensi tidak valid.',
            'unit_assessment.*.status.required' => 'Pilih K atau BK untuk setiap Unit Kompetensi.',
            'unit_assessment.*.status.in' => 'Nilai penilaian mandiri harus K atau BK.',
            'unit_evidence.*.mimes' => 'Bukti kompetensi harus berformat PDF, JPG, PNG, DOC, atau DOCX.',
            'unit_evidence.*.max' => 'Ukuran bukti kompetensi maksimal 2MB.',
            'portfolio.*.*.max' => 'Ukuran file portfolio maksimal 2MB.',
            'agree.accepted' => 'Anda harus menyetujui pernyataan.',
            'ttd_digital.required' => 'Tanda tangan digital wajib diisi.',
            'ttd_digital.regex' => 'Format tanda tangan digital tidak valid.',
        ];
    }
}
