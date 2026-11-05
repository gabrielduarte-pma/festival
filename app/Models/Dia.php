<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dia extends Model
{
    protected $table = 'dias';

    protected $fillable = ['slug', 'sigla', 'nome', 'data', 'ordem'];

    public function atracoes(): HasMany
    {
        return $this->hasMany(Atracao::class)->orderBy('ordem')->orderBy('id');
    }
}
