<?php

namespace App\Http\Controllers;

use App\Concerns\SalvaPerfil;
use App\Models\Stand;
use Illuminate\Http\Request;

class MeuStandController extends Controller
{
    use SalvaPerfil;

    public function index(Request $request)
    {
        $stands = $request->user()->stands()->with('fotos')->get();

        if ($stands->count() === 1) {
            return redirect()->route('meu-stand.edit', $stands->first());
        }

        return view('meu-stand.index', compact('stands'));
    }

    public function edit(Request $request, Stand $stand)
    {
        abort_unless($request->user()->podeEditar($stand), 403);

        return view('meu-stand.edit', ['stand' => $stand->load('fotos')]);
    }

    public function update(Request $request, Stand $stand)
    {
        abort_unless($request->user()->podeEditar($stand), 403);

        $dados = $request->validate($this->regrasPerfil());
        $total = $stand->fotos()->count() - count($dados['remover_fotos'] ?? []) + count($request->file('fotos', []));
        if ($total > self::MAX_FOTOS) {
            return back()->withErrors(['fotos' => 'Cada stand pode ter até '.self::MAX_FOTOS.' fotos.'])->withInput();
        }

        $this->salvarPerfil($stand, $request, $dados);

        return back()->with('ok', 'Informações salvas. Já estão no ar no app do festival!');
    }
}
