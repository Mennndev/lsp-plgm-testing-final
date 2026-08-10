<?php

namespace Tests\Feature\Admin;

use App\Models\PengajuanSkema;
use App\Models\ProgramPelatihan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_has_pengajuan_terbaru_variable(): void
    {
        $admin = User::factory()->create([
            'nama' => 'Admin User',
            'email' => 'admin@test.com',
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertViewHas('pengajuanTerbaru');
        $response->assertViewMissing('pendaftaranBaru');
    }

    public function test_dashboard_displays_pengajuan_skema_data(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['nama' => 'Test User']);

        $program = ProgramPelatihan::create([
            'kode_skema' => 'TEST-001',
            'nama' => 'Test Program',
            'slug' => 'test-program',
            'kategori' => 'Test',
            'kategori_slug' => 'test',
            'is_published' => true,
        ]);

        $pengajuan = PengajuanSkema::create([
            'user_id' => $user->id,
            'program_pelatihan_id' => $program->id,
            'status' => 'pending',
            'tanggal_pengajuan' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();

        $firstPengajuan = $response->viewData('pengajuanTerbaru')->first();
        $this->assertSame($pengajuan->id, $firstPengajuan->id);
        $this->assertNotNull($firstPengajuan->user);
        $this->assertNotNull($firstPengajuan->program);
        $this->assertSame('Test User', $firstPengajuan->user->nama);
        $this->assertSame('Test Program', $firstPengajuan->program->nama);
    }
}
