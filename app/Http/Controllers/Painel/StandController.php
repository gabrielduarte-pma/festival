<?php

namespace App\Http\Controllers\Painel;

use App\Concerns\SalvaPerfil;
use App\Http\Controllers\Controller;
use App\Models\Alteracao;
use App\Models\Stand;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StandController extends Controller
{
    use SalvaPerfil;

    public function index(Request $request)
    {
        $q = trim((string) $request->query('q'));
        $cat = $request->query('categoria');
        $filtro = $request->query('filtro');

        $stands = Stand::with(['fotos', 'usuarios'])
            ->when($q, fn ($s) => $s->where(fn ($w) => $w->where('nome', 'like', "%{$q}%")->orWhere('numero', 'like', "%{$q}%")))
            ->when($cat, fn ($s) => $s->where('categoria', $cat))
            ->when($filtro === 'inativos', fn ($s) => $s->where('ativo', false))
            ->when($filtro === 'sem-expositor', fn ($s) => $s->doesntHave('usuarios'))
            ->orderBy('nome')
            ->paginate(40)
            ->withQueryString();

        if ($filtro === 'incompletos') {
            $stands->setCollection($stands->getCollection()->reject->perfilCompleto()->values());
        }

        return view('painel.stands.index', compact('stands', 'q', 'cat', 'filtro'));
    }

    public function create()
    {
        $stand = new Stand(['ativo' => true, 'categoria' => 'Gastronomia']);

        return view('painel.stands.form', ['stand' => $stand, 'mapa' => $this->dadosMapa($stand)]);
    }

    public function store(Request $request)
    {
        $stand = new Stand();
        $stand->fill($this->dadosBasicos($request));
        $stand->slug = Stand::gerarSlug($stand->nome);
        $stand->save();

        $dados = $request->validate($this->regrasPerfil());
        $this->salvarPerfil($stand, $request, $dados);

        return redirect()->route('painel.stands.edit', $stand)->with('ok', 'Stand criado.');
    }

    public function edit(Stand $stand)
    {
        return view('painel.stands.form', ['stand' => $stand->load('fotos', 'usuarios'), 'mapa' => $this->dadosMapa($stand)]);
    }

    public function update(Request $request, Stand $stand)
    {
        $basicos = $this->dadosBasicos($request);
        $stand->fill($basicos);
        $mudou = array_keys($stand->getDirty());
        if (in_array('nome', $mudou, true)) {
            $stand->slug = Stand::gerarSlug($stand->nome, $stand->id);
        }
        $stand->save();

        if ($mudou) {
            Alteracao::create([
                'user_id' => $request->user()->id,
                'stand_id' => $stand->id,
                'resumo' => 'Dados do stand: '.implode(', ', $mudou),
                'dados' => ['campos' => $mudou],
            ]);
        }

        $dados = $request->validate($this->regrasPerfil());
        $this->salvarPerfil($stand, $request, $dados);

        return back()->with('ok', 'Stand salvo.');
    }

    public function destroy(Request $request, Stand $stand)
    {
        Alteracao::create(['user_id' => $request->user()->id, 'resumo' => 'Stand removido: '.$stand->nome]);
        $stand->delete();

        return redirect()->route('painel.stands.index')->with('ok', 'Stand removido.');
    }

    /** Dados para o seletor visual de posição (lotes, pontos de referência e demais stands). */
    private function dadosMapa(Stand $atual): array
    {
        $base = json_decode(file_get_contents(database_path('data/mapa.json')), true);

        return [
            'w' => config('festival.mapa.w'),
            'h' => config('festival.mapa.h'),
            'lotes' => array_map(fn ($l) => ['rect' => $l['rect']], $base['lotes']),
            'landmarks' => array_map(fn ($l) => ['nome' => $l['nome'], 'rect' => $l['rect']], $base['landmarks']),
            'stands' => Stand::where('id', '!=', $atual->id ?? 0)->get(['nome', 'rect_x', 'rect_y', 'rect_w', 'rect_h'])
                ->map(fn ($s) => ['nome' => $s->nome, 'rect' => [$s->rect_x, $s->rect_y, $s->rect_w, $s->rect_h]])->values()->all(),
        ];
    }

    private function dadosBasicos(Request $request): array
    {
        $d = $request->validate([
            'nome' => ['required', 'string', 'max:150'],
            'categoria' => ['required', Rule::in(config('festival.categorias'))],
            'numero' => ['nullable', 'string', 'max:20'],
            'ilha' => ['nullable', 'integer', 'between:1,20'],
            'posicao' => ['nullable', 'integer', 'between:1,50'],
            'lado' => ['nullable', Rule::in(['L', 'R'])],
            'referencia' => ['nullable', 'string', 'max:120'],
            'rect_x' => ['required', 'integer', 'between:0,'.config('festival.mapa.w')],
            'rect_y' => ['required', 'integer', 'between:0,'.config('festival.mapa.h')],
            'rect_w' => ['required', 'integer', 'between:1,'.config('festival.mapa.w')],
            'rect_h' => ['required', 'integer', 'between:1,'.config('festival.mapa.h')],
        ]);
        $d['ativo'] = $request->boolean('ativo');

        return $d;
    }
}
