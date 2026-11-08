<?php

namespace Database\Seeders;

use App\Models\Stand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class StandSeeder extends Seeder
{
    /**
     * Sincroniza os stands com o mapa (database/data/stands.json).
     * Idempotente: atualiza nome, categoria e posição pelo id, mas preserva o perfil já
     * preenchido (sobre, horário, cardápio, redes, fotos). Os ids listados em
     * stands_removidos.json (que saíram do mapa) ficam inativos em vez de apagados.
     */
    public function run(): void
    {
        $itens = json_decode(file_get_contents(database_path('data/stands.json')), true);
        $removidos = json_decode(file_get_contents(database_path('data/stands_removidos.json')), true);

        foreach ($itens as $i) {
            $stand = Stand::find($i['id']) ?? new Stand();
            $novo = ! $stand->exists;
            $stand->id = $i['id'];
            $stand->fill([
                'nome' => $i['nome'],
                'slug' => ($stand->slug && $stand->nome === $i['nome']) ? $stand->slug : Str::slug($i['nome']).'-'.$i['id'],
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
            if ($novo) {
                $stand->ativo = true;
            }
            $stand->save();
        }

        $desativados = Stand::whereIn('id', $removidos)->where('ativo', true)->update(['ativo' => false]);

        $this->command?->info(count($itens).' stands sincronizados; '.$desativados.' desativados (saíram do mapa).');
    }
}
