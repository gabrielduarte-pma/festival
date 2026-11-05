<?php

namespace App\Http\Controllers\Painel;

use App\Http\Controllers\Controller;
use App\Models\Alteracao;

class AlteracaoController extends Controller
{
    public function index()
    {
        return view('painel.alteracoes', [
            'itens' => Alteracao::with(['usuario', 'stand'])->latest()->paginate(50),
        ]);
    }
}
