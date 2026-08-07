<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'nama' => 'Test User',
            'email' => 'test@example.com',
            'no_hp' => '081234567890',
            'jenis_kelamin' => 'Laki-laki',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '2000-01-01',
            'nik' => '3273010101000001',
            'password' => 'password',
            'password_confirmation' => 'password',
            'ttd_digital' => 'data:image/png;base64,'.base64_encode('signature'),
            'setuju' => '1',
        ], $overrides);
    }

    public function test_registration_screen_can_be_rendered(): void
    {
        $this->get(route('pendaftaran.create'))->assertOk();
    }

    public function test_new_users_can_register(): void
    {
        Storage::fake('public');

        $response = $this->post(route('pendaftaran.store'), $this->validPayload());

        $response->assertSessionHasNoErrors()
            ->assertRedirect(route('pendaftaran.create'));

        $this->assertGuest();
        $this->assertDatabaseHas('users', [
            'nama' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'user',
        ]);
        $this->assertDatabaseHas('pendaftarans', [
            'email' => 'test@example.com',
            'no_ktp' => '3273010101000001',
            'setuju' => 1,
        ]);
    }

    public function test_invalid_base64_signature_does_not_create_partial_registration(): void
    {
        Storage::fake('public');

        $this->post(route('pendaftaran.store'), $this->validPayload([
            'email' => 'invalid-signature@example.com',
            'ttd_digital' => 'data:image/png;base64,%%%BUKAN-BASE64%%%',
        ]))->assertSessionHasErrors('ttd_digital');

        $this->assertDatabaseMissing('users', [
            'email' => 'invalid-signature@example.com',
        ]);
        $this->assertDatabaseMissing('pendaftarans', [
            'email' => 'invalid-signature@example.com',
        ]);
        $this->assertEmpty(Storage::disk('public')->allFiles('ttd'));
    }

    public function test_duplicate_email_does_not_create_second_registration_or_signature(): void
    {
        Storage::fake('public');

        User::factory()->create(['email' => 'duplicate@example.com']);

        $this->post(route('pendaftaran.store'), $this->validPayload([
            'email' => 'duplicate@example.com',
            'nik' => '3273010101000002',
        ]))->assertSessionHasErrors('email');

        $this->assertSame(1, User::where('email', 'duplicate@example.com')->count());
        $this->assertDatabaseMissing('pendaftarans', [
            'email' => 'duplicate@example.com',
        ]);
        $this->assertEmpty(Storage::disk('public')->allFiles('ttd'));
    }
}
