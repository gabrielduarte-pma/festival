<?php

namespace App\Concerns;

use App\Models\Alteracao;
use App\Models\Stand;
use App\Models\StandFoto;
use App\Support\ImagemStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/** Regras e gravação do perfil público de um stand (usado pelo admin e pelo expositor). */
trait SalvaPerfil
{
    protected const MAX_FOTOS = 8;

    protected function regrasPerfil(): array
    {
        return [
            'sobre' => ['nullable', 'string', 'max:1200'],
            'horario' => ['nullable', 'string', 'max:120'],
            'cardapio_url' => ['nullable', 'url', 'max:500'],
            'cardapio' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'],
            'remover_cardapio' => ['nullable', 'boolean'],
            'instagram' => ['nullable', 'string', 'max:120', 'regex:/^[A-Za-z0-9._@\/:\-?=&]+$/'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'fotos' => ['nullable', 'array', 'max:'.self::MAX_FOTOS],
            'fotos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
            'remover_fotos' => ['nullable', 'array'],
            'remover_fotos.*' => ['integer'],
        ];
    }

    protected function salvarPerfil(Stand $stand, Request $request, array $dados): void
    {
        $usuario = $request->user();

        $stand->fill([
            'sobre' => $dados['sobre'] ?? null,
            'horario' => $dados['horario'] ?? null,
            'instagram' => isset($dados['instagram']) ? trim($dados['instagram']) : null,
            'whatsapp' => isset($dados['whatsapp']) ? (preg_replace('/\D+/', '', $dados['whatsapp']) ?: null) : null,
        ]);

        // Cardápio: link OU arquivo
        if ($request->boolean('remover_cardapio')) {
            $this->apagarCardapio($stand);
            $stand->cardapio_url = null;
        }
        if (! empty($dados['cardapio_url'])) {
            $this->apagarCardapio($stand);
            $stand->cardapio_url = $dados['cardapio_url'];
        }
        if ($request->hasFile('cardapio')) {
            $this->apagarCardapio($stand);
            $stand->cardapio_path = $request->file('cardapio')->store('cardapios', 'public');
            $stand->cardapio_url = null;
        }

        $campos = array_keys($stand->getDirty());
        $stand->save();

        // Fotos
        $remover = array_map('intval', $dados['remover_fotos'] ?? []);
        if ($remover) {
            foreach ($stand->fotos()->whereIn('id', $remover)->get() as $foto) {
                Storage::disk('public')->delete($foto->path);
                $foto->delete();
                $campos[] = 'fotos (removida)';
            }
        }

        $novas = $request->file('fotos', []);
        $vagas = self::MAX_FOTOS - $stand->fotos()->count();
        $ordem = (int) $stand->fotos()->max('ordem');
        foreach (array_slice($novas, 0, max(0, $vagas)) as $arquivo) {
            StandFoto::create([
                'stand_id' => $stand->id,
                'path' => ImagemStore::salvar($arquivo, 'stands/'.$stand->id, 1600),
                'ordem' => ++$ordem,
            ]);
            $campos[] = 'fotos (nova)';
        }

        if ($campos) {
            Alteracao::create([
                'user_id' => $usuario?->id,
                'stand_id' => $stand->id,
                'resumo' => 'Perfil atualizado: '.implode(', ', array_unique($campos)),
                'dados' => ['campos' => array_values(array_unique($campos))],
            ]);
        }
    }

    private function apagarCardapio(Stand $stand): void
    {
        if ($stand->cardapio_path) {
            Storage::disk('public')->delete($stand->cardapio_path);
            $stand->cardapio_path = null;
        }
    }
}
