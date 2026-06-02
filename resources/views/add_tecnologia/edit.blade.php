<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Tecnologia - {{ $tecnologia->titulo }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;600;700;800&family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,400,0,0&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="antialiased tecnologia-page">
    @include('layouts.header')

    <section class="header-banner header-banner--compact">
        <div class="header-content">
            <h1>Editar tecnologia</h1>
            <span class="header-content__subtitle">{{ $tecnologia->numero_caso }}</span>
        </div>
        <div class="top-actions">
            <div class="top-links top-links--row">
                <a href="{{ route('add_tecnologia.show', $tecnologia) }}" class="nav-item">Ver detalhes</a>
                <a href="{{ route('dashboard') }}" class="nav-item">← Dashboard</a>
            </div>
        </div>
    </section>

    <div class="dashboard-layout">
        <aside class="sidebar">
            <h3>Menu</h3>
            <a href="{{ route('dashboard') }}" class="menu-item">📊 Dashboard</a>
            <a href="{{ route('add_tecnologia.index') }}" class="menu-item">➕ Nova tecnologia</a>
            @if(Auth::user()->isAdmin())
                <a href="{{ route('admin.usuarios.index') }}" class="menu-item">👥 Usuários</a>
            @endif
        </aside>

        <main class="main-panel">
            @if ($errors->any())
                <div class="form-alert form-alert--error">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('add_tecnologia.update', $tecnologia) }}" method="POST" enctype="multipart/form-data" id="tecnologiaForm" class="tecnologia-form">
                @csrf
                @method('PUT')

                <!-- Parte 1 — Informações principais -->
                <details class="form-accordion" open>
                    <summary class="form-accordion__trigger">ℹ️ Parte 1 — Informações principais</summary>
                    <div class="form-accordion__body">
                        <div class="form-grid form-grid--2">
                            <div>
                                <div class="form-field">
                                    <label class="form-label">Idioma <span class="form-label__required">*</span></label>
                                    <select name="idioma" id="idioma" class="form-select" required>
                                        <option value="pt-br" @selected(old('idioma', $tecnologia->idioma) === 'pt-br')>Português (Brasil)</option>
                                        <option value="en" @selected(old('idioma', $tecnologia->idioma) === 'en')>English</option>
                                    </select>
                                </div>
                                <div class="form-field">
                                    <label class="form-label">Unidade Nit relacionado</label>
                                    <select name="unidade_id" class="form-select select2">
                                        <option value="">- Selecione -</option>
                                        @foreach($unidades as $unidade)
                                            <option value="{{ $unidade->id }}"
                                                @selected(old('unidade_id', $tecnologia->unidade_id) == $unidade->id)>
                                                {{ $unidade->nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-field">
                                    <label class="form-label">Título <span class="form-label__required">*</span></label>
                                    <input type="text" name="titulo" class="form-input" required maxlength="255"
                                           value="{{ old('titulo', $tecnologia->titulo) }}">
                                </div>
                                <div class="form-field">
                                    <label class="form-label">Número de caso Fiocruz</label>
                                    <input type="text" name="numero_caso" class="form-input" maxlength="255"
                                           value="{{ old('numero_caso', $tecnologia->numero_caso) }}">
                                </div>
                            </div>
                            <div>
                                <div class="form-field">
                                    <label class="form-label">Resumo da solução <span class="form-label__required">*</span></label>
                                    <textarea name="resumo_solucao" class="form-textarea" rows="4" maxlength="180" required>{{ old('resumo_solucao', $tecnologia->resumo_solucao) }}</textarea>
                                    <div class="form-counter" id="resumoCounter">180 caracteres restantes</div>
                                </div>
                            </div>
                        </div>

                        <div class="form-field">
                            <label class="form-label">Problema <span class="form-label__required">*</span></label>
                            <textarea name="problema" class="form-textarea" rows="6" maxlength="700" required>{{ old('problema', $tecnologia->problema) }}</textarea>
                            <div class="form-counter" id="problemaCounter">700 caracteres restantes</div>
                        </div>

                        <div class="form-field">
                            <label class="form-label">Solução <span class="form-label__required">*</span></label>
                            <textarea name="solucao" class="form-textarea" rows="6" maxlength="700" required>{{ old('solucao', $tecnologia->solucao) }}</textarea>
                            <div class="form-counter" id="solucaoCounter">700 caracteres restantes</div>
                        </div>

                        <div class="form-field">
                            <label class="form-label">O que buscamos</label>
                            <textarea name="o_que_buscam" class="form-textarea" rows="6">{{ old('o_que_buscam', $tecnologia->o_que_buscam) }}</textarea>
                        </div>

                        <!-- Categoria/Tipo de tecnologia -->
                        <div class="form-field">
                            <label class="form-label">Categoria/Tipo de tecnologia</label>
                            <select id="tipo_tecnologia" name="tipo_tecnologia" class="form-select">
                                <option value="">- Selecione -</option>
                                @foreach($categorias as $categoria)
                                    <option value="{{ $categoria->id }}"
                                        @selected(old('tipo_tecnologia', $tecnologia->categorias->first()?->id) == $categoria->id)>
                                        {{ $categoria->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-field">
                            <label class="form-label">Estágio</label>
                            <select id="estagio_id" name="estagio_id" class="form-select">
                                <option value="">- Selecione a categoria primeiro -</option>
                            </select>
                        </div>

                        <!-- Diferenciais da Tecnologia -->
                        <x-diferenciais-manager
                            :icones="$icones ?? []"
                            :diferenciaisDisponiveis="$diferenciais ?? []"
                            :oldDiferenciais="old('diferenciais', [])"
                            :selectedDiferenciais="$tecnologia->diferenciais->toArray()"
                        />
                    </div>
                </details>

                <!-- Parte 2 — Imagens e vídeo -->
                <details class="form-accordion">
                    <summary class="form-accordion__trigger">🖼️ Parte 2 — Imagens e vídeo</summary>
                    <div class="form-accordion__body">
                        <div class="form-grid form-grid--2">
                            <div class="form-field">
                                <label class="form-label">Imagem lateral</label>
                                <input type="file" name="imagem_lateral" class="form-input" accept="image/*">
                                @if($tecnologia->imagem_lateral)
                                    <p class="form-hint">Imagem atual cadastrada.</p>
                                @endif
                            </div>
                            <div class="form-field">
                                <label class="form-label">URL do vídeo (YouTube)</label>
                                <input type="url" name="url_video" class="form-input" placeholder="https://youtu.be/ID-DO-VIDEO"
                                       value="{{ old('url_video', $tecnologia->url_video) }}">
                            </div>
                        </div>
                    </div>
                </details>

                <!-- Parte 3 — Propriedade Intelectual e Inventores -->
                <details class="form-accordion">
                    <summary class="form-accordion__trigger">⚙️ Parte 3 — Propriedade Intelectual e Inventores</summary>
                    <div class="form-accordion__body">
                        <div class="form-field">
                            <label class="form-label">Possui propriedade intelectual? <span class="form-label__required">*</span></label>
                            <select name="possui_pi" id="possui_pi" class="form-select" required onchange="togglePropriedades(this)">
                                <option value="0" @selected(!old('possui_pi', $tecnologia->possui_pi))>Não</option>
                                <option value="1" @selected(old('possui_pi', $tecnologia->possui_pi))>Sim</option>
                            </select>
                        </div>

                        <div id="propriedadesContainer" class="{{ old('possui_pi', $tecnologia->possui_pi) ? '' : 'is-hidden' }}">
                            <h3 class="form-section-title">Propriedades intelectuais</h3>
                            <div id="propriedadesList">
                                @if($tecnologia->propriedades && $tecnologia->propriedades->count() > 0)
                                    @foreach($tecnologia->propriedades as $index => $propriedade)
                                        <div class="propriedade-item" style="{{ $index > 0 ? 'margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e2e8f0;' : '' }}">
                                            <div class="form-grid form-grid--2">
                                                <div class="form-field">
                                                    <label class="form-label">Tipo de propriedade</label>
                                                    <select name="tipo_propriedade_id[]" class="form-select">
                                                        <option value="">- Selecione -</option>
                                                        @foreach($tipos_propriedade as $tipo)
                                                            <option value="{{ $tipo->id }}" @selected($propriedade->tipo_propriedade_id == $tipo->id)>
                                                                {{ $tipo->nome }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-field">
                                                    <label class="form-label">Descrição</label>
                                                    <textarea name="pi_descricao[]" class="form-textarea" rows="2">{{ $propriedade->descricao }}</textarea>
                                                </div>
                                                @if($index > 0)
                                                <div class="form-field" style="grid-column: span 2;">
                                                    <button type="button" class="btn-form btn-form--danger" onclick="this.closest('.propriedade-item').remove()">
                                                        <span class="material-symbols-outlined">delete</span>
                                                        Remover propriedade
                                                    </button>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="propriedade-item">
                                        <div class="form-grid form-grid--2">
                                            <div class="form-field">
                                                <label class="form-label">Tipo de propriedade</label>
                                                <select name="tipo_propriedade_id[]" class="form-select">
                                                    <option value="">- Selecione -</option>
                                                    @foreach($tipos_propriedade as $tipo)
                                                        <option value="{{ $tipo->id }}">{{ $tipo->nome }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-field">
                                                <label class="form-label">Descrição</label>
                                                <textarea name="pi_descricao[]" class="form-textarea" rows="2"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="form-actions form-actions--inline" style="margin-top: 1rem;">
                                <button type="button" class="btn-form btn-form--ghost" onclick="adicionarPropriedadeIntelectual()">
                                    <span class="material-symbols-outlined">add</span>
                                    Adicionar nova propriedade intelectual
                                </button>
                            </div>
                        </div>

                        <h3 class="form-section-title">👥 Inventores</h3>
                        <div id="inventoresContainer">
                            @if($tecnologia->inventores && $tecnologia->inventores->count() > 0)
                                @foreach($tecnologia->inventores as $index => $inventor)
                                    <div class="inventor-item" style="{{ $index > 0 ? 'margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e2e8f0;' : '' }}">
                                        <div class="form-grid form-grid--2">
                                            <div class="form-field">
                                                <label class="form-label">Nome do inventor</label>
                                                <input type="text" name="inventores[{{ $index }}][nome]" class="form-input" value="{{ $inventor->nome }}">
                                            </div>
                                            <div class="form-field">
                                                <label class="form-label">Coordenador?</label>
                                                <select name="inventores[{{ $index }}][coordenador]" class="form-select">
                                                    <option value="0" @selected(!$inventor->coordenador)>Não</option>
                                                    <option value="1" @selected($inventor->coordenador)>Sim</option>
                                                </select>
                                            </div>
                                            <div class="form-field">
                                                <label class="form-label">Link Lattes</label>
                                                <input type="url" name="inventores[{{ $index }}][lattes]" class="form-input" value="{{ $inventor->lattes }}">
                                            </div>
                                            <div class="form-field">
                                                <label class="form-label">LinkedIn</label>
                                                <input type="url" name="inventores[{{ $index }}][linkedin]" class="form-input" value="{{ $inventor->linkedin }}">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="inventor-item">
                                    <div class="form-grid form-grid--2">
                                        <div class="form-field">
                                            <label class="form-label">Nome do inventor</label>
                                            <input type="text" name="inventores[0][nome]" class="form-input" placeholder="Nome completo">
                                        </div>
                                        <div class="form-field">
                                            <label class="form-label">Coordenador?</label>
                                            <select name="inventores[0][coordenador]" class="form-select">
                                                <option value="0">Não</option>
                                                <option value="1">Sim</option>
                                            </select>
                                        </div>
                                        <div class="form-field">
                                            <label class="form-label">Link Lattes</label>
                                            <input type="url" name="inventores[0][lattes]" class="form-input" placeholder="http://lattes.cnpq.br/...">
                                        </div>
                                        <div class="form-field">
                                            <label class="form-label">LinkedIn</label>
                                            <input type="url" name="inventores[0][linkedin]" class="form-input" placeholder="https://linkedin.com/in/...">
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="form-actions form-actions--inline" style="margin-top: 1rem;">
                            <button type="button" class="btn-form btn-form--ghost" onclick="adicionarInventor()">
                                <span class="material-symbols-outlined">add</span>
                                Adicionar inventor
                            </button>
                        </div>
                    </div>
                </details>

                <!-- Data de submissão -->
                <div class="form-block">
                    <div class="form-field">
                        <label class="form-label">Data de submissão <span class="form-label__required">*</span></label>
                        <input type="date" name="data_submissao" class="form-input" required
                               value="{{ old('data_submissao', optional($tecnologia->data_submissao)->format('Y-m-d')) }}">
                    </div>
                </div>

                <!-- Botões de ação -->
                <div class="form-actions">
                    <a href="{{ route('add_tecnologia.show', $tecnologia) }}" class="btn-form btn-form--outline">Cancelar</a>
                    <button type="submit" name="action" value="save" class="btn-form btn-form--secondary">Salvar rascunho</button>
                    <button type="submit" name="action" value="submit" class="btn-form btn-form--primary">Atualizar tecnologia</button>
                </div>
            </form>
        </main>
    </div>

    @include('layouts.footer')

    <style>
        .is-hidden {
            display: none;
        }
        .propriedade-item,
        .inventor-item {
            animation: fadeIn 0.3s ease-in;
        }
        .btn-form--danger {
            background: #fee2e2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
        }
        .btn-form--danger:hover {
            background: #fecaca;
            border-color: #f87171;
        }
        .btn-form--ghost {
            background: transparent;
            border: 1px solid #cbd5e1;
            color: #475569;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
        }
        .btn-form--ghost:hover {
            background: #f1f5f9;
            border-color: #94a3b8;
        }
        .btn-form--secondary {
            background: #64748b;
            border: 1px solid #64748b;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
        }
        .btn-form--secondary:hover {
            background: #475569;
        }
        .form-actions--inline {
            margin-top: 1rem;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

    <script>
        // Contadores de caracteres
        function setupCounter(textareaId, counterId, max) {
            const textarea = document.getElementById(textareaId);
            const counter = document.getElementById(counterId);
            if (textarea && counter) {
                textarea.addEventListener('input', function() {
                    const remaining = max - this.value.length;
                    counter.textContent = remaining + ' caracteres restantes';
                    counter.style.color = remaining < 0 ? 'red' : '';
                });
                textarea.dispatchEvent(new Event('input'));
            }
        }

        // Controle de Propriedade Intelectual
        let propriedadeIndex = {{ $tecnologia->propriedades ? $tecnologia->propriedades->count() : 1 }};

        function togglePropriedades(select) {
            const container = document.getElementById('propriedadesContainer');
            container.classList.toggle('is-hidden', select.value !== '1');
        }

        function adicionarPropriedadeIntelectual() {
            const container = document.getElementById('propriedadesList');
            const newItem = document.createElement('div');
            newItem.className = 'propriedade-item';
            newItem.style.marginTop = '1rem';
            newItem.style.paddingTop = '1rem';
            newItem.style.borderTop = '1px solid #e2e8f0';
            newItem.innerHTML = `
                <div class="form-grid form-grid--2">
                    <div class="form-field">
                        <label class="form-label">Tipo de propriedade</label>
                        <select name="tipo_propriedade_id[]" class="form-select">
                            <option value="">- Selecione -</option>
                            @foreach($tipos_propriedade as $tipo)
                                <option value="{{ $tipo->id }}">{{ $tipo->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-field">
                        <label class="form-label">Descrição</label>
                        <textarea name="pi_descricao[]" class="form-textarea" rows="2"></textarea>
                    </div>
                    <div class="form-field" style="grid-column: span 2;">
                        <button type="button" class="btn-form btn-form--danger" onclick="this.closest('.propriedade-item').remove()">
                            <span class="material-symbols-outlined">delete</span>
                            Remover propriedade
                        </button>
                    </div>
                </div>
            `;
            container.appendChild(newItem);
            propriedadeIndex++;
        }

        // Controle de Inventores
        let inventorIndex = {{ $tecnologia->inventores ? $tecnologia->inventores->count() : 1 }};

        function adicionarInventor() {
            const container = document.getElementById('inventoresContainer');
            const newItem = document.createElement('div');
            newItem.className = 'inventor-item';
            newItem.style.marginTop = '1rem';
            newItem.style.paddingTop = '1rem';
            newItem.style.borderTop = '1px solid #e2e8f0';
            newItem.innerHTML = `
                <div class="form-grid form-grid--2">
                    <div class="form-field">
                        <label class="form-label">Nome do inventor</label>
                        <input type="text" name="inventores[${inventorIndex}][nome]" class="form-input" placeholder="Nome completo">
                    </div>
                    <div class="form-field">
                        <label class="form-label">Coordenador?</label>
                        <select name="inventores[${inventorIndex}][coordenador]" class="form-select">
                            <option value="0">Não</option>
                            <option value="1">Sim</option>
                        </select>
                    </div>
                    <div class="form-field">
                        <label class="form-label">Link Lattes</label>
                        <input type="url" name="inventores[${inventorIndex}][lattes]" class="form-input" placeholder="http://lattes.cnpq.br/...">
                    </div>
                    <div class="form-field">
                        <label class="form-label">LinkedIn</label>
                        <input type="url" name="inventores[${inventorIndex}][linkedin]" class="form-input" placeholder="https://linkedin.com/in/...">
                    </div>
                    <div class="form-field" style="grid-column: span 4;">
                        <button type="button" class="btn-form btn-form--danger" onclick="this.closest('.inventor-item').remove()">
                            <span class="material-symbols-outlined">delete</span>
                            Remover inventor
                        </button>
                    </div>
                </div>
            `;
            container.appendChild(newItem);
            inventorIndex++;
        }

        const estagiosPorCategoria = @json($estagiosPorCategoria);
        const selectedEstagioId = '{{ old('estagio_id', $tecnologia->categorias->first()?->pivot->estagio_id ?? $tecnologia->estagio_id) }}';

        function atualizarEstagios() {
            const categoriaId = document.getElementById('tipo_tecnologia')?.value;
            const estagioSelect = document.getElementById('estagio_id');

            if (!estagioSelect) {
                return;
            }

            estagioSelect.innerHTML = '<option value="">- Selecione -</option>';

            if (!categoriaId || !estagiosPorCategoria[categoriaId]) {
                estagioSelect.innerHTML = '<option value="">- Selecione a categoria primeiro -</option>';
                return;
            }

            estagiosPorCategoria[categoriaId].forEach(function(estagio) {
                const option = document.createElement('option');
                option.value = estagio.id;
                option.textContent = estagio.nome;
                if (selectedEstagioId === String(estagio.id)) {
                    option.selected = true;
                }
                estagioSelect.appendChild(option);
            });
        }

        // Inicialização
        document.addEventListener('DOMContentLoaded', function() {
            setupCounter('resumo_solucao', 'resumoCounter', 180);
            setupCounter('problema', 'problemaCounter', 700);
            setupCounter('solucao', 'solucaoCounter', 700);

            const possuiPi = document.querySelector('#possui_pi');
            if (possuiPi) togglePropriedades(possuiPi);

            const tipoCategoria = document.getElementById('tipo_tecnologia');
            if (tipoCategoria) {
                tipoCategoria.addEventListener('change', atualizarEstagios);
                atualizarEstagios();
            }

            // Select2
            $('.select2').select2({
                placeholder: '- Selecione -',
                allowClear: true
            });
        });
    </script>
</body>
</html>
