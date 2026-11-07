<?php

namespace Tests\Feature;

use App\Models\Stand;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Mail\ConviteExpositor;
use Tests\TestCase;

class FestivalTest extends TestCase
{
    use RefreshDatabase;

    private function stand(string $nome = 'Casa do Camarão'): Stand
    {
        return Stand::create([
            'nome' => $nome, 'slug' => Stand::gerarSlug($nome), 'categoria' => 'Gastronomia',
            'rect_x' => 10, 'rect_y' => 20, 'rect_w' => 80, 'rect_h' => 60, 'ativo' => true,
        ]);
    }

    public function test_dados_js_serves_stands_from_database()
    {
        $this->stand();

        $this->get('/dados.js')->assertOk()->assertSee('Casa do Camarão', false);
    }

    public function test_expositor_cannot_open_admin_panel()
    {
        $this->actingAs(User::factory()->create(['role' => 'expositor']));

        $this->get('/painel')->assertRedirect('/meu-stand');
    }

    public function test_admin_can_open_panel()
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        $this->get('/painel')->assertOk();
    }

    public function test_invite_flow_sets_password_and_logs_in()
    {
        $user = User::factory()->create(['role' => 'expositor', 'email_verified_at' => null]);
        $token = $user->gerarConvite();

        $this->get("/convite/{$token}")->assertOk();
        $this->post("/convite/{$token}", ['password' => 'senha-forte-123', 'password_confirmation' => 'senha-forte-123'])
            ->assertRedirect('/meu-stand');

        $this->assertAuthenticatedAs($user->fresh());
        $this->assertNull($user->fresh()->invite_token);
        $this->get("/convite/{$token}")->assertStatus(410);
    }

    public function test_expired_invite_is_rejected()
    {
        $user = User::factory()->create(['role' => 'expositor']);
        $token = $user->gerarConvite();
        $user->forceFill(['invite_expires_at' => now()->subMinute()])->save();

        $this->get("/convite/{$token}")->assertStatus(410);
    }

    public function test_expositor_edits_only_own_stand_and_goes_live()
    {
        Storage::fake('public');
        $meu = $this->stand('Meu Stand');
        $outro = $this->stand('Outro Stand');
        $user = User::factory()->create(['role' => 'expositor']);
        $user->stands()->attach($meu);

        $this->actingAs($user)
            ->put("/meu-stand/{$meu->id}", ['sobre' => 'Melhor camarão da cidade', 'instagram' => '@meustand', 'horario' => '18h às 23h'])
            ->assertSessionHasNoErrors();

        $this->assertSame('Melhor camarão da cidade', $meu->fresh()->sobre);
        $this->assertDatabaseHas('alteracoes', ['stand_id' => $meu->id, 'user_id' => $user->id]);
        $this->put("/meu-stand/{$outro->id}", ['sobre' => 'invasão'])->assertForbidden();
        $this->get('/dados.js')->assertSee('Melhor camarão da cidade', false);
    }

    public function test_photo_upload_is_stored()
    {
        Storage::fake('public');
        $stand = $this->stand();
        $user = User::factory()->create(['role' => 'expositor']);
        $user->stands()->attach($stand);

        $this->actingAs($user)->put("/meu-stand/{$stand->id}", [
            'fotos' => [UploadedFile::fake()->image('foto.jpg', 800, 600)],
        ])->assertSessionHasNoErrors();

        $this->assertCount(1, $stand->fresh()->fotos);
    }

    public function test_admin_creates_exhibitor_and_gets_invite_link()
    {
        $stand = $this->stand();
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        $this->post('/painel/expositores', ['name' => 'Maria', 'email' => 'maria@example.com', 'stands' => [$stand->id]])
            ->assertRedirect()->assertSessionHas('convite');

        $maria = User::where('email', 'maria@example.com')->first();
        $this->assertSame('expositor', $maria->role);
        $this->assertTrue($maria->podeEditar($stand));
    }

    public function test_admin_saves_map_anchors()
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        $this->put('/painel/mapa', ['pontos' => [
            ['nome' => 'INÍCIO', 'x' => 653, 'y' => 100, 'lat' => -4.56, 'lng' => -37.77],
            ['nome' => 'FIM', 'x' => 651, 'y' => 4048, 'lat' => -4.57, 'lng' => -37.77],
        ]])->assertSessionHasNoErrors();

        $this->get('/dados.js')->assertSee('-4.56', false);
    }

    public function test_creating_exhibitor_sends_invite_email_and_confirms()
    {
        Mail::fake();
        $stand = $this->stand();
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        $this->post('/painel/expositores', ['name' => 'Maria', 'email' => 'maria@example.com', 'stands' => [$stand->id], 'enviar_email' => 1])
            ->assertSessionHas('ok', fn ($m) => str_contains($m, 'E-mail enviado para maria@example.com'))
            ->assertSessionHas('convite');

        Mail::assertSent(ConviteExpositor::class, fn ($m) => $m->hasTo('maria@example.com') && str_contains($m->link, '/convite/'));
        $this->assertNotNull(User::where('email', 'maria@example.com')->first()->convite_enviado_em);
    }

    public function test_without_email_checkbox_nothing_is_sent()
    {
        Mail::fake();
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        $this->post('/painel/expositores', ['name' => 'João', 'email' => 'joao@example.com'])->assertSessionHas('convite');

        Mail::assertNothingSent();
    }

    public function test_regenerate_link_can_send_email()
    {
        Mail::fake();
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $exp = User::factory()->create(['role' => 'expositor', 'email' => 'ana@example.com']);

        $this->post("/painel/expositores/{$exp->id}/convite", ['enviar' => 1])->assertSessionHas('ok');

        Mail::assertSent(ConviteExpositor::class, fn ($m) => $m->hasTo('ana@example.com'));
    }

    public function test_email_failure_shows_warning_and_keeps_link()
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        Mail::shouldReceive('to')->andThrow(new \RuntimeException('falha smtp'));

        $this->post('/painel/expositores', ['name' => 'Rui', 'email' => 'rui@example.com', 'enviar_email' => 1])
            ->assertSessionHas('aviso')->assertSessionHas('convite');
    }
}
