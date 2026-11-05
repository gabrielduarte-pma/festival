<?php

namespace Database\Seeders;

use App\Models\Atracao;
use App\Models\Dia;
use Illuminate\Database\Seeder;

class ProgramacaoSeeder extends Seeder
{
    public function run(): void
    {
        if (Dia::count() > 0) {
            return;
        }

        $dias = [
            [
                '13',
                'SEX',
                'Sexta',
                '13/11',
                [
                    ['Thiago Freitas', 'thiago.webp', true, false],
                    ['Serfano Giló', 'serfano.png', false, false],
                    ['Joyce Tainá', 'ex-joyce.jpg', false, false],
                    ['Larissa Leite', 'ex-larissa.jpeg', false, false],
                ]
            ],
            [
                '14',
                'SÁB',
                'Sábado',
                '14/11',
                [
                    ['Diego Facó', 'diego.jpeg', false, true],
                    ['Paralamas do Sucesso', 'paralamas.png', true, false],
                ]
            ],
            [
                '15',
                'DOM',
                'Domingo',
                '15/11',
                [
                    ['Wawa', 'wawa.jpeg', false, true],
                    ['Davizão', 'davizao.webp', true, false],
                ]
            ],
        ];

        foreach ($dias as $o => [$slug, $sigla, $nome, $data, $atracoes]) {
            $dia = Dia::create(compact('slug', 'sigla', 'nome', 'data') + ['ordem' => $o]);
            foreach ($atracoes as $n => [$an, $img, $dest, $flip]) {
                Atracao::create([
                    'dia_id' => $dia->id,
                    'nome' => $an,
                    'palco' => 'Palco principal',
                    'img' => $img,
                    'destaque' => $dest,
                    'flip' => $flip,
                    'ordem' => $n,
                ]);
            }
        }
    }
}
