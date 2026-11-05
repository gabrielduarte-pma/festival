<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StandFoto extends Model
{
    protected $table = 'stand_fotos';

    protected $fillable = ['stand_id', 'path', 'ordem'];

    public function stand(): BelongsTo
    {
        return $this->belongsTo(Stand::class);
    }

    public function url(): string
    {
        return '/storage/'.$this->path;
    }
}
