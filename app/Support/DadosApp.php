<?php

namespace App\Support;

use App\Models\Configuracao;
use App\Models\Dia;
use App\Models\Stand;

/** Monta os objetos (EVENTO, DIAS, STANDS, PERFIS, MAPA) que o app público lê em /dados.js. */
class DadosApp
{
    public static function evento(): array
    {
        return config('festival.evento');
    }

    public static function dias(): array
    {
        return Dia::with('atracoes')->orderBy('ordem')->get()->map(fn ($d) => [
            'id' => $d->slug,
            'sigla' => $d->sigla,
            'nome' => $d->nome,
            'data' => $d->data,
            'atracoes' => $d->atracoes->map(fn ($a) => array_filter([
                'nome' => $a->nome,
                'hora' => $a->hora ?? '',
                'palco' => $a->palco ?? '',
                'img' => $a->imgParaApp(),
                'destaque' => $a->destaque ?: null,
                'flip' => $a->flip ?: null,
            ], fn ($v) => $v !== null))->values()->all(),
        ])->values()->all();
    }

    public static function stands(): array
    {
        return Stand::where('ativo', true)->orderBy('id')->get()->map(fn ($s) => [
            'id' => $s->id,
            'nome' => $s->nome,
            'cat' => $s->categoria,
            'num' => $s->numero ?? '',
            'rect' => [$s->rect_x, $s->rect_y, $s->rect_w, $s->rect_h],
            'ilha' => $s->ilha,
            'pos' => $s->posicao,
            'lado' => $s->lado,
            'ref' => $s->referencia ?? '',
        ])->values()->all();
    }

    public static function perfis(): object
    {
        $out = [];
        foreach (Stand::with('fotos')->where('ativo', true)->get() as $s) {
            $p = array_filter([
                'fotos' => $s->fotos->map(fn ($f) => $f->url())->all() ?: null,
                'sobre' => $s->sobre,
                'horario' => $s->horario,
                'cardapio' => $s->cardapioHref(),
                'instagram' => $s->instagram,
                'whatsapp' => $s->whatsapp,
            ], fn ($v) => $v !== null && $v !== '');
            if ($p) {
                $out[$s->id] = $p;
            }
        }

        return (object) $out;
    }

    public static function mapa(): array
    {
        $base = json_decode(file_get_contents(database_path('data/mapa.json')), true);
        $base['src'] = '/assets/mapa.svg';
        $base['ancoras'] = array_values(Configuracao::obter('mapa_ancoras', []));

        return $base;
    }

    public static function js(): string
    {
        $j = fn ($v) => json_encode($v, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG);

        return 'window.EVENTO='.$j(self::evento()).";\n"
            .'window.DIAS='.$j(self::dias()).";\n"
            .'window.STANDS='.$j(self::stands()).";\n"
            .'window.PERFIS='.$j(self::perfis()).";\n"
            .'window.MAPA='.$j(self::mapa()).";\n";
    }
}
