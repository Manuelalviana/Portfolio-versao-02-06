<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use App\Models\User;
use App\Models\Unidade;
use App\Models\Situacao;
use App\Models\Estagio;
use App\Models\Midia;
use App\Models\PropriedadeIntelectual;
use App\Models\Inventor;
use App\Models\Anotacao;
use App\Models\PalavraChave;
use App\Models\Doenca;
use App\Models\Categoria;
use App\Models\Diferencial;

class Tecnologia extends Model
{
    use HasFactory;

    protected $table = 'tecnologias';

    protected $fillable = [
        'titulo',
        'nome',
        'idioma',
        'unidade_id',
        'numero_caso',
        'data_submissao',
        'resumo_solucao',
        'problema',
        'solucao',
        'o_que_buscam',
        'estagio_id',
        'situacao_id',
        'id_status',
        'id_user_criador',
        'possui_pi',
        'imagem_lateral',
        'slug',
        'drupal_nid',
    ];

    protected $casts = [
        'data_submissao' => 'date',
        'possui_pi' => 'boolean',
    ];

    // ============ RELACIONAMENTOS ============

    public function responsavel()
    {
        return $this->belongsTo(User::class, 'id_user_criador');
    }

    public function unidade()
    {
        return $this->belongsTo(Unidade::class, 'unidade_id');
    }

    public function situacao()
    {
        return $this->belongsTo(Situacao::class, 'situacao_id');
    }

    public function status()
    {
        return $this->belongsTo(Situacao::class, 'id_status');
    }

    public function estagio()
    {
        return $this->belongsTo(Estagio::class, 'estagio_id');
    }

    public function midias()
    {
        return $this->hasMany(Midia::class);
    }

    public function propriedadeIntelectual()
    {
        return $this->hasOne(PropriedadeIntelectual::class);
    }

    public function inventores()
    {
        return $this->hasMany(Inventor::class);
    }

    public function anotacao()
    {
        return $this->hasOne(Anotacao::class);
    }

    // ============ RELACIONAMENTOS MANY-TO-MANY ============

    public function palavrasChave(): BelongsToMany
    {
        return $this->belongsToMany(PalavraChave::class, 'palavra_chave_tecnologia');
    }

    public function doencas(): BelongsToMany
    {
        return $this->belongsToMany(Doenca::class, 'doenca_tecnologia');
    }

    public function categorias(): BelongsToMany
    {
        return $this->belongsToMany(Categoria::class, 'categoria_tecnologia', 'tecnologia_id', 'categoria_id')
                    ->withPivot('estagio_id')
                    ->withTimestamps();
    }

    public function diferenciais(): BelongsToMany
    {
        return $this->belongsToMany(Diferencial::class, 'diferencial_tecnologia');
    }
}
