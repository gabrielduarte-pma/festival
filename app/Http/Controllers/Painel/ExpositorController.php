<?php

namespace App\Http\Controllers\Painel;

use App\Http\Controllers\Controller;
use App\Models\Stand;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ExpositorController extends Controller
{
    public function index()
    {
        $expositores = User::where('role', 'expositor')->withCount('stands')->orderBy('name')->paginate(40);

        return view('painel.expositores.index', compact('expositores'));
    }

    public function create()
    {
        return view('painel.expositores.form', [
            'user' => new User(['ativo' => true]),
            'stands' => Stand::orderBy('nome')->get(['id', 'nome', 'numero', 'ilha', 'posicao']),
            'selecionados' => collect(old('stands', [])),
        ]);
    }

    public function store(Request $request)
    {
        $dados = $this->validar($request);

        $user = new User([
            'name' => $dados['name'],
            'email' => Str::lower($dados['email']),
            'telefone' => $dados['telefone'] ?? null,
            'password' => Str::random(40),
            'role' => 'expositor',
            'ativo' => true,
        ]);
        $user->email_verified_at = now();
        $user->save();
        $user->stands()->sync($dados['stands'] ?? []);

        $token = $user->gerarConvite();

        return redirect()->route('painel.expositores.edit', $user)
            ->with('ok', 'Expositor criado. Envie o link de convite abaixo.')
            ->with('convite', url('/convite/'.$token));
    }

    public function edit(User $expositor)
    {
        abort_if($expositor->isAdmin(), 404);

        return view('painel.expositores.form', [
            'user' => $expositor,
            'stands' => Stand::orderBy('nome')->get(['id', 'nome', 'numero', 'ilha', 'posicao']),
            'selecionados' => $expositor->stands()->pluck('stands.id'),
        ]);
    }

    public function update(Request $request, User $expositor)
    {
        abort_if($expositor->isAdmin(), 404);
        $dados = $this->validar($request, $expositor);

        $expositor->fill([
            'name' => $dados['name'],
            'email' => Str::lower($dados['email']),
            'telefone' => $dados['telefone'] ?? null,
            'ativo' => $request->boolean('ativo'),
        ])->save();
        $expositor->stands()->sync($dados['stands'] ?? []);

        return back()->with('ok', 'Expositor salvo.');
    }

    /** Gera um novo link (serve para primeiro acesso e para quem esqueceu a senha). */
    public function convite(User $expositor)
    {
        abort_if($expositor->isAdmin(), 404);
        $token = $expositor->gerarConvite();

        return back()->with('ok', 'Novo link gerado (válido por '.config('festival.convite_dias').' dias).')
            ->with('convite', url('/convite/'.$token));
    }

    public function destroy(User $expositor)
    {
        abort_if($expositor->isAdmin(), 404);
        $expositor->delete();

        return redirect()->route('painel.expositores.index')->with('ok', 'Expositor removido.');
    }

    private function validar(Request $request, ?User $atual = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:190', Rule::unique('users', 'email')->ignore($atual?->id)],
            'telefone' => ['nullable', 'string', 'max:30'],
            'stands' => ['nullable', 'array'],
            'stands.*' => ['integer', 'exists:stands,id'],
        ]);
    }
}
