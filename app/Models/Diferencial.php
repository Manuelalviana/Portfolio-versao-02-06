<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diferencial extends Model
{
    use HasFactory;
    
    protected $table = 'diferenciais';
    
    protected $fillable = [
        'nome',
        'icone',
        'descricao',
        'id_idioma',
    ];
    
    protected $casts = [
        'icone' => 'string',
    ];
    
    /**
     * Relacionamento com idioma
     */
    public function idioma()
    {
        return $this->belongsTo(Idioma::class, 'id_idioma');
    }
    
    /**
     * Relacionamento com tecnologias (muitos para muitos)
     */
    public function tecnologias()
    {
        return $this->belongsToMany(Tecnologia::class, 'diferencial_tecnologia', 'diferencial_id', 'tecnologia_id');
    }
}