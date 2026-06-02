<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categoria extends Model
{
    use HasFactory;
    
    protected $table = 'categorias';
    
    protected $fillable = [
        'nome',
        'descricao',
        'id_idioma',
    ];
    
    /**
     * Uma categoria tem muitos estágios
     */
    public function estagios(): HasMany
    {
        return $this->hasMany(Estagio::class, 'id_categoria');
    }

    /**
     * Escopo para filtrar por idioma
     */
    public function scopePorIdioma($query, $idioma = 'pt-br')
    {
        return $query->where('id_idioma', $idioma === 'pt-br' ? 1 : 2);
    }
}