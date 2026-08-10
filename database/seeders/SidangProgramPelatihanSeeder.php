<?php

namespace Database\Seeders;

use App\Models\BuktiAdministratif;
use App\Models\BuktiPortofolioTemplate;
use App\Models\PersyaratanDasar;
use App\Models\ProgramPelatihan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SidangProgramPelatihanSeeder extends Seeder
{
    /**
     * Seed skema, unit kompetensi, dan persyaratan untuk kebutuhan demo/sidang.
     *
     * Elemen kompetensi dan KUK tidak dibuat karena tidak tersedia pada sumber
     * daftar skema yang diberikan pengguna.
     */
    public function run(): void
    {
        $programs = [
            [
                'kode_skema' => 'SK-OKM-2018',
                'nama' => 'Skema Sertifikasi Okupasi Pada Kompetensi Operator Komputer Muda',
                'kategori' => 'Operator Komputer',
                'rujukan_skkni' => 'Kepmenaker RI Nomor 56 Tahun 2018',
                'jumlah_unit' => 8,
                'units' => [
                    ['J.630PR00.001.2', 'Menggunakan Perangkat Komputer'],
                    ['J.630PR00.002.2', 'Menggunakan Sistem Operasi'],
                    ['J.630PR00.003.2', 'Menggunakan Peralatan Peripheral'],
                    ['J.630PR00.004.2', 'Menggunakan Perangkat Lunak Pengolah Kata Tingkat Dasar'],
                    ['J.630PR00.005.2', 'Menggunakan Perangkat Lunak Lembar Sebar (Spreadsheet) Tingkat Dasar'],
                    ['J.630PR00.006.2', 'Menggunakan Perangkat Lunak Presentasi Tingkat Dasar'],
                    ['J.630PR00.008.2', 'Menggunakan Perangkat Lunak Pengakses Surat Elektronik (Email Client)'],
                    ['J.630PR00.009.2', 'Menggunakan Aplikasi Berbasis Internet'],
                ],
                'persyaratan_dasar' => [
                    ['Ijazah minimal SMA/SMK atau Surat Keterangan Mahasiswa', true],
                    ['Sertifikat Pelatihan atau Surat Pengalaman Kerja terkait (jika ada)', false],
                ],
                'bukti_administratif' => [
                    ['KTP / Kartu Identitas', true],
                    ['Pas Foto', true],
                    ['Curriculum Vitae (CV)', true],
                ],
                'bukti_portofolio' => [
                    ['Contoh Dokumen Pengolah Kata', true],
                    ['Contoh Dokumen Spreadsheet', true],
                    ['Contoh File Presentasi', false],
                ],
            ],
            [
                'kode_skema' => 'SK-PPD-2016',
                'nama' => 'Skema Sertifikasi Okupasi Pada Kompetensi Pengelolaan Perkantoran Digital',
                'kategori' => 'Perkantoran Digital',
                'rujukan_skkni' => 'Kepmenaker RI Nomor 183 Tahun 2016',
                'jumlah_unit' => 12,
                'units' => [
                    ['N.821100.053.02', 'Memproduksi Dokumen di Komputer'],
                    ['N.821100.054.01', 'Menggunakan Peralatan Komunikasi'],
                    ['N.821100.055.01', 'Mengatur Teleconference'],
                    ['N.821100.056.02', 'Memelihara Data di Komputer'],
                    ['N.821100.057.02', 'Mengoperasikan Aplikasi Perangkat Lunak'],
                    ['N.821100.058.02', 'Mengakses Data di Komputer'],
                    ['N.821100.059.02', 'Menggunakan Peralatan dan Sumberdaya Kerja'],
                    ['N.821100.060.02', 'Membuat Surat/Dokumen Elektronik'],
                    ['N.821100.061.02', 'Mengakses Informasi melalui Homepage'],
                    ['N.821100.062.02', 'Mengembangkan Data Informasi di Komputer (Database)'],
                    ['N.821100.063.02', 'Memutakhirkan Informasi pada Homepage Perusahaan'],
                    ['N.821100.064.02', 'Mengoperasikan Sistem Informasi'],
                ],
                'persyaratan_dasar' => [
                    ['Ijazah minimal SMA/SMK atau Surat Keterangan Mahasiswa', true],
                    ['Sertifikat Pelatihan atau Surat Pengalaman Kerja terkait (jika ada)', false],
                ],
                'bukti_administratif' => [
                    ['KTP / Kartu Identitas', true],
                    ['Pas Foto', true],
                    ['Curriculum Vitae (CV)', true],
                ],
                'bukti_portofolio' => [
                    ['Contoh Surat atau Dokumen Elektronik', true],
                    ['Contoh Dokumen Administrasi Perkantoran', true],
                    ['Contoh Pengolahan Data / Database Sederhana', false],
                ],
            ],
            [
                'kode_skema' => 'SK-DM-2013',
                'nama' => 'Skema Sertifikasi Okupasi Pada Kompetensi Digital Marketing',
                'kategori' => 'Digital Marketing',
                'rujukan_skkni' => 'Kepmenakertrans RI Nomor 389 Tahun 2013',
                'jumlah_unit' => 10,
                'units' => [
                    ['M.702090.001.01', 'Mengidentifikasi Elemen Pemasaran Perusahaan'],
                    ['J.630PR00.001.2', 'Menggunakan Perangkat Komputer'],
                    ['J.630PR00.010.2', 'Menggunakan Aplikasi Media Sosial'],
                    ['J.630PR00.007.2', 'Menggunakan Penelusuran Situs Web'],
                    ['M.702090.004.01', 'Melakukan Pendekatan Kepada Calon Pelanggan'],
                    ['M.731000.001.01', 'Membuat Perencanaan Periklanan'],
                    ['M.731000.003.01', 'Merancang Strategi Kreatif dan Pembuatan Iklan'],
                    ['M.731000.004.01', 'Merancang Strategi dan Pembelian Media'],
                    ['M.702090.006.01', 'Menyusun Rencana Aktifitas Penjualan'],
                    ['M.702090.005.01', 'Melaksanakan Keterampilan Penjualan'],
                ],
                'persyaratan_dasar' => [
                    ['Ijazah minimal SMA/SMK atau Surat Keterangan Mahasiswa', true],
                    ['Sertifikat Pelatihan atau Surat Pengalaman Kerja terkait (jika ada)', false],
                ],
                'bukti_administratif' => [
                    ['KTP / Kartu Identitas', true],
                    ['Pas Foto', true],
                    ['Curriculum Vitae (CV)', true],
                ],
                'bukti_portofolio' => [
                    ['Contoh Konten Media Sosial', true],
                    ['Contoh Desain / Materi Promosi', true],
                    ['Contoh Rencana atau Strategi Pemasaran', false],
                ],
            ],
        ];

        foreach ($programs as $programData) {
            $units = $programData['units'];
            $persyaratanDasar = $programData['persyaratan_dasar'];
            $buktiAdministratif = $programData['bukti_administratif'];
            $buktiPortofolio = $programData['bukti_portofolio'];

            unset(
                $programData['units'],
                $programData['persyaratan_dasar'],
                $programData['bukti_administratif'],
                $programData['bukti_portofolio']
            );

            $program = ProgramPelatihan::updateOrCreate(
                ['kode_skema' => $programData['kode_skema']],
                [
                    ...$programData,
                    'slug' => Str::slug($programData['nama']),
                    'kategori_slug' => Str::slug($programData['kategori']),
                    'is_published' => true,
                ]
            );

            foreach ($units as $index => [$kodeUnit, $judulUnit]) {
                $program->units()->updateOrCreate(
                    ['no_urut' => $index + 1],
                    [
                        'kode_unit' => $kodeUnit,
                        'judul_unit' => $judulUnit,
                    ]
                );
            }

            foreach ($persyaratanDasar as $index => [$namaDokumen, $isWajib]) {
                PersyaratanDasar::updateOrCreate(
                    [
                        'program_pelatihan_id' => $program->id,
                        'urutan' => $index + 1,
                    ],
                    [
                        'nama_dokumen' => $namaDokumen,
                        'tipe_dokumen' => 'file_upload',
                        'is_wajib' => $isWajib,
                    ]
                );
            }

            foreach ($buktiAdministratif as $index => [$namaDokumen, $isWajib]) {
                BuktiAdministratif::updateOrCreate(
                    [
                        'program_pelatihan_id' => $program->id,
                        'urutan' => $index + 1,
                    ],
                    [
                        'nama_dokumen' => $namaDokumen,
                        'tipe_dokumen' => 'file_upload',
                        'is_wajib' => $isWajib,
                    ]
                );
            }

            foreach ($buktiPortofolio as $index => [$namaDokumen, $isWajib]) {
                BuktiPortofolioTemplate::updateOrCreate(
                    [
                        'program_pelatihan_id' => $program->id,
                        'urutan' => $index + 1,
                    ],
                    [
                        'nama_dokumen' => $namaDokumen,
                        'is_wajib' => $isWajib,
                    ]
                );
            }
        }
    }
}
