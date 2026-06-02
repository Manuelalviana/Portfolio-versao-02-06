<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estagio extends Model
{
    use HasFactory;

    protected $table = 'estagios';

    protected $fillable = [
        'nome',
        'etapa',
        'descricao',
        'id_categoria',
        'id_idioma',
    ];

    // Um estágio pode ter várias tecnologias
    public function tecnologias()
    {
        return $this->hasMany(Tecnologia::class);
    }

    // Estágio pertence a uma categoria
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria');
    }

    // Estágio pertence a um idioma
    public function idioma()
    {
        return $this->belongsTo(Idioma::class, 'id_idioma');
    }
}