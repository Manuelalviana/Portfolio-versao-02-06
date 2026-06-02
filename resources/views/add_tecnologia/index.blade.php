<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <script src="{{ asset('js/app.js') }}"></script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Criar Tecnologia - Portfólio de Inovação</title>
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
            <h1>Criar Tecnologia</h1> 
            <span class="header-content__subtitle">Cadastro no Portfólio de Inovação</span>
        </div>
        <div class="top-actions">
            <div class="top-links top-links--row">
                <a href="{{ route('dashboard') }}" class="nav-item">← Voltar ao Dashboard</a>
            </div>
        </div>
    </section>

    <div class="dashboard-layout">
        <aside class="sidebar">
            <h3>Menu</h3>
            <a href="{{ route('dashboard') }}" class="menu-item">📊 Dashboard</a>
            <a href="{{ route('add_tecnologia.index') }}" class="menu-item active">➕ Nova tecnologia</a>
            @if(Auth::user()->isAdmin())
                <a href="{{ route('admin.usuarios.index') }}" class="menu-item">👥 Usuários</a>
            @endif
        </aside>

        <main class="main-panel">
            <div class="tecnologia-page__intro">
                <h1>🔬 <b>Formulário de cadastro</b></h1>
                <p>
                    O Sumário Executivo resume, de maneira objetiva, as estratégias de exploração definidas pela equipe do projeto.
                </p>
            </div>

            @if (session('success'))
                <div class="form-alert form-alert--success">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="form-alert form-alert--error">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

           <!-- index.blade.php = CRIAR nova tecnologia -->
            <form action="{{ route('add_tecnologia.store') }}" method="POST" enctype="multipart/form-data" id="tecnologiaForm" class="tecnologia-form">
                @csrf

                <!-- Parte 1 — Informações principais -->
                <details class="form-accordion" open>
                    <summary class="form-accordion__trigger">ℹ️ Parte 1 — Informações principais</summary>
                    <div class="form-accordion__body">
                        <div class="form-grid form-grid--2">
                            <div>
                                <div class="form-field">
                                    <label class="form-label"><b>Idioma </b><span class="form-label__required">*</span></label>
                                    <select name="idioma" id="idioma" class="form-select" required onchange="window.location='?idioma=' + this.value">
                                        <option value="pt-br" @selected($idiomaSelecionado === 'pt-br')>Português (Brasil)</option>
                                        <option value="en" @selected($idiomaSelecionado === 'en')>English</option>
                                    </select>
                                </div>
                                <div class="form-field">
                                    <label class="form-label"><b>Unidade Nit relacionado</b></label>
                                    <select name="unidade_id" class="form-select select2">
                                        <option value="">- Selecione -</option>
                                        @foreach($unidades as $unidade)
                                            <option value="{{ $unidade->id }}" @selected(old('unidade_id') == $unidade->id)>{{ $unidade->nome }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-field">
                                    <label class="form-label"><b>Título </b><span class="form-label__required">*</span></label>
                                    <input type="text" name="titulo" class="form-input" required maxlength="255" value="{{ old('titulo') }}">
                                </div>
                                <div class="form-field">
                                    <label class="form-label"><b>Número de caso Fiocruz</b></label>
                                    <input type="text" name="numero_caso" class="form-input" maxlength="255" placeholder="Preencha se houver" value="{{ old('numero_caso') }}">
                                    <p class="form-hint">Preencha se houver</p>
                                </div>
                            </div>
                            <div>
                                <div class="form-field">
                                    <label class="form-label">Resumo da solução <span class="form-label__required">*</span></label>
                                    <textarea name="resumo_solucao" class="form-textarea" rows="4" maxlength="180" required placeholder="Escreva um breve resumo da solução...">{{ old('resumo_solucao') }}</textarea>
                                    <div class="form-counter" id="resumoCounter">180 caracteres restantes</div>
                                    <p class="form-hint">Apresentado abaixo do título da tecnologia no site do Portfólio.</p>
                                </div>
                            </div>
                        </div>

                        <div class="form-field">
                            <label class="form-label"> <b> Problema </b><span class="form-label__required">*</span></label>
                            <textarea name="problema" class="form-textarea" rows="6" maxlength="700" required>{{ old('problema') }}</textarea>
                            <div class="form-counter" id="problemaCounter">700 caracteres restantes</div>
                            <p class="form-hint">Explique de forma breve o problema que a tecnologia visa resolver.</p>
                        </div>

                        <div class="form-field">
                            <label class="form-label"><b>Solução </b><span class="form-label__required">*</span></label>
                            <textarea name="solucao" class="form-textarea" rows="6" maxlength="700" required>{{ old('solucao') }}</textarea>
                            <div class="form-counter" id="solucaoCounter">700 caracteres restantes</div>
                            <p class="form-hint">Apresente objetivamente a solução proposta.</p>
                        </div>

                        <div class="form-field">
                            <label class="form-label"> <b> O que buscamos</b></label>
                            <textarea name="o_que_buscam" class="form-textarea" rows="6">{{ old('o_que_buscam') }}</textarea>
                            <p class="form-hint">Descreva o que espera da relação a ser estabelecida.</p>
                        </div>

                        <!-- Categoria/Tipo de tecnologia -->
                        <div class="form-field">
                            <label class="form-label"> <b> Categoria </b> </label>
                            <select id="tipo_tecnologia" name="tipo_tecnologia" class="form-select">
                                <option value="">- Selecione -</option>
                                @foreach($categorias as $categoria)
                                    <option value="{{ $categoria->id }}" @selected(old('tipo_tecnologia') == $categoria->id)>
                                        {{ $categoria->nome }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="form-hint">  Selecione a categoria da tecnologia.</p>
                        </div>

                        <div class="form-field">
                            <label class="form-label"> <b> Estágio </b> </label>
                            <select id="estagio_id" name="estagio_id" class="form-select">
                                <option value="">- Selecione a categoria primeiro -</option>
                            </select>
                            <p class="form-hint">Selecione o estágio correspondente à categoria e idioma.</p>
                        </div>

                        <!-- Diferenciais da Tecnologia - Componente com cards -->
                        <!-- add_tecnologia/index.blade.php -->
                        <x-diferenciais-manager 
                            :icones="$icones" 
                            :diferenciaisDisponiveis="$diferenciais"
                            :oldDiferenciais="old('diferenciais', [])"
                            :selectedDiferenciais="[]"
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
                                <p class="form-hint">Tamanho ideal: 375×200px. Máx. 80MB (png, gif, jpg, jpeg).</p>
                            </div>
                            <div class="form-field">
                                <label class="form-label">URL do vídeo (YouTube)</label>
                                <input type="url" name="url_video" class="form-input" placeholder="https://youtu.be/ID-DO-VIDEO" value="{{ old('url_video') }}">
                                <p class="form-hint">Formato: https://youtu.be/ID-DO-VIDEO</p>
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
                                <option value="0" @selected(old('possui_pi', '0') == '0')>Não</option>
                                <option value="1" @selected(old('possui_pi') == '1')>Sim</option>
                            </select>
                        </div>

                        <div id="propriedadesContainer" class="is-hidden">
                            <h3 class="form-section-title">Propriedades intelectuais</h3>
                            <div id="propriedadesList">
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
                                            <textarea name="pi_descricao[]" class="form-textarea" rows="2" placeholder="Descreva a propriedade intelectual..."></textarea>
                                        </div>
                                    </div>
                                </div>
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
                        <input type="date" name="data_submissao" class="form-input" required value="{{ old('data_submissao', now()->toDateString()) }}">
                    </div>
                </div>

                <!-- Botões de ação -->
                <div class="form-actions">
                    <a href="{{ route('dashboard') }}" class="btn-form btn-form--outline">Cancelar</a>
                    <button type="submit" name="action" value="save" class="btn-form btn-form--secondary">Salvar rascunho</button>
                    <button type="submit" name="action" value="submit" class="btn-form btn-form--primary">Submeter tecnologia</button>
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
        
        .form-actions--inline {
            margin-top: 1rem;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @media (max-width: 768px) {
            .form-grid--2 {
                grid-template-columns: 1fr;
            }
        }

        [x-cloak] { display: none !important; }
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
                    if (remaining < 0) counter.style.color = 'red';
                    else counter.style.color = '';
                });
            }
        }

        // Controle de Propriedade Intelectual
        let propriedadeIndex = 1;
        
        function togglePropriedades(select) {
            const container = document.getElementById('propriedadesContainer');
            if (select.value === '1') {
                container.classList.remove('is-hidden');
            } else {
                container.classList.add('is-hidden');
            }
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
                        <textarea name="pi_descricao[]" class="form-textarea" rows="2" placeholder="Descreva a propriedade intelectual..."></textarea>
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
        let inventorIndex = 1;
        
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

        const estagiosPorCategoria = @json($estagiosPorCategoria ?? []);
        const selectedEstagioId = '{{ old('estagio_id') }}';

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
            // Contadores
            setupCounter('resumo_solucao', 'resumoCounter', 180);
            setupCounter('problema', 'problemaCounter', 700);
            setupCounter('solucao', 'solucaoCounter', 700);

            // Propriedade intelectual
            const possuiPi = document.querySelector('#possui_pi');
            if (possuiPi) {
                togglePropriedades(possuiPi);
            }

            const tipoCategoria = document.getElementById('tipo_tecnologia');
            if (tipoCategoria) {
                tipoCategoria.addEventListener('change', atualizarEstagios);
                atualizarEstagios();
            }
        });
    </script>
</body>
</html>
