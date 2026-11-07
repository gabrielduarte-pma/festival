<?php

namespace App\Http\Controllers\Painel;

use App\Http\Controllers\Controller;
use App\Models\Alteracao;
use App\Models\Stand;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index()
    {
        $stands = Stand::with('fotos')->get();

        return view('painel.dashboard', [
            'total' => $stands->count(),
            'ativos' => $stands->where('ativo', true)->count(),
            'completos' => $stands->filter->perfilCompleto()->count(),
            'comFoto' => $stands->filter(fn ($s) => $s->fotos->count() > 0)->count(),
            'expositores' => User::where('role', 'expositor')->count(),
            'pendentes' => User::where('role', 'expositor')->whereNotNull('invite_token')->count(),
            'recentes' => Alteracao::with(['usuario', 'stand'])->latest()->limit(10)->get(),
        ]);
    }

    /** Manda um e-mail de teste para o próprio administrador (confere a configuração do Brevo). */
    public function emailTeste(Request $request)
    {
        $para = $request->user()->email;

        try {
            Mail::raw('Teste de envio do painel do Festival de Gastronomia e Cultura do Aracati. Se você recebeu esta mensagem, o e-mail está configurado corretamente.', function ($m) use ($para) {
                $m->to($para)->subject('Teste de e-mail — Festival Aracati');
            });

            return back()->with('ok', '✔ E-mail de teste enviado para '.$para.' às '.now()->format('H:i').'. Confira a caixa de entrada (e o spam).');
        } catch (\Throwable $e) {
            report($e);

            return back()->with('aviso', 'O e-mail de teste NÃO foi enviado ('.Str::limit($e->getMessage(), 200).'). Confira as variáveis MAIL_* no .env.');
        }
    }
}
