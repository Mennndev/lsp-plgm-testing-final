<?php

namespace Tests\Feature;

use App\Models\Berita;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoWriteFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_and_admin_can_complete_live_chat_flow(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->post(route('chat.store'), [
            'subject' => 'Pertanyaan Demo Sidang',
            'message' => 'Halo admin, saya ingin bertanya.',
        ]);

        $chat = Chat::firstOrFail();
        $response->assertRedirect(route('chat.show', $chat->id));
        $this->assertDatabaseHas('chat_messages', [
            'chat_id' => $chat->id,
            'user_id' => $user->id,
            'message' => 'Halo admin, saya ingin bertanya.',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.chat.show', $chat->id))
            ->assertOk();

        $this->assertDatabaseHas('chats', [
            'id' => $chat->id,
            'admin_id' => $admin->id,
            'status' => 'open',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.chat.send-message', $chat->id), [
                'message' => 'Halo, ada yang bisa dibantu?',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('chat_messages', [
            'chat_id' => $chat->id,
            'user_id' => $admin->id,
            'message' => 'Halo, ada yang bisa dibantu?',
        ]);

        $this->actingAs($user)
            ->get(route('chat.get-messages', $chat->id))
            ->assertOk()
            ->assertJsonPath('status', 'open');

        $this->actingAs($admin)
            ->post(route('admin.chat.close', $chat->id), [
                'message' => 'Chat ditutup setelah selesai.',
            ])
            ->assertRedirect(route('admin.chat.index'));

        $this->assertDatabaseHas('chats', [
            'id' => $chat->id,
            'status' => 'closed',
        ]);
    }

    public function test_admin_can_create_edit_publish_and_delete_news(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('admin.berita.create'))
            ->assertOk();

        $this->actingAs($admin)
            ->post(route('admin.berita.store'), [
                'judul' => 'Berita Demo Sidang',
                'ringkasan' => 'Ringkasan berita demo.',
                'konten' => 'Isi berita untuk demonstrasi sistem.',
                'tanggal_terbit' => now()->format('Y-m-d'),
                'is_published' => '1',
            ])
            ->assertRedirect(route('admin.berita.index'));

        $berita = Berita::where('slug', 'berita-demo-sidang')->firstOrFail();

        $this->get(route('berita.show', $berita->slug))
            ->assertOk()
            ->assertSee('Berita Demo Sidang');

        $this->actingAs($admin)
            ->get(route('admin.berita.edit', $berita->id))
            ->assertOk();

        $this->actingAs($admin)
            ->put(route('admin.berita.update', $berita->id), [
                'judul' => 'Berita Demo Sidang Diperbarui',
                'ringkasan' => 'Ringkasan diperbarui.',
                'konten' => 'Isi berita yang sudah diperbarui.',
                'tanggal_terbit' => now()->format('Y-m-d'),
                'is_published' => '1',
            ])
            ->assertRedirect(route('admin.berita.index'));

        $berita->refresh();
        $this->assertSame('berita-demo-sidang-diperbarui', $berita->slug);

        $this->actingAs($admin)
            ->delete(route('admin.berita.destroy', $berita->id))
            ->assertRedirect(route('admin.berita.index'));

        $this->assertDatabaseMissing('artikels', ['id' => $berita->id]);
    }
}
