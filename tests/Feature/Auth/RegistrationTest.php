<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $this->get(route('pendaftaran.create'))->assertOk();
    }

    public function test_new_users_can_register(): void
    {
        Storage::fake('public');

        $response = $this->post(route('pendaftaran.store'), [
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
        ]);

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
}
