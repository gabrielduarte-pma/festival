<?php

namespace Database\Seeders;

use App\Models\Atracao;
use App\Models\Dia;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Programação oficial (Programacao_Festival_Atualizada_Editavel_13.docx).
 *
 * Substitui TODAS as atrações dos dias 13, 14 e 15 pela programação do arquivo.
 * Rodar no servidor:  php artisan db:seed --class=ProgramacaoFestivalSeeder --force
 *
 * - Só as atrações do Palco Principal que já tinham foto no app mantêm imagem; a foto atual
 *   (inclusive as enviadas pelo painel) é reaproveitada. As demais não têm imagem.
 * - Pode rodar de novo, mas cada execução recria as atrações (edições feitas no painel nos 3 dias se perdem).
 */
class ProgramacaoFestivalSeeder extends Seeder
{
    /** Foto padrão (pasta public/assets), destaque e espelhamento das atrações do Palco Principal com imagem. */
    private const IMAGENS = [
        'thiago' => ['thiago.webp', true, false],
        'joyce' => ['ex-joyce.jpg', false, false],
        'larissa' => ['ex-larissa.jpeg', false, false],
        'diego' => ['diego.jpeg', false, true],
        'paralamas' => ['paralamas.png', true, false],
        'wawa' => ['wawa.jpeg', false, true],
        'davizao' => ['davizao.webp', true, false],
    ];

    private const DIAS = [
        '13' => ['SEX', 'Sexta', '13/11', 0],
        '14' => ['SÁB', 'Sábado', '14/11', 1],
        '15' => ['DOM', 'Domingo', '15/11', 2],
    ];

    /** [dia, local, horário, atração, chave da imagem, ordem] */
    private function atracoes(): array
    {
        return [
            ['13', 'Espaço das Artes · Pavimento Superior', '18h', 'Exposição Boi do Mestre Cajueiro', null, 10],
            ['13', 'Espaço das Artes · Salão Mestre Hélio', '18h', 'Exposição Hélio Santos - O Mestre do Aracati', null, 11],
            ['13', 'Espaço das Artes · Salão Principal', '18h', 'Ser Tão Aracatiense - O fazer que cria histórias', null, 12],
            ['13', 'Arena Kids', '18h30', 'O Grande Rabanete (Creche Saskia)', null, 13],
            ['13', 'Lounge Boas-Vindas · Sertão', '18h30', 'Danilo Ferrer - O Príncipe da Sanfona', null, 14],
            ['13', 'Lounge História · K2', '18h30', 'Violinista Pablo Neruda (Parte 1)', null, 15],
            ['13', 'Teatro Francisca Clotildes · Interno - Palco', '18h30', 'O Mistério do Beco do Barão', null, 16],
            ['13', 'Lounge Boticário', '19h', 'DJ Jeff', null, 17],
            ['13', 'Palco Mar', '19h', 'Atos 29 (Gospel)', null, 18],
            ['13', 'Teatro Francisca Clotildes · Interno - Palco', '19h', 'Mundo Encantado do Darcy Encantos', null, 19],
            ['13', 'Arena Kids', '19h30', 'Os Batukinhas', null, 20],
            ['13', 'Lounge História · K2', '19h30', 'Abertura Oficial seguido de apresentação da Banda JK', null, 21],
            ['13', 'Orquestra no Sobrado · Instituto do Museu Jaguaribano', '19h30', 'Concerto com a Banda Municipal Jacques Klein - Cancioneiro Popular', null, 22],
            ['13', 'Palco Mar', '19h30', 'Samba Natividade', null, 23],
            ['13', 'Teatro Francisca Clotildes · Interno - Palco', '19h30', 'O Mágico de Oz', null, 24],
            ['13', 'Teatro Francisca Clotildes · Jardim', '19h30', 'Cultura Viva - Balé', null, 25],
            ['13', 'Cortejos', '20h', 'Bonecos da Vila', null, 26],
            ['13', 'Lounge Quimanga', '20h', 'Jorginho Maia - Brasilidades', null, 27],
            ['13', 'Palco Principal', '20h', 'Eliezio', null, 28],
            ['13', 'Lounge Boas-Vindas · Sertão', '20h30', 'Cantoria Popular - Rogaciano Lopes', null, 29],
            ['13', 'Lounge Boticário', '20h30', 'Isaac Machado', null, 30],
            ['13', 'Lounge História · K2', '20h30', 'Violinista Pablo Neruda (Parte 2)', null, 31],
            ['13', 'Teatro Francisca Clotildes · Interno - Palco', '20h30', 'Vestígios', null, 32],
            ['13', 'Cortejos', '21h', 'Bonecos Gigantes Mestre Hélio', null, 33],
            ['13', 'Lounge Boas-Vindas · Sertão', '21h', 'Pé de Serra Paulo e Zefinha', null, 34],
            ['13', 'Palco Principal', '21h', 'Larissa Leite', 'larissa', 2],
            ['13', 'Lounge História · K2', '21h30', 'João Vitor - Canções de Amor', null, 35],
            ['13', 'Lounge História · K2', '22h30', 'DJ Breno - Vintage Café', null, 36],
            ['13', 'Palco Mar', '22h30', 'Sérfano Giló', null, 37],
            ['13', 'Palco Principal', '23h', 'Thiago Freitas', 'thiago', 0],
            ['13', 'Palco Principal', '1h', 'Joyce Tainá', 'joyce', 1],
            ['14', 'Espaço das Artes · Pavimento Superior', '18h', 'Exposição Boi do Mestre Cajueiro', null, 10],
            ['14', 'Espaço das Artes · Salão Mestre Hélio', '18h', 'Exposição Hélio Santos - O Mestre do Aracati', null, 11],
            ['14', 'Espaço das Artes · Salão Principal', '18h', 'Ser Tão Aracatiense - O fazer que cria histórias', null, 12],
            ['14', 'Teatro Francisca Clotildes · Interno - Palco', '18h', 'Summer Nights', null, 13],
            ['14', 'Arena Kids', '18h30', 'Contação de Histórias (Creche Maria Eridan)', null, 14],
            ['14', 'Lounge Boas-Vindas · Sertão', '18h30', 'Danilo Ferrer - O Príncipe da Sanfona', null, 15],
            ['14', 'Cortejos', '19h', 'Boi do Mestre Cajueiro', null, 16],
            ['14', 'Lounge Boticário', '19h', 'DJ Jeff', null, 17],
            ['14', 'Lounge História · K2', '19h', 'Banda JK', null, 18],
            ['14', 'Orquestra no Sobrado · Instituto do Museu Jaguaribano', '19h', 'Concerto com a Banda Municipal Jacques Klein - Cancioneiro Popular', null, 19],
            ['14', 'Teatro Francisca Clotildes · Interno - Palco', '19h', 'O Ir - Jamais Desistir e Sim Persistir para Existir', null, 20],
            ['14', 'Teatro Francisca Clotildes · Jardim', '19h', 'Peça Teatral: Menino Mangue', null, 21],
            ['14', 'Arena Kids', '19h30', 'Os Batukinhas', null, 22],
            ['14', 'Lounge História · K2', '19h30', 'Pianista Christian', null, 23],
            ['14', 'Teatro Francisca Clotildes · Interno - Palco', '19h40', 'Contação de História com Nyedja Gennari', null, 24],
            ['14', 'Cortejos', '20h', 'Bonecos da Vila', null, 25],
            ['14', 'Lounge Quimanga', '20h', 'Saxofonista Assis Matos', null, 26],
            ['14', 'Palco Principal', '20h', 'Eliezio', null, 27],
            ['14', 'Teatro Francisca Clotildes · Interno - Palco', '20h', 'Show de Humor - Deus, Eu Tô Tentando', null, 28],
            ['14', 'Lounge Boas-Vindas · Sertão', '20h30', 'Dideus Sales - Recital Poesias do Sertão', null, 29],
            ['14', 'Lounge Boticário', '20h30', 'Isaac Machado', null, 30],
            ['14', 'Palco Mar', '20h30', 'Trio Huana', null, 31],
            ['14', 'Cortejos', '21h', 'Bonecos Gigantes do Mestre Hélio', null, 32],
            ['14', 'Lounge Boas-Vindas · Sertão', '21h', 'Rafael Costa - Forró Diferenciado', null, 33],
            ['14', 'Lounge História · K2', '21h', 'João Vitor - Canções de Amor', null, 34],
            ['14', 'Palco Principal', '21h', 'A definir', null, 35],
            ['14', 'Lounge História · K2', '22h30', 'DJ Breno', null, 36],
            ['14', 'Palco Mar', '22h30', 'Batucada do Brow', null, 37],
            ['14', 'Palco Principal', '23h', 'Os Paralamas do Sucesso', 'paralamas', 1],
            ['14', 'Palco Principal', '1h', 'Diego Facó', 'diego', 0],
            ['15', 'Espaço das Artes · Pavimento Superior', '18h', 'Exposição Boi do Mestre Cajueiro', null, 10],
            ['15', 'Espaço das Artes · Salão Mestre Hélio', '18h', 'Exposição Hélio Santos - O Mestre do Aracati', null, 11],
            ['15', 'Espaço das Artes · Salão Principal', '18h', 'Ser Tão Aracatiense - O fazer que cria histórias', null, 12],
            ['15', 'Palco Mar', '18h', 'Canção da Paz (Gospel)', null, 13],
            ['15', 'Lounge Boas-Vindas · Sertão', '19h', 'Viviane Silva', null, 14],
            ['15', 'Lounge Boticário', '19h', 'DJ Jeff', null, 15],
            ['15', 'Lounge História · K2', '19h', 'Banda JK', null, 16],
            ['15', 'Orquestra no Sobrado · Instituto do Museu Jaguaribano', '19h', 'Concerto com a Banda Municipal Jacques Klein - Cancioneiro Popular', null, 17],
            ['15', 'Palco Principal', '19h', 'Eliezio', null, 18],
            ['15', 'Teatro Francisca Clotildes · Interno - Palco', '19h', 'Linguae', null, 19],
            ['15', 'Teatro Francisca Clotildes · Jardim', '19h', 'Peça Teatral: Menino Mangue', null, 20],
            ['15', 'Arena Kids', '19h30', 'Os Batukinhas', null, 21],
            ['15', 'Lounge História · K2', '19h30', 'Sonart Choro e piano', null, 22],
            ['15', 'Palco Mar', '19h30', 'Jeanzinho Classe A Acústico', null, 23],
            ['15', 'Arena Kids', '20h', 'Contação de Histórias (Creche Ângela Clotilde)', null, 24],
            ['15', 'Cortejos', '20h', 'Maracaty', null, 25],
            ['15', 'Lounge Quimanga', '20h', 'Samia Freitas', null, 26],
            ['15', 'Palco Principal', '20h', 'Davizão', 'davizao', 1],
            ['15', 'Teatro Francisca Clotildes · Interno - Palco', '20h', 'Fragmentos', null, 27],
            ['15', 'Lounge Boticário', '20h30', 'Isaac Machado', null, 28],
            ['15', 'Lounge Boas-Vindas · Sertão', '21h', 'Danilo Ferrer - O Príncipe da Sanfona', null, 29],
            ['15', 'Teatro Francisca Clotildes · Interno - Palco', '21h', 'Circo das Letras e a Magia das Mil e Uma Noites', null, 30],
            ['15', 'Lounge História · K2', '21h30', 'João Vitor - Canções de Amor', null, 31],
            ['15', 'Palco Mar', '22h', 'Ruderalis', null, 32],
            ['15', 'Lounge História · K2', '22h30', 'DJ Breno', null, 33],
            ['15', 'Palco Principal', '22h30', 'Wawá', 'wawa', 0]
        ];
    }

    private function norm(string $s): string
    {
        return Str::lower(Str::ascii($s));
    }

    public function run(): void
    {
        DB::transaction(function () {
            // fotos/ajustes atuais por chave (antes de apagar)
            $atuais = Atracao::whereNotNull('img')->get();
            $img = [];
            foreach (self::IMAGENS as $chave => [$padrao, $dest, $flip]) {
                $achada = $atuais->first(fn ($a) => str_contains($this->norm($a->nome), $chave));
                $img[$chave] = $achada
                    ? [$achada->img, (bool) $achada->destaque, (bool) $achada->flip]
                    : [$padrao, $dest, $flip];
            }

            $dias = [];
            foreach (self::DIAS as $slug => [$sigla, $nome, $data, $ordem]) {
                $dias[$slug] = Dia::updateOrCreate(['slug' => $slug], compact('sigla', 'nome', 'data', 'ordem'));
            }
            Atracao::whereIn('dia_id', collect($dias)->pluck('id'))->delete();

            foreach ($this->atracoes() as [$d, $palco, $hora, $nome, $chave, $ordem]) {
                [$foto, $dest, $flip] = $chave ? $img[$chave] : [null, false, false];
                Atracao::create([
                    'dia_id' => $dias[$d]->id,
                    'nome' => $nome,
                    'hora' => $hora,
                    'palco' => $palco,
                    'img' => $foto,
                    'destaque' => $dest,
                    'flip' => $flip,
                    'ordem' => $ordem,
                ]);
            }
        });
    }
}
