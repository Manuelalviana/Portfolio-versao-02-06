<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Diferencial;
use App\Models\Doenca;
use App\Models\Estagio;
use App\Models\PalavraChave;
use App\Models\Situacao;
use App\Models\Tecnologia;
use App\Models\TipoPropriedade;
use App\Models\Unidade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Models\Icone;
use App\Models\PropriedadeIntelectual;

class AddTecnologiaController extends Controller
{
    public function index(Request $request)
    {
        $idiomaSelecionado = old('idioma', $request->get('idioma', 'pt-br'));

        // Define o ID do idioma
        $idiomaId = match ($idiomaSelecionado) {
            'en' => 2,
            default => 1,
        };

        // Busca unidades
        $unidades = Unidade::orderBy('nome')->get();

        // Busca diferenciais por idioma
        $diferenciais = Diferencial::where('id_idioma', $idiomaId)
            ->orderBy('nome')
            ->get();

        // Busca tipos de propriedade por idioma
        $tipos_propriedade = TipoPropriedade::where('id_idioma', $idiomaId)
            ->orderBy('nome')
            ->get();

         // Busca os ícones (para o select de ícones personalizados)
        $icones = Icone::orderBy('name')->pluck('name')->toArray();

        // Filtra categorias por idioma
        $categorias = Categoria::where('id_idioma', $idiomaId)
            ->orderBy('nome')
            ->get();

        // Busca estágios por idioma e agrupa por categoria para o frontend
        $estagiosPorCategoria = Estagio::where('id_idioma', $idiomaId)
            ->orderBy('id_categoria')
            ->orderBy('nome')
            ->get()
            ->groupBy('id_categoria')
            ->map(function ($grupo) {
                return $grupo->map(function ($estagio) {
                    return [
                        'id' => $estagio->id,
                        'nome' => $estagio->nome,
                    ];
                })->values()->all();
            })
            ->toArray();

        $doencas = Doenca::all();
        $palavras_chave = PalavraChave::all();

        return view('add_tecnologia.index', compact(
            'unidades',
            'categorias',
            'estagiosPorCategoria',
            'doencas',
            'diferenciais',
            'palavras_chave',
            'tipos_propriedade',
            'idiomaSelecionado',
            'idiomaId',
            'icones'
        ));
    }

    public function show(Tecnologia $tecnologia)
    {
        $tecnologia->load(['situacao',
        //'estagio',
        'unidade',
        'inventores',
        'midias']);

        return view('add_tecnologia.show', compact('tecnologia'));
    }

    public function edit(Tecnologia $tecnologia)
    {
        $tecnologia->load([
            'situacao',
           // 'estagio',
            'unidade',
            'categorias',
            'diferenciais',
        ]);

        // Busca os estágios agrupados por id_categoria
       /* $estagiosAgrupados = Estagio::whereNotNull('id_categoria')
            ->orderBy('id_categoria')
            ->orderBy('nome')
            ->get()
            ->groupBy('id_categoria')
            ->map(function ($group) {
                return $group->map(fn($e) => ['id' => $e->id, 'nome' => $e->nome])->values()->all();
            })
            ->toArray();
*/
        $situacoes = Situacao::orderBy('nome')->get();
        $unidades = Unidade::orderBy('nome')->get();
        $idiomaId = $tecnologia->idioma === 'en' ? 2 : 1;
        $categorias = Categoria::where('id_idioma', $idiomaId)
            ->orderBy('nome')
            ->get();
        $estagiosPorCategoria = Estagio::where('id_idioma', $tecnologia->idioma === 'en' ? 2 : 1)
            ->orderBy('id_categoria')
            ->orderBy('nome')
            ->get()
            ->groupBy('id_categoria')
            ->map(function ($group) {
                return $group->map(function ($estagio) {
                    return ['id' => $estagio->id, 'nome' => $estagio->nome];
                })->values()->all();
            })
            ->toArray();
        $diferenciais = Diferencial::where('id_idioma', $tecnologia->idioma === 'en' ? 2 : 1)
            ->orderBy('nome')
            ->get();
        $tipos_propriedade = TipoPropriedade::where('id_idioma', $tecnologia->idioma === 'en' ? 2 : 1)
            ->orderBy('nome')
            ->get();

        if ($situacoes->isEmpty()) {
            $situacoes = collect([
                Situacao::firstOrCreate(['nome' => 'Rascunho']),
                Situacao::firstOrCreate(['nome' => 'Em Análise']),
                Situacao::firstOrCreate(['nome' => 'Em Desenvolvimento']),
                Situacao::firstOrCreate(['nome' => 'Concluída']),
            ]);
        }

        return view('add_tecnologia.edit', compact(
            'tecnologia',
            'situacoes',
            'unidades',
            'categorias',
            'diferenciais',
            'tipos_propriedade',
            'estagiosPorCategoria',
            'idiomaId'
        ));
    }

    public function store(Request $request)
    {
        $idiomaId = $request->input('idioma') === 'en' ? 2 : 1;

        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'idioma' => 'required|in:pt-br,en',
            'unidade_id' => 'nullable|exists:unidades,id',
            'numero_caso' => 'nullable|string|max:255',
            'data_submissao' => 'required|date',
            'resumo_solucao' => 'required|string|max:180',
            'problema' => 'required|string|max:700',
            'solucao' => 'required|string|max:700',
            'o_que_buscam' => 'nullable|string',
            'tipo_tecnologia' => 'nullable|exists:categorias,id',
            'estagio_id' => [
                'nullable',
                Rule::exists('estagios', 'id')->where(function ($query) use ($request, $idiomaId) {
                    return $query->where('id_categoria', $request->input('tipo_tecnologia'))
                                 ->where('id_idioma', $idiomaId);
                }),
            ],
            'imagem_lateral' => 'nullable|image|max:81920',
            'url_video' => 'nullable|url',
            'doencas' => 'nullable|array',
            'doencas.*' => 'exists:doencas,id',
            'palavras_chave' => 'nullable|array',
            'palavras_chave.*' => 'exists:palavras_chave,id',
            'diferenciais' => 'nullable|array',
            'diferenciais.*.id' => 'nullable|exists:diferenciais,id',
            'diferenciais.*.nome' => 'nullable|string|max:40',
            'diferenciais.*.tipo' => 'nullable|string|in:padrao,personalizado',
            'diferenciais.*.icone' => 'nullable|string|max:100',
            'diferenciais.*.descricao' => 'nullable|string|max:200',
            'diferenciais.*.id_idioma' => 'nullable|exists:idiomas,id',
            'outro_diferencial' => 'nullable|string|max:40',
            'possui_pi' => 'required|boolean',
            'tipo_propriedade_id' => 'nullable|array',
            'tipo_propriedade_id.*' => 'nullable|integer|exists:tipo_propriedade,id',
            'pi_descricao' => 'nullable|array',
            'pi_descricao.*' => 'nullable|string|max:2000',
            'pi_link' => 'nullable|array',
            'pi_link.*' => 'nullable|url|max:255',
            'inventores' => 'nullable|array',
            'inventores.*.nome' => 'nullable|string|max:255',
            'inventores.*.coordenador' => 'nullable|boolean',
            'inventores.*.lattes' => 'nullable|url',
            'inventores.*.linkedin' => 'nullable|url',
        ]);

        $numeroCaso = $validated['numero_caso'] ?? 'TEC-' . now()->format('YmdHis');
        $slugBase = Str::slug($validated['titulo']);
        $slug = $slugBase;
        $count = 1;
        while (Tecnologia::where('slug', $slug)->exists()) {
            $slug = $slugBase . '-' . $count++;
        }

        $situacaoId = Situacao::firstWhere('nome', 'Rascunho')?->id
            ?? Situacao::firstOrCreate(['nome' => 'Rascunho'])->id;

        DB::transaction(function () use ($validated, $numeroCaso, $slug, $situacaoId, $request, $idiomaId) {
            $tecnologia = Tecnologia::create([
                'titulo' => $validated['titulo'],
                'idioma' => $validated['idioma'],
                'unidade_id' => $validated['unidade_id'] ?? null,
                'numero_caso' => $numeroCaso,
                'data_submissao' => $validated['data_submissao'],
                'resumo_solucao' => $validated['resumo_solucao'],
                'problema' => $validated['problema'],
                'solucao' => $validated['solucao'],
                'o_que_buscam' => $validated['o_que_buscam'] ?? null,
              //  'estagio_id' => $validated['estagio_id'],
                'situacao_id' => $situacaoId,
                'slug' => $slug,
                'id_user_criador' => Auth::id(),
                'possui_pi' => $validated['possui_pi'],
                'drupal_nid' => (Tecnologia::max('drupal_nid') ?? 0) + 1,
            ]);

            if ($request->hasFile('imagem_lateral')) {
                $path = $request->file('imagem_lateral')->store('tecnologias', 'public');
                $tecnologia->update(['imagem_lateral' => $path]);
            }

            if (!empty($validated['tipo_tecnologia'])) {
                $tecnologia->categorias()->sync([
                    $validated['tipo_tecnologia'] => ['estagio_id' => $validated['estagio_id'] ?? null],
                ]);
            } else {
                $tecnologia->categorias()->detach();
            }

            $tecnologia->update(['estagio_id' => $validated['estagio_id'] ?? null]);

            if (!empty($validated['doencas'])) {
                $tecnologia->doencas()->sync($validated['doencas']);
            }

            if (!empty($validated['palavras_chave'])) {
                $tecnologia->palavras_chave()->sync($validated['palavras_chave']);
            }

            // Substitui o diferencial a seguir por este:
            if (!empty($validated['diferenciais'])) {
                $idsParaSinc = [];

                foreach ($validated['diferenciais'] as $diff) {
                    $tipo = $diff['tipo'] ?? 'padrao';

                    if ($tipo === 'personalizado' && !empty($diff['nome'])) {
                        $modelo = Diferencial::firstOrCreate(
                            ['nome' => trim($diff['nome'])],
                            ['icone' => $diff['icone'] ?? 'help', 'id_idioma' => $diff['id_idioma'] ?? $idiomaId]
                        );
                        $idsParaSinc[] = $modelo->id;

                    } elseif (!empty($diff['id'])) {
                        $idsParaSinc[] = (int) $diff['id'];
                    }
                }

                if (!empty($idsParaSinc)) {
                    $tecnologia->diferenciais()->sync($idsParaSinc);
                }
            }

//   if (!empty($validated['diferenciais'])) { $tecnologia->diferenciais()->sync($validated['diferenciais']);  }

            if (!empty($validated['outro_diferencial'])) {
                $outro = Diferencial::firstOrCreate(
                    ['nome' => $validated['outro_diferencial']],
                    ['id_idioma' => $idiomaId]
                );
                $tecnologia->diferenciais()->syncWithoutDetaching([$outro->id]);
            }

            if (!empty($validated['tipo_propriedade_id'])) {
                foreach ($validated['tipo_propriedade_id'] as $index => $tipoId) {
                    $descricao = $validated['pi_descricao'][$index] ?? null;
                    $link = $validated['pi_link'][$index] ?? null;
                    if ($tipoId) {
                        $tecnologia->propriedades_intelectuais()->create([
                            'tipo_propriedade_id' => $tipoId,
                            'tipo' => TipoPropriedade::find($tipoId)?->nome,
                            'descricao' => $descricao,
                            'link' => $link,
                        ]);
                    }
                }
            }

            if (!empty($request->input('inventores'))) {
                foreach ($request->input('inventores') as $inventorData) {
                    if (!empty($inventorData['nome'])) {
                        $tecnologia->inventores()->create([
                            'nome' => $inventorData['nome'],
                            'coordenador' => filter_var($inventorData['coordenador'] ?? false, FILTER_VALIDATE_BOOLEAN),
                            'lattes' => $inventorData['lattes'] ?? null,
                            'linkedin' => $inventorData['linkedin'] ?? null,
                        ]);
                    }
                }
            }

            if (!empty($validated['url_video'])) {
                $tecnologia->midias()->create([
                    'url_video' => $validated['url_video'],
                ]);
            }
        });

        return redirect()->route('dashboard')->with('success', 'Tecnologia criada com sucesso!');
    }

    public function update(Request $request, Tecnologia $tecnologia)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'unidade_id' => 'nullable|exists:unidades,id',
            'numero_caso' => 'nullable|string|max:255',
            'data_submissao' => 'required|date',
            'resumo_solucao' => 'required|string|max:180',
            'problema' => 'required|string|max:700',
            'solucao' => 'required|string|max:700',
            'o_que_buscam' => 'nullable|string',
           // 'estagio_id' => 'nullable|exists:estagios,id',
            'categorias' => 'nullable|array',
            'categorias.*' => 'exists:categorias,id',
            'doencas' => 'nullable|array',
            'doencas.*' => 'exists:doencas,id',
            'palavras_chave' => 'nullable|array',
            'palavras_chave.*' => 'exists:palavras_chave,id',
            'diferenciais' => 'nullable|array',
            'diferenciais.*.id' => 'nullable|exists:diferenciais,id',
            'diferenciais.*.nome' => 'nullable|string|max:40',
            'diferenciais.*.tipo' => 'nullable|string|in:padrao,personalizado',
            'diferenciais.*.icone' => 'nullable|string|max:100',
            'diferenciais.*.descricao' => 'nullable|string|max:200',
            'diferenciais.*.id_idioma' => 'nullable|exists:idiomas,id',
            'tipo_propriedade_id' => 'nullable|array',
            'tipo_propriedade_id.*' => 'nullable|integer|exists:tipo_propriedade,id',
            'pi_link' => 'nullable|array',
            'pi_link.*' => 'nullable|url|max:255',
            'tipo_tecnologia' => 'nullable|exists:categorias,id',
            'estagio_id' => [
                'nullable',
                Rule::exists('estagios', 'id')->where(function ($query) use ($request) {
                    return $query->when($request->input('tipo_tecnologia'), function ($query, $categoriaId) {
                        return $query->where('id_categoria', $categoriaId);
                    });
                }),
            ],
            'situacao_id' => 'nullable|exists:situacoes,id',
            'possui_pi' => 'required|boolean',
            'imagem_lateral' => 'nullable|image|max:81920',
        ]);

        $tecnologia->update([
            'titulo' => $validated['titulo'],
            'unidade_id' => $validated['unidade_id'] ?? null,
            'numero_caso' => $validated['numero_caso'] ?? $tecnologia->numero_caso,
            'data_submissao' => $validated['data_submissao'],
            'resumo_solucao' => $validated['resumo_solucao'],
            'problema' => $validated['problema'],
            'solucao' => $validated['solucao'],
            'o_que_buscam' => $validated['o_que_buscam'] ?? null,
            'situacao_id' => $validated['situacao_id'] ?? $tecnologia->situacao_id,
            'possui_pi' => $validated['possui_pi'],
        ]);

        // Categoria (tipo_tecnologia)
        if (!empty($validated['tipo_tecnologia'])) {
            $tecnologia->categorias()->sync([
                $validated['tipo_tecnologia'] => ['estagio_id' => $validated['estagio_id'] ?? null],
            ]);
        } else {
            $tecnologia->categorias()->detach();
        }

        $tecnologia->update(['estagio_id' => $validated['estagio_id'] ?? null]);

        // Sincronizar diferenciais
        if (!empty($validated['diferenciais'])) {
            $idsParaSinc = [];
            $idiomaId = $tecnologia->idioma === 'en' ? 2 : 1;

            foreach ($validated['diferenciais'] as $diff) {
                $tipo = $diff['tipo'] ?? 'padrao';

                if ($tipo === 'personalizado' && !empty($diff['nome'])) {
                    $modelo = Diferencial::firstOrCreate(
                        ['nome' => trim($diff['nome'])],
                        ['icone' => $diff['icone'] ?? 'help', 'id_idioma' => $diff['id_idioma'] ?? $idiomaId]
                    );
                    $idsParaSinc[] = $modelo->id;

                } elseif (!empty($diff['id'])) {
                    $idsParaSinc[] = (int) $diff['id'];
                }
            }

            if (!empty($idsParaSinc)) {
                $tecnologia->diferenciais()->sync($idsParaSinc);
            } else {
                $tecnologia->diferenciais()->detach();
            }
        } else {
            $tecnologia->diferenciais()->detach();
        }

        // Upload imagem
        if ($request->hasFile('imagem_lateral')) {
            $path = $request->file('imagem_lateral')->store('tecnologias', 'public');
            $tecnologia->update(['imagem_lateral' => $path]);
        }

        return redirect()
            ->route('add_tecnologia.show', $tecnologia)
            ->with('success', 'Tecnologia atualizada com sucesso!');
    }

    public function destroy(Tecnologia $tecnologia)
    {
        $tecnologia->delete();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Tecnologia excluída com sucesso!');
    }
}
