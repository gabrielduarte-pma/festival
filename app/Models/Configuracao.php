<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracao extends Model
{
    protected $table = 'configuracoes';

    protected $primaryKey = 'chave';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['chave', 'valor'];

    public static function obter(string $chave, mixed $padrao = null): mixed
    {
        $r = static::find($chave);

        return $r && $r->valor !== null ? json_decode($r->valor, true) : $padrao;
    }

    public static function gravar(string $chave, mixed $valor): void
    {
        static::updateOrCreate(['chave' => $chave], ['valor' => json_encode($valor, JSON_UNESCAPED_UNICODE)]);
    }
}
