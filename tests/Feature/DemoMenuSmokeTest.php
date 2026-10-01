<?php

namespace Tests\Feature;

use App\Models\Pendaftaran;
use App\Models\PengajuanSkema;
use App\Models\ProgramPelatihan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoMenuSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_demo_pages_render(): void
    {
        $this->get(route('home'))->assertOk();
        $this->get(route('pendaftaran.create'))->assertOk();
        $this->get(route('login'))->assertOk();
        $this->get(route('tentang-kami'))->assertOk();
        $this->get(route('visi-misi'))->assertOk();
        $this->get(route('kebijakan-mutu'))->assertOk();
        $this->get(route('struktur-organisasi'))->assertOk();
        $this->get(route('skema.index'))->assertOk();
        $this->get('/tempat-sertifikasi')->assertOk();
        $this->get(route('berita.index'))->assertOk();
    }

    public function test_midtrans_debug_endpoint_is_not_publicly_available(): void
    {
        $this->get('/cek-midtrans')->assertNotFound();
    }

    public function test_user_demo_menus_render(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)->get(route('dashboard.user'))->assertOk();
        $this->actingAs($user)->get(route('ProfileUser.edit'))->assertOk();
        $this->actingAs($user)->get(route('pengajuan.pilih-skema'))->assertOk();
        $this->actingAs($user)->get(route('notifications.index'))->assertOk();
        $this->actingAs($user)->get(route('chat.index'))->assertOk();
        $this->actingAs($user)->get(route('notifications.latest'))->assertOk()->assertJsonStructure([
            'notifications', 'unread_count',
        ]);
    }

    public function test_admin_sidebar_pages_render_with_empty_data(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $routes = [
            'admin.dashboard',
            'admin.program-pelatihan.index',
            'admin.asesi.index',
            'admin.pengajuan.index',
            'admin.jadwal-asesmen.index',
            'admin.sertifikat.index',
            'admin.pembayaran.index',
            'admin.laporan.index',
            'admin.chat.index',
            'admin.berita.index',
            'admin.asesor.index',
        ];

        foreach ($routes as $routeName) {
            $this->actingAs($admin)->get(route($routeName))->assertOk();
        }
    }

    public function test_admin_can_open_asesi_detail_and_search_by_nama(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $asesiUser = User::factory()->create([
            'role' => 'user',
            'nama' => 'Asesi Demo Sidang',
            'email' => 'asesi.demo@example.com',
        ]);

        $pendaftaran = Pendaftaran::create([
            'user_id' => $asesiUser->id,
            'email' => $asesiUser->email,
            'jenis_kelamin' => 'Laki-laki',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '2000-01-01',
            'no_ktp' => '3273010101000001',
            'setuju' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.asesi.index', ['search' => 'Asesi Demo Sidang']))
            ->assertOk()
            ->assertSee('Asesi Demo Sidang');

        $this->actingAs($admin)
            ->get(route('admin.asesi.show', $pendaftaran->id))
            ->assertOk()
            ->assertSee('Asesi Demo Sidang')
            ->assertSee('asesi.demo@example.com');
    }

    public function test_admin_can_schedule_assessment_after_payment(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $asesor = User::factory()->create(['role' => 'asesor']);
        $asesi = User::factory()->create(['role' => 'user']);

        $program = ProgramPelatihan::create([
            'kode_skema' => 'SKM-PAID-DEMO',
            'nama' => 'Skema Paid Demo',
            'slug' => 'skema-paid-demo',
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
            'tanggal_pengajuan' => now()->subDays(2),
            'tanggal_disetujui' => now()->subDay(),
            'approved_by' => $admin->id,
        ]);

        $mulai = now()->addDay();

        $response = $this->actingAs($admin)->post(route('admin.jadwal-asesmen.upsert'), [
            'pengajuan_skema_id' => $pengajuan->id,
            'asesor_id' => $asesor->id,
            'tanggal_mulai' => $mulai->format('Y-m-d H:i:s'),
            'tanggal_selesai' => $mulai->copy()->addHours(2)->format('Y-m-d H:i:s'),
            'mode_asesmen' => 'offline',
            'lokasi' => 'Ruang Sidang Demo',
            'status' => 'scheduled',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('jadwal_asesmen', [
            'pengajuan_skema_id' => $pengajuan->id,
            'asesor_id' => $asesor->id,
            'status' => 'scheduled',
        ]);
    }

    public function test_removed_unimplemented_admin_routes_return_not_found(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/admin/asesi/create')->assertNotFound();
    }
}
