<?php

namespace Database\Seeders;

use App\Models\Stand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class StandSeeder extends Seeder
{
    /** Importa os stands extraídos do mapa (database/data/stands.json). Idempotente: preserva o perfil já preenchido. */
    public function run(): void
    {
        $itens = json_decode(file_get_contents(database_path('data/stands.json')), true);

        foreach ($itens as $i) {
            $stand = Stand::find($i['id']) ?? new Stand();
            $stand->id = $i['id'];
            $stand->fill([
                'nome' => $i['nome'],
                'slug' => $stand->slug ?: Str::slug($i['nome']).'-'.$i['id'],
                'categoria' => $i['cat'],
                'numero' => $i['num'] !== '' ? $i['num'] : null,
                'ilha' => $i['ilha'],
                'posicao' => $i['pos'],
                'lado' => $i['lado'],
                'referencia' => $i['ref'] !== '' ? $i['ref'] : null,
                'rect_x' => $i['rect'][0],
                'rect_y' => $i['rect'][1],
                'rect_w' => $i['rect'][2],
                'rect_h' => $i['rect'][3],
            ]);
            $stand->save();
        }

        $this->command?->info(count($itens).' stands importados.');
    }
}
