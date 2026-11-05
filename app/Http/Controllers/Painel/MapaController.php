<?php

namespace App\Http\Controllers\Painel;

use App\Http\Controllers\Controller;
use App\Models\Configuracao;
use Illuminate\Http\Request;

class MapaController extends Controller
{
    public function edit()
    {
        $salvas = Configuracao::obter('mapa_ancoras', []);
        $mapa = json_decode(file_get_contents(database_path('data/mapa.json')), true);

        // Sempre mostra 4 linhas: as salvas e, se faltar, os pontos INÍCIO e FIM do mapa.
        $linhas = $salvas ?: [
            ['nome' => 'INÍCIO (topo do mapa, acima do Palco)', 'x' => 653, 'y' => 100, 'lat' => null, 'lng' => null],
            ['nome' => 'FIM (base do mapa, abaixo do Rosário)', 'x' => 651, 'y' => 4048, 'lat' => null, 'lng' => null],
        ];
        while (count($linhas) < 4) {
            $linhas[] = ['nome' => '', 'x' => null, 'y' => null, 'lat' => null, 'lng' => null];
        }

        return view('painel.mapa', ['linhas' => $linhas, 'referencias' => $mapa['landmarks']]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'pontos' => ['required', 'array', 'max:8'],
            'pontos.*.nome' => ['nullable', 'string', 'max:80'],
            'pontos.*.x' => ['nullable', 'numeric', 'between:0,1274'],
            'pontos.*.y' => ['nullable', 'numeric', 'between:0,4071'],
            'pontos.*.lat' => ['nullable', 'numeric', 'between:-90,90'],
            'pontos.*.lng' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        $validos = [];
        foreach ($request->input('pontos') as $p) {
            if (($p['lat'] ?? '') !== '' && ($p['lng'] ?? '') !== '' && ($p['x'] ?? '') !== '' && ($p['y'] ?? '') !== '') {
                $validos[] = [
                    'nome' => $p['nome'] ?? '',
                    'x' => (float) $p['x'], 'y' => (float) $p['y'],
                    'lat' => (float) $p['lat'], 'lng' => (float) $p['lng'],
                ];
            }
        }

        Configuracao::gravar('mapa_ancoras', $validos);

        $msg = count($validos) >= 2
            ? 'Calibração salva. O GPS do app está ativo ('.count($validos).' pontos).'
            : 'Salvo, mas o GPS só liga com pelo menos 2 pontos completos.';

        return back()->with('ok', $msg);
    }
}
