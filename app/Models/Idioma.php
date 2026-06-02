<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Idioma extends Model
{
    use HasFactory;

    protected $table = 'idiomas';

    protected $fillable = [
        'nome',
        'descricao',
    ];

    // Um idioma possui várias categorias
    public function categorias(): HasMany
    {
        return $this->hasMany(Categoria::class, 'id_idioma');
    }

    // Um idioma possui vários estágios
    public function estagios(): HasMany
    {
        return $this->hasMany(Estagio::class, 'id_idioma');
    }
}