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
            'cardapio_tipo' => ['nullable', 'in:link,arquivo'],
            'cardapio_url' => ['nullable', 'url', 'max:500'],
            'cardapio' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:30720'],
            'remover_cardapio' => ['nullable', 'boolean'],
            'instagram' => ['nullable', 'string', 'max:120', 'regex:/^[A-Za-z0-9._@\/:\-?=&]+$/'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'fotos' => ['nullable', 'array', 'max:'.self::MAX_FOTOS],
            'fotos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
            'remover_fotos' => ['nullable', 'array'],
            'remover_fotos.*' => ['integer'],
        ];
    }

    protected function mensagensPerfil(): array
    {
        return [
            'cardapio.max' => 'O arquivo do cardápio passa do limite de 30 MB. Envie um arquivo menor ou use o link do cardápio.',
            'cardapio.mimes' => 'O cardápio deve ser um PDF ou uma imagem (JPG, PNG ou WebP).',
            'cardapio.uploaded' => 'Não foi possível enviar o cardápio: o arquivo é maior que o limite aceito. Envie um arquivo menor.',
            'fotos.max' => 'Cada estande pode ter até '.self::MAX_FOTOS.' fotos.',
            'fotos.*.max' => 'Cada foto pode ter no máximo 8 MB. Escolha uma foto menor.',
            'fotos.*.image' => 'Os arquivos de foto devem ser imagens (JPG, PNG ou WebP).',
            'fotos.*.mimes' => 'As fotos devem ser JPG, PNG ou WebP.',
            'fotos.*.uploaded' => 'Não foi possível enviar uma das fotos: o arquivo é maior que o limite aceito (8 MB). Escolha uma foto menor.',
            'cardapio_url.url' => 'O link do cardápio precisa começar com http:// ou https://.',
            'instagram.regex' => 'O Instagram deve ter só letras, números, ponto ou sublinhado. Exemplo: @seurestaurante.',
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

        // Cardápio: o expositor escolhe usar um LINK ou enviar um ARQUIVO
        $tipo = $dados['cardapio_tipo'] ?? null;
        if ($tipo === 'link') {
            $this->apagarCardapio($stand);
            $stand->cardapio_url = ! empty($dados['cardapio_url']) ? $dados['cardapio_url'] : null;
        } elseif ($tipo === 'arquivo') {
            $stand->cardapio_url = null;
            if ($request->boolean('remover_cardapio')) {
                $this->apagarCardapio($stand);
            }
            if ($request->hasFile('cardapio')) {
                $this->apagarCardapio($stand);
                $stand->cardapio_path = $request->file('cardapio')->store('cardapios', 'public');
            }
        } else {
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
