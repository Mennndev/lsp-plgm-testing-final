<?php

namespace Tests\Feature\Asesor;

use App\Models\FormulirAsesmen;
use App\Models\PengajuanAsesor;
use App\Models\PengajuanSkema;
use App\Models\ProgramPelatihan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormulirDemoFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_assessor_can_open_save_complete_and_print_assessment_form(): void
    {
        $asesor = User::factory()->create(['role' => 'asesor']);
        $asesi = User::factory()->create(['role' => 'user']);

        $program = ProgramPelatihan::create([
            'kode_skema' => 'SKM-FORM-DEMO',
            'nama' => 'Skema Formulir Demo',
            'slug' => 'skema-formulir-demo',
            'kategori' => 'Testing',
            'kategori_slug' => 'testing',
            'jumlah_unit' => 1,
            'estimasi_biaya' => 500000,
            'is_published' => true,
        ]);

        $pengajuan = PengajuanSkema::create([
            'user_id' => $asesi->id,
            'program_pelatihan_id' => $program->id,
            'status' => 'paid',
            'tanggal_pengajuan' => now(),
        ]);

        PengajuanAsesor::create([
            'pengajuan_skema_id' => $pengajuan->id,
            'asesor_id' => $asesor->id,
            'role' => 'utama',
        ]);

        $this->actingAs($asesor)
            ->get(route('asesor.pengajuan.show', $pengajuan->id))
            ->assertOk();

        $this->actingAs($asesor)
            ->get(route('asesor.formulir.index', $pengajuan->id))
            ->assertOk()
            ->assertSee('FR.IA.01');

        $this->actingAs($asesor)
            ->get(route('asesor.formulir.show', [$pengajuan->id, 'FR_IA_01']))
            ->assertOk()
            ->assertSee('Lokasi Observasi');

        $this->actingAs($asesor)
            ->post(route('asesor.formulir.store', [$pengajuan->id, 'FR_IA_01']), [
                'data' => [
                    'lokasi' => 'TUK Demo',
                    'tanggal' => now()->format('Y-m-d'),
                    'aktivitas' => 'Demonstrasi aktivitas kerja.',
                    'hasil' => 'Aktivitas sesuai kriteria.',
                    'catatan' => 'Siap untuk demo sidang.',
                ],
                'status' => 'draft',
            ])
            ->assertRedirect(route('asesor.formulir.index', $pengajuan->id));

        $form = FormulirAsesmen::where('pengajuan_skema_id', $pengajuan->id)
            ->where('asesor_id', $asesor->id)
            ->where('jenis_formulir', 'FR_IA_01')
            ->firstOrFail();

        $this->assertSame('draft', $form->status);
        $this->assertSame('TUK Demo', $form->data['lokasi']);

        $this->actingAs($asesor)
            ->post(route('asesor.formulir.store', [$pengajuan->id, 'FR_IA_01']), [
                'data' => [
                    'lokasi' => 'TUK Demo',
                    'tanggal' => now()->format('Y-m-d'),
                    'aktivitas' => 'Demonstrasi aktivitas kerja.',
                    'hasil' => 'Aktivitas sesuai kriteria.',
                    'catatan' => 'Formulir selesai.',
                ],
                'status' => 'selesai',
            ])
            ->assertRedirect(route('asesor.formulir.index', $pengajuan->id));

        $this->assertDatabaseHas('formulir_asesmen', [
            'id' => $form->id,
            'status' => 'selesai',
        ]);

        $response = $this->actingAs($asesor)
            ->get(route('asesor.formulir.cetak', [$pengajuan->id, 'FR_IA_01']));

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', (string) $response->headers->get('content-type'));
    }

    public function test_unassigned_assessor_cannot_open_another_assessors_forms(): void
    {
        $assignedAsesor = User::factory()->create(['role' => 'asesor']);
        $otherAsesor = User::factory()->create(['role' => 'asesor']);
        $asesi = User::factory()->create(['role' => 'user']);

        $program = ProgramPelatihan::create([
            'kode_skema' => 'SKM-FORM-SCOPE',
            'nama' => 'Skema Form Scope',
            'slug' => 'skema-form-scope',
            'kategori' => 'Testing',
            'kategori_slug' => 'testing',
            'jumlah_unit' => 1,
            'estimasi_biaya' => 500000,
            'is_published' => true,
        ]);

        $pengajuan = PengajuanSkema::create([
            'user_id' => $asesi->id,
            'program_pelatihan_id' => $program->id,
            'status' => 'paid',
            'tanggal_pengajuan' => now(),
        ]);

        PengajuanAsesor::create([
            'pengajuan_skema_id' => $pengajuan->id,
            'asesor_id' => $assignedAsesor->id,
            'role' => 'utama',
        ]);

        $this->actingAs($otherAsesor)
            ->get(route('asesor.formulir.index', $pengajuan->id))
            ->assertNotFound();
    }
}
