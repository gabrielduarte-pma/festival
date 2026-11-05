<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alteracao extends Model
{
    protected $table = 'alteracoes';

    protected $fillable = ['user_id', 'stand_id', 'resumo', 'dados'];

    protected function casts(): array
    {
        return ['dados' => 'array'];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function stand(): BelongsTo
    {
        return $this->belongsTo(Stand::class);
    }
}
