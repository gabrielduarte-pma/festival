<?php

namespace App\Http\Controllers\Painel;

use App\Http\Controllers\Controller;
use App\Models\Alteracao;
use App\Models\Stand;
use App\Models\User;

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
}
