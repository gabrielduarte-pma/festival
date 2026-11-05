<?php

namespace Database\Seeders;

use App\Models\Configuracao;
use Illuminate\Database\Seeder;

/**
 * Pontos de calibração do GPS (coletados em campo). Nas bancas de lado, x = eixo da rua (650) ± 68 (calçada).
 * O ponto do Palco foi retirado: a coordenada informada cai perto da Praça Dr. Leite, não no Palco do desenho.
 */
class MapaSeeder extends Seeder
{
    public function run(): void
    {
        Configuracao::gravar('mapa_ancoras', [
            ['nome' => 'Câmara', 'x' => 582, 'y' => 487, 'lat' => -4.562569, 'lng' => -37.772022],
            ['nome' => 'Praça Dr. Leite', 'x' => 582, 'y' => 1385, 'lat' => -4.563817, 'lng' => -37.772636],
            ['nome' => 'Teatro', 'x' => 718, 'y' => 1865, 'lat' => -4.564606, 'lng' => -37.772681],
            ['nome' => 'Museu', 'x' => 718, 'y' => 2155, 'lat' => -4.565047, 'lng' => -37.772858],
            ['nome' => 'Aracaty Club', 'x' => 582, 'y' => 2502, 'lat' => -4.565403, 'lng' => -37.773261],
            ['nome' => 'Beco das Uvas', 'x' => 582, 'y' => 2822, 'lat' => -4.565794, 'lng' => -37.773350],
            ['nome' => 'Self Point', 'x' => 651, 'y' => 3480, 'lat' => -4.566974, 'lng' => -37.773566],
        ]);
    }
}
