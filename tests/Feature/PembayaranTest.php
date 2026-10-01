<?php

namespace Tests\Feature;

use App\Models\Pembayaran;
use App\Models\PengajuanSkema;
use App\Models\ProgramPelatihan;
use App\Models\User;
use App\Services\MidtransService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class PembayaranTest extends TestCase
{
    use RefreshDatabase;

    private function createApprovedPengajuan(?User $user = null): array
    {
        $user ??= User::factory()->create(['role' => 'user']);

        $program = ProgramPelatihan::create([
            'kode_skema' => 'TEST-'.uniqid(),
            'nama' => 'Test Program',
            'slug' => 'test-program-'.uniqid(),
            'kategori' => 'Test',
            'kategori_slug' => 'test',
            'estimasi_biaya' => 500000,
            'is_published' => true,
        ]);

        $pengajuan = PengajuanSkema::create([
            'user_id' => $user->id,
            'program_pelatihan_id' => $program->id,
            'status' => 'approved',
            'tanggal_pengajuan' => now(),
        ]);

        return compact('user', 'program', 'pengajuan');
    }

    public function test_payment_created_when_pengajuan_approved(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $context = $this->createApprovedPengajuan();
        $context['pengajuan']->update(['status' => 'pending']);

        $response = $this->actingAs($admin)
            ->post(route('admin.pengajuan.approve', $context['pengajuan']->id));

        $response->assertRedirect(route('admin.pengajuan.show', $context['pengajuan']->id));

        $this->assertDatabaseHas('pembayaran', [
            'pengajuan_skema_id' => $context['pengajuan']->id,
            'user_id' => $context['user']->id,
            'nominal' => '500000.00',
            'status' => 'pending',
        ]);

        $this->assertNotEmpty($context['pengajuan']->fresh()->pembayaran->order_id);
    }

    public function test_user_can_view_own_payment_page(): void
    {
        $context = $this->createApprovedPengajuan();

        $pembayaran = Pembayaran::create([
            'pengajuan_skema_id' => $context['pengajuan']->id,
            'user_id' => $context['user']->id,
            'order_id' => Pembayaran::generateOrderId(),
            'nominal' => 500000,
            'status' => 'pending',
        ]);

        $this->actingAs($context['user'])
            ->get(route('pembayaran.show', $context['pengajuan']->id))
            ->assertOk()
            ->assertViewHas('pengajuan')
            ->assertViewHas('pembayaran', fn ($value) => $value->is($pembayaran));
    }

    public function test_user_cannot_view_another_users_payment(): void
    {
        $context = $this->createApprovedPengajuan();
        $otherUser = User::factory()->create(['role' => 'user']);

        $this->actingAs($otherUser)
            ->get(route('pembayaran.show', $context['pengajuan']->id))
            ->assertNotFound();
    }

    public function test_processing_payment_creates_order_and_returns_snap_token(): void
    {
        $context = $this->createApprovedPengajuan();

        $service = Mockery::mock(MidtransService::class);
        $service->shouldReceive('createSnapToken')
            ->once()
            ->withArgs(function (Pembayaran $pembayaran, User $user) use ($context) {
                return $pembayaran->pengajuan_skema_id === $context['pengajuan']->id
                    && $pembayaran->order_id !== ''
                    && $user->is($context['user']);
            })
            ->andReturn('test-snap-token');
        $this->app->instance(MidtransService::class, $service);

        $this->actingAs($context['user'])
            ->postJson(route('pembayaran.process', $context['pengajuan']->id))
            ->assertOk()
            ->assertJson([
                'success' => true,
                'snap_token' => 'test-snap-token',
            ]);

        $this->assertDatabaseHas('pembayaran', [
            'pengajuan_skema_id' => $context['pengajuan']->id,
            'user_id' => $context['user']->id,
            'status' => 'pending',
        ]);
    }
}
