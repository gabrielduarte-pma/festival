<?php

namespace App\Http\Controllers\Painel;

use App\Http\Controllers\Controller;
use App\Models\Atracao;
use App\Models\Dia;
use App\Support\ImagemStore;
use Illuminate\Http\Request;

class ProgramacaoController extends Controller
{
    public function index()
    {
        return view('painel.programacao.index', ['dias' => Dia::with('atracoes')->orderBy('ordem')->get()]);
    }

    public function storeDia(Request $request)
    {
        $d = $request->validate([
            'slug' => ['required', 'string', 'max:10', 'unique:dias,slug'],
            'sigla' => ['required', 'string', 'max:5'],
            'nome' => ['required', 'string', 'max:30'],
            'data' => ['required', 'string', 'max:10'],
        ]);
        Dia::create($d + ['ordem' => (int) Dia::max('ordem') + 1]);

        return back()->with('ok', 'Dia criado.');
    }

    public function updateDia(Request $request, Dia $dia)
    {
        $dia->update($request->validate([
            'sigla' => ['required', 'string', 'max:5'],
            'nome' => ['required', 'string', 'max:30'],
            'data' => ['required', 'string', 'max:10'],
        ]));

        return back()->with('ok', 'Dia atualizado.');
    }

    public function destroyDia(Dia $dia)
    {
        foreach ($dia->atracoes as $a) {
            ImagemStore::apagar($a->img && str_starts_with($a->img, 'storage/') ? $a->img : null);
        }
        $dia->delete();

        return back()->with('ok', 'Dia removido.');
    }

    public function storeAtracao(Request $request, Dia $dia)
    {
        $d = $this->validar($request);
        $a = new Atracao($d);
        $a->dia_id = $dia->id;
        $a->ordem = (int) $dia->atracoes()->max('ordem') + 1;
        $this->aplicarImagem($request, $a);
        $a->save();

        return back()->with('ok', 'Atração adicionada.');
    }

    public function updateAtracao(Request $request, Atracao $atracao)
    {
        $atracao->fill($this->validar($request));
        $this->aplicarImagem($request, $atracao);
        $atracao->save();

        return back()->with('ok', 'Atração salva.');
    }

    public function destroyAtracao(Atracao $atracao)
    {
        if ($atracao->img && str_starts_with($atracao->img, 'storage/')) {
            ImagemStore::apagar($atracao->img);
        }
        $atracao->delete();

        return back()->with('ok', 'Atração removida.');
    }

    private function validar(Request $request): array
    {
        $d = $request->validate([
            'nome' => ['required', 'string', 'max:150'],
            'hora' => ['nullable', 'string', 'max:20'],
            'palco' => ['nullable', 'string', 'max:120'],
            'ordem' => ['nullable', 'integer', 'between:0,99'],
            'imagem' => ['nullable', 'image', 'mimes:png,webp,jpg,jpeg', 'max:6144'],
        ]);
        unset($d['imagem']);
        $d['destaque'] = $request->boolean('destaque');
        $d['flip'] = $request->boolean('flip');
        $d['ordem'] = $d['ordem'] ?? 0;

        return $d;
    }

    private function aplicarImagem(Request $request, Atracao $atracao): void
    {
        if ($request->boolean('remover_imagem') && $atracao->img) {
            ImagemStore::apagar(str_starts_with($atracao->img, 'storage/') ? $atracao->img : null);
            $atracao->img = null;
        }
        if ($request->hasFile('imagem')) {
            if ($atracao->img && str_starts_with($atracao->img, 'storage/')) {
                ImagemStore::apagar($atracao->img);
            }
            // PNG/WebP com fundo transparente continuam transparentes (convertidos para WebP).
            $atracao->img = 'storage/'.ImagemStore::salvar($request->file('imagem'), 'atracoes', 1400);
        }
    }
}
