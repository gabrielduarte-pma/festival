<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Atracao extends Model
{
    protected $table = 'atracoes';

    protected $fillable = ['dia_id', 'nome', 'hora', 'palco', 'img', 'destaque', 'flip', 'ordem'];

    protected function casts(): array
    {
        return ['destaque' => 'boolean', 'flip' => 'boolean'];
    }

    public function dia(): BelongsTo
    {
        return $this->belongsTo(Dia::class);
    }

    /** Caminho usado pelo app: imagens enviadas ficam em /storage, as de exemplo em /assets. */
    public function imgParaApp(): ?string
    {
        if (! $this->img) {
            return null;
        }

        return str_starts_with($this->img, 'storage/') ? '/'.$this->img : $this->img;
    }
}
