<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Midia extends Model
{
    use HasFactory;

    protected $table = 'midias';

    protected $fillable = [
        'tecnologia_id',
        'descricao',
        'link',
        'tipo_midia',
        'ordem',
    ];

    public function tecnologia()
    {
        return $this->belongsTo(Tecnologia::class);
    }
}
