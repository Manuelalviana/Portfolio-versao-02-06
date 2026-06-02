<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropriedadeIntelectual extends Model
{
    use HasFactory;

    protected $table = 'propriedades_intelectuais';

    protected $fillable = [
        'tecnologia_id',
        'possui_propriedade',
        'tipo_propriedade_id',
        'descricao',
        'link_propriedade',
        'numero_registro',
        'data_registro',
    ];

    public function tecnologia()
    {
        return $this->belongsTo(Tecnologia::class);
    }

    public function tipoPropriedade()
    {
        return $this->belongsTo(TipoPropriedade::class, 'tipo_propriedade_id');
    }
}
