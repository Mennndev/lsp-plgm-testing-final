<?php

namespace Tests\Feature\Admin;

use App\Models\JadwalAsesmen;
use App\Models\PengajuanSkema;
use App\Models\ProgramPelatihan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class JadwalAsesmenSertifikatTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_jadwal_asesmen(): void
    {
        [$admin, $asesor, $pengajuan] = $this->makeApprovedPengajuan();

        $tanggalMulai = now()->addDay();

        $response = $this->actingAs($admin)->post(route('admin.jadwal-asesmen.upsert'), [
            'pengajuan_skema_id' => $pengajuan->id,
            'asesor_id' => $asesor->id,
            'tanggal_mulai' => $tanggalMulai->format('Y-m-d H:i:s'),
            'tanggal_selesai' => $tanggalMulai->copy()->addHours(2)->format('Y-m-d H:i:s'),
            'mode_asesmen' => 'online',
            'tautan_meeting' => 'https://meet.example.com/asesmen',
            'status' => 'scheduled',
            'catatan' => 'Jadwal test',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('jadwal_asesmen', [
            'pengajuan_skema_id' => $pengajuan->id,
            'asesor_id' => $asesor->id,
            'mode_asesmen' => 'online',
            'status' => 'scheduled',
        ]);
    }

    public function test_admin_can_mark_existing_past_jadwal_as_completed(): void
    {
        [$admin, $asesor, $pengajuan] = $this->makeApprovedPengajuan();

        $jadwal = JadwalAsesmen::create([
            'pengajuan_skema_id' => $pengajuan->id,
            'asesor_id' => $asesor->id,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
            'tanggal_mulai' => now()->subDay(),
            'tanggal_selesai' => now()->subDay()->addHours(2),
            'mode_asesmen' => 'offline',
            'lokasi' => 'Ruang Asesmen',
            'status' => 'scheduled',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.jadwal-asesmen.upsert'), [
            'pengajuan_skema_id' => $pengajuan->id,
            'asesor_id' => $asesor->id,
            'tanggal_mulai' => $jadwal->tanggal_mulai->format('Y-m-d H:i:s'),
            'tanggal_selesai' => $jadwal->tanggal_selesai->format('Y-m-d H:i:s'),
            'mode_asesmen' => 'offline',
            'lokasi' => 'Ruang Asesmen',
            'status' => 'completed',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('jadwal_asesmen', [
            'pengajuan_skema_id' => $pengajuan->id,
            'status' => 'completed',
        ]);
    }

    public function test_admin_can_upload_certificate_after_assessment_completed(): void
    {
        Storage::fake('public');

        [$admin, $asesor, $pengajuan] = $this->makeApprovedPengajuan();

        JadwalAsesmen::create([
            'pengajuan_skema_id' => $pengajuan->id,
            'asesor_id' => $asesor->id,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
            'tanggal_mulai' => now()->subDay(),
            'tanggal_selesai' => now()->subDay()->addHours(2),
            'mode_asesmen' => 'offline',
            'lokasi' => 'Ruang Asesmen',
            'status' => 'completed',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.sertifikat.store', $pengajuan->id), [
            'nomor_sertifikat' => 'CERT-001',
            'jenis_bukti' => 'Sertifikat Kompetensi',
            'tanggal_terbit' => now()->format('Y-m-d'),
            'tanggal_berlaku_sampai' => now()->addYears(3)->format('Y-m-d'),
            'file_sertifikat' => UploadedFile::fake()->create('sertifikat.pdf', 100, 'application/pdf'),
        ]);

        $response->assertRedirect(route('admin.sertifikat.index'));
        $response->assertSessionHasNoErrors();

        $sertifikat = $pengajuan->fresh('sertifikat')->sertifikat;

        $this->assertNotNull($sertifikat);
        $this->assertEquals('CERT-001', $sertifikat->nomor_sertifikat);
        Storage::disk('public')->assertExists($sertifikat->file_sertifikat);
    }

    public function test_admin_report_page_loads(): void
    {
        [$admin] = $this->makeApprovedPengajuan();

        $response = $this->actingAs($admin)->get(route('admin.laporan.index'));

        $response->assertOk();
        $response->assertViewHas('statistik');
        $response->assertViewHas('pengajuanList');
    }

    private function makeApprovedPengajuan(): array
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $asesor = User::factory()->create([
            'role' => 'asesor',
        ]);

        $asesi = User::factory()->create([
            'role' => 'user',
        ]);

        $program = ProgramPelatihan::create([
            'kode_skema' => 'SKM-TEST',
            'nama' => 'Skema Test',
            'slug' => 'skema-test',
            'kategori' => 'Testing',
            'kategori_slug' => 'testing',
            'jumlah_unit' => 1,
            'estimasi_biaya' => 500000,
            'is_published' => true,
        ]);

        $pengajuan = PengajuanSkema::create([
            'user_id' => $asesi->id,
            'program_pelatihan_id' => $program->id,
            'status' => 'approved',
            'tanggal_pengajuan' => now()->subDays(2),
            'tanggal_disetujui' => now()->subDay(),
            'approved_by' => $admin->id,
        ]);

        return [$admin, $asesor, $pengajuan];
    }
}
