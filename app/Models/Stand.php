<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Stand extends Model
{
    protected $fillable = [
        'nome', 'slug', 'categoria', 'numero', 'ilha', 'posicao', 'lado', 'referencia',
        'rect_x', 'rect_y', 'rect_w', 'rect_h',
        'sobre', 'horario', 'cardapio_url', 'cardapio_path', 'instagram', 'whatsapp', 'ativo',
    ];

    protected function casts(): array
    {
        return ['ativo' => 'boolean'];
    }

    public function fotos(): HasMany
    {
        return $this->hasMany(StandFoto::class)->orderBy('ordem')->orderBy('id');
    }

    public function usuarios(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'stand_user');
    }

    public static function gerarSlug(string $nome, ?int $ignorarId = null): string
    {
        $base = Str::slug($nome) ?: 'stand';
        $slug = $base;
        $i = 2;
        while (static::where('slug', $slug)->when($ignorarId, fn ($q) => $q->where('id', '!=', $ignorarId))->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    public function localizacao(): string
    {
        if ($this->ilha) {
            return 'Ilha '.$this->ilha.' · posição '.$this->posicao;
        }

        return $this->numero && $this->numero !== 's/n' ? 'Lote nº '.$this->numero : 'Sem número';
    }

    public function perfilCompleto(): bool
    {
        return $this->sobre && ($this->cardapio_url || $this->cardapio_path) && $this->instagram && $this->fotos->count() > 0;
    }

    public function cardapioHref(): ?string
    {
        if ($this->cardapio_url) {
            return $this->cardapio_url;
        }

        return $this->cardapio_path ? '/storage/'.$this->cardapio_path : null;
    }

    /** O retângulo do stand cabe dentro do desenho atual do mapa? */
    public function noMapa(): bool
    {
        $w = config('festival.mapa.w');
        $h = config('festival.mapa.h');

        return $this->rect_x >= 0 && $this->rect_y >= 0
            && $this->rect_x + $this->rect_w <= $w
            && $this->rect_y + $this->rect_h <= $h;
    }
}
