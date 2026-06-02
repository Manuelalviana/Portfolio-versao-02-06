<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventor extends Model
{
    use HasFactory;

    protected $table = 'inventores';

    protected $fillable = [
        'tecnologia_id',
        'nome',
        'linkedin',
        'email',
        'instituicao',
    ];

    public function tecnologia()
    {
        return $this->belongsTo(Tecnologia::class);
    }
}
