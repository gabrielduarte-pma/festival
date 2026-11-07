<?php

namespace App\Http\Controllers\Painel;

use App\Http\Controllers\Controller;
use App\Mail\ConviteExpositor;
use App\Models\Stand;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
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

        $link = url('/convite/'.$user->gerarConvite());

        $redirect = redirect()->route('painel.expositores.edit', $user)->with('convite', $link);

        if ($request->boolean('enviar_email')) {
            return $this->comResultadoEnvio($redirect, $user, $link, 'Expositor criado. ');
        }

        return $redirect->with('ok', 'Expositor criado. Envie o link de convite abaixo.');
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

    /** Gera um novo link (primeiro acesso ou senha esquecida). Com enviar=1 também manda por e-mail. */
    public function convite(Request $request, User $expositor)
    {
        abort_if($expositor->isAdmin(), 404);
        $link = url('/convite/'.$expositor->gerarConvite());

        $redirect = back()->with('convite', $link);

        if ($request->boolean('enviar')) {
            return $this->comResultadoEnvio($redirect, $expositor, $link);
        }

        return $redirect->with('ok', 'Novo link gerado (válido por '.config('festival.convite_dias').' dias). O link anterior deixou de valer.');
    }

    public function destroy(User $expositor)
    {
        abort_if($expositor->isAdmin(), 404);
        $expositor->delete();

        return redirect()->route('painel.expositores.index')->with('ok', 'Expositor removido.');
    }

    /** Envia o convite por e-mail e devolve o redirect com a confirmação (ou o aviso de falha). */
    private function comResultadoEnvio($redirect, User $user, string $link, string $prefixo = '')
    {
        try {
            Mail::to($user->email)->send(new ConviteExpositor($user, $link));
            $user->forceFill(['convite_enviado_em' => now()])->save();

            return $redirect->with('ok', $prefixo.'✔ E-mail enviado para '.$user->email.' às '.now()->format('H:i').'. O link também está abaixo, caso queira mandar pelo WhatsApp.');
        } catch (\Throwable $e) {
            report($e);

            return $redirect->with('aviso', $prefixo.'O e-mail NÃO foi enviado para '.$user->email.' ('.Str::limit($e->getMessage(), 180).'). Copie o link abaixo e envie manualmente, ou confira as configurações de e-mail.');
        }
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
