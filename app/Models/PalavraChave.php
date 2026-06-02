<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PalavraChave extends Model
{
    use HasFactory;

    protected $table = 'palavras_chave';

    protected $fillable = [
        'nome',
    ];

    public function tecnologias(): BelongsToMany
    {
        return $this->belongsToMany(Tecnologia::class, 'palavra_chave_tecnologia');
    }
}
