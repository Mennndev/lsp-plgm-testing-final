<?php

namespace Tests\Feature;

use App\Models\ElemenKompetensi;
use App\Models\KriteriaUnjukKerja;
use App\Models\Pembayaran;
use App\Models\PengajuanSkema;
use App\Models\ProgramPelatihan;
use App\Models\UnitKompetensi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Tests\TestCase;

class SidangReadinessTest extends TestCase
{
    use RefreshDatabase;

    private function createProgram(string $suffix = 'A'): ProgramPelatihan
    {
        return ProgramPelatihan::create([
            'kode_skema' => 'SIDANG-'.$suffix.'-'.uniqid(),
            'nama' => 'Skema Demo '.$suffix,
            'slug' => 'skema-demo-'.strtolower($suffix).'-'.uniqid(),
            'kategori' => 'Teknologi Informasi',
            'kategori_slug' => 'teknologi-informasi',
            'estimasi_biaya' => 500000,
            'is_published' => true,
        ]);
    }

    private function createPengajuan(User $user, ProgramPelatihan $program, string $status = 'pending'): PengajuanSkema
    {
        return PengajuanSkema::create([
            'user_id' => $user->id,
            'program_pelatihan_id' => $program->id,
            'status' => $status,
            'tanggal_pengajuan' => now(),
        ]);
    }

    private function createKuk(ProgramPelatihan $program, string $suffix): KriteriaUnjukKerja
    {
        $unit = UnitKompetensi::create([
            'program_pelatihan_id' => $program->id,
            'no_urut' => 1,
            'kode_unit' => 'UNIT-'.$suffix.'-'.uniqid(),
            'judul_unit' => 'Unit '.$suffix,
        ]);

        $elemen = ElemenKompetensi::create([
            'unit_kompetensi_id' => $unit->id,
            'no_urut' => 1,
            'nama_elemen' => 'Elemen '.$suffix,
        ]);

        return KriteriaUnjukKerja::create([
            'elemen_kompetensi_id' => $elemen->id,
            'no_urut' => 1,
            'deskripsi' => 'KUK '.$suffix,
        ]);
    }

    private function validPengajuanPayload(ProgramPelatihan $program, array $selfAssessment): array
    {
        return [
            'program_pelatihan_id' => $program->id,
            'nama_lengkap' => 'Peserta Demo Sidang',
            'nik' => '3273010101010001',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '2002-01-01',
            'jenis_kelamin' => 'L',
            'kebangsaan' => 'Indonesia',
            'alamat_rumah' => 'Bandung',
            'telepon_kantor' => '0221234567',
            'hp' => '081234567890',
            'email' => 'peserta.demo@example.com',
            'pekerjaan' => 'Mahasiswa',
            'self_assessment' => $selfAssessment,
            'agree' => '1',
            'ttd_digital' => 'data:image/png;base64,'.base64_encode('demo-signature'),
        ];
    }

    public function test_approving_same_application_twice_does_not_create_duplicate_payment(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);
        $program = $this->createProgram();
        $pengajuan = $this->createPengajuan($user, $program);

        $this->actingAs($admin)->post(route('admin.pengajuan.approve', $pengajuan->id))->assertRedirect();
        $this->actingAs($admin)->post(route('admin.pengajuan.approve', $pengajuan->id))->assertRedirect();

        $this->assertSame(1, Pembayaran::where('pengajuan_skema_id', $pengajuan->id)->count());
        $this->assertSame('approved', $pengajuan->fresh()->status);
    }

    public function test_payment_finish_callback_cannot_be_opened_by_another_user(): void
    {
        $owner = User::factory()->create(['role' => 'user']);
        $other = User::factory()->create(['role' => 'user']);
        $program = $this->createProgram();
        $pengajuan = $this->createPengajuan($owner, $program, 'approved');

        $payment = Pembayaran::create([
            'pengajuan_skema_id' => $pengajuan->id,
            'user_id' => $owner->id,
            'order_id' => Pembayaran::generateOrderId(),
            'nominal' => 500000,
            'status' => 'pending',
        ]);

        $this->actingAs($other)
            ->get(route('pembayaran.finish', $payment->id))
            ->assertNotFound();
    }

    public function test_admin_cannot_assign_regular_user_as_assessor(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $regularUser = User::factory()->create(['role' => 'user']);
        $candidate = User::factory()->create(['role' => 'user']);
        $program = $this->createProgram();
        $pengajuan = $this->createPengajuan($regularUser, $program, 'paid');

        $this->actingAs($admin)
            ->post(route('admin.pengajuan.assign-asesor', $pengajuan->id), [
                'asesor_id' => $candidate->id,
            ])
            ->assertSessionHasErrors('asesor_id');

        $this->assertDatabaseMissing('pengajuan_asesor', [
            'pengajuan_skema_id' => $pengajuan->id,
            'asesor_id' => $candidate->id,
        ]);
    }

    public function test_assessor_cannot_submit_score_for_kuk_from_another_scheme(): void
    {
        $asesor = User::factory()->create(['role' => 'asesor']);
        $user = User::factory()->create(['role' => 'user']);
        $programA = $this->createProgram('A');
        $programB = $this->createProgram('B');
        $validKuk = $this->createKuk($programA, 'A');
        $foreignKuk = $this->createKuk($programB, 'B');
        $pengajuan = $this->createPengajuan($user, $programA, 'paid');

        DB::table('pengajuan_asesor')->insert([
            'pengajuan_skema_id' => $pengajuan->id,
            'asesor_id' => $asesor->id,
            'role' => 'utama',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($asesor)
            ->post(route('asesor.pengajuan.store', $pengajuan->id), [
                'nilai' => [$foreignKuk->id => 'K'],
            ])
            ->assertStatus(422);

        $this->assertDatabaseMissing('pengajuan_asesor_assessments', [
            'pengajuan_skema_id' => $pengajuan->id,
            'kriteria_unjuk_kerja_id' => $foreignKuk->id,
        ]);

        $this->actingAs($asesor)
            ->post(route('asesor.pengajuan.store', $pengajuan->id), [
                'nilai' => [$validKuk->id => 'K'],
            ])
            ->assertRedirect(route('asesor.dashboard'));
    }

    public function test_login_is_rate_limited_after_repeated_failed_attempts(): void
    {
        $user = User::factory()->create(['email' => 'sidang@example.com']);
        $key = Str::lower($user->email).'|127.0.0.1';
        RateLimiter::clear($key);

        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->post(route('login.process'), [
                'email' => $user->email,
                'password' => 'password-salah',
            ]);
        }

        $this->post(route('login.process'), [
            'email' => $user->email,
            'password' => 'password-salah',
        ])->assertSessionHasErrors('email');

        $this->assertGreaterThan(0, RateLimiter::availableIn($key));
        RateLimiter::clear($key);
    }

    public function test_admin_midtrans_payment_pages_render_with_current_status_schema(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);
        $program = $this->createProgram();
        $pengajuan = $this->createPengajuan($user, $program, 'approved');

        $payment = Pembayaran::create([
            'pengajuan_skema_id' => $pengajuan->id,
            'user_id' => $user->id,
            'order_id' => Pembayaran::generateOrderId(),
            'nominal' => 500000,
            'status' => 'processing',
            'payment_type' => 'bank_transfer',
        ]);

        $this->actingAs($admin)->get(route('admin.pembayaran.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.pembayaran.show', $payment->id))->assertOk();
    }

    public function test_final_application_requires_all_kuk_from_selected_scheme(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $program = $this->createProgram();
        $kukA = $this->createKuk($program, 'A');
        $kukB = $this->createKuk($program, 'B');

        $this->actingAs($user)
            ->post(route('pengajuan.store'), $this->validPengajuanPayload($program, [
                $kukA->id => 'K',
            ]))
            ->assertSessionHasErrors('self_assessment');

        $this->assertDatabaseCount('pengajuan_skema', 0);

        $this->actingAs($user)
            ->post(route('pengajuan.store'), $this->validPengajuanPayload($program, [
                $kukA->id => 'K',
                $kukB->id => 'BK',
            ]))
            ->assertRedirect(route('dashboard.user'));

        $this->assertDatabaseHas('pengajuan_apl01', [
            'hp' => '081234567890',
            'telepon_kantor' => '0221234567',
        ]);
    }

    public function test_user_cannot_create_second_active_application_for_same_scheme(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $program = $this->createProgram();
        $kuk = $this->createKuk($program, 'A');
        $payload = $this->validPengajuanPayload($program, [$kuk->id => 'K']);

        $this->actingAs($user)->post(route('pengajuan.store'), $payload)->assertRedirect(route('dashboard.user'));
        $this->actingAs($user)->post(route('pengajuan.store'), $payload)->assertSessionHasErrors('program_pelatihan_id');

        $this->assertSame(1, PengajuanSkema::where('user_id', $user->id)
            ->where('program_pelatihan_id', $program->id)
            ->count());
    }
}
