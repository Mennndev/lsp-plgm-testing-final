<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    private function validProfileData(array $overrides = []): array
    {
        return array_merge([
            'nama' => 'Test User',
            'no_hp' => '081234567890',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '2000-01-01',
            'jenis_kelamin' => 'Laki-laki',
            'no_ktp' => '3273010101000001',
            'alamat' => 'Jl. Test No. 1',
            'kota' => 'Bandung',
            'provinsi' => 'Jawa Barat',
            'pendidikan' => 'D3',
            'pekerjaan' => 'Mahasiswa',
            'instansi' => 'Politeknik LP3I',
        ], $overrides);
    }

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('ProfileUser.edit'))
            ->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->patch(route('ProfileUser.update'), $this->validProfileData());

        $response->assertSessionHasNoErrors()
            ->assertRedirect(route('ProfileUser.edit'));

        $this->assertSame('Test User', $user->fresh()->nama);
        $this->assertDatabaseHas('pendaftarans', [
            'user_id' => $user->id,
            'no_ktp' => '3273010101000001',
            'kota' => 'Bandung',
        ]);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->delete(route('ProfileUser.destroy'), [
                'password' => 'password',
            ]);

        $response->assertSessionHasNoErrors()->assertRedirect('/');
        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->from(route('ProfileUser.edit'))
            ->delete(route('ProfileUser.destroy'), [
                'password' => 'wrong-password',
            ]);

        $response->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect(route('ProfileUser.edit'));

        $this->assertNotNull($user->fresh());
    }
}
