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

            'tujuan_asesmen' => 'nullable|array',
            'tujuan_asesmen.*' => Rule::in(['PKT', 'RPL', 'RCC', 'Lainnya']),
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
            'bukti_kompetensi' => 'nullable|array',
            'bukti_kompetensi.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:2048',

            'self_assessment' => 'required|array|min:1',
            'self_assessment.*' => ['required', Rule::in(['K', 'BK'])],
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
            if ($validator->errors()->has('program_pelatihan_id') || $validator->errors()->has('self_assessment')) {
                return;
            }

            $programId = (int) $this->input('program_pelatihan_id');
            $submittedKukIds = collect(array_keys($this->input('self_assessment', [])))
                ->map(fn ($id) => (int) $id)
                ->sort()
                ->values();

            $expectedKukIds = DB::table('kriteria_unjuk_kerja as kuk')
                ->join('elemen_kompetensis as elemen', 'elemen.id', '=', 'kuk.elemen_kompetensi_id')
                ->join('unit_kompetensis as unit', 'unit.id', '=', 'elemen.unit_kompetensi_id')
                ->where('unit.program_pelatihan_id', $programId)
                ->pluck('kuk.id')
                ->map(fn ($id) => (int) $id)
                ->sort()
                ->values();

            if ($expectedKukIds->isEmpty()) {
                $validator->errors()->add('self_assessment', 'Skema belum memiliki KUK yang dapat dinilai.');
                return;
            }

            if ($submittedKukIds->all() !== $expectedKukIds->all()) {
                $validator->errors()->add(
                    'self_assessment',
                    'Seluruh KUK pada skema wajib diisi dan tidak boleh berasal dari skema lain.'
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
            'dokumen.*.mimes' => 'Format dokumen harus PDF, JPG, PNG, DOC, atau DOCX.',
            'dokumen.*.max' => 'Ukuran dokumen maksimal 2MB.',
            'self_assessment.required' => 'Self assessment wajib diisi.',
            'self_assessment.array' => 'Self assessment tidak valid.',
            'portfolio.*.*.max' => 'Ukuran file portfolio maksimal 2MB.',
            'agree.accepted' => 'Anda harus menyetujui pernyataan.',
            'ttd_digital.required' => 'Tanda tangan digital wajib diisi.',
            'ttd_digital.regex' => 'Format tanda tangan digital tidak valid.',
        ];
    }
}
