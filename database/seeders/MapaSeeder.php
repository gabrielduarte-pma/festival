<?php

namespace Database\Seeders;

use App\Models\Configuracao;
use Illuminate\Database\Seeder;

/** Pontos de calibração do GPS (coordenadas coletadas no meio da rua). */
class MapaSeeder extends Seeder
{
    public function run(): void
    {
        Configuracao::gravar('mapa_ancoras', [
            ['nome' => 'Palco',      'x' => 653, 'y' => 152,  'lat' => -4.563732, 'lng' => -37.772436],
            ['nome' => 'Self Point', 'x' => 651, 'y' => 3480, 'lat' => -4.566974, 'lng' => -37.773566],
        ]);
    }
}
