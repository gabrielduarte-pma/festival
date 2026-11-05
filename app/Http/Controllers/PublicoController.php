<?php

namespace App\Http\Controllers;

use App\Support\DadosApp;

class PublicoController extends Controller
{
    public function index()
    {
        return view('publico.app');
    }

    public function dados()
    {
        return response(DadosApp::js(), 200, [
            'Content-Type' => 'application/javascript; charset=utf-8',
            'Cache-Control' => 'public, max-age=30',
        ]);
    }
}
