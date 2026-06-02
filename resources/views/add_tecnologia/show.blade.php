<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ver Tecnologia - {{ $tecnologia->nome }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;600;700;800&family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body class="antialiased tecnologia-page">
    @include('layouts.header')

    <section class="header-banner header-banner--compact">
        <div class="header-content">
            <h1>{{ $tecnologia->titulo ?? $tecnologia->nome }}</h1>
            <span class="header-content__subtitle">Caso {{ $tecnologia->numero_caso }}</span>
        </div>
        <div class="top-actions">
            <div class="top-links top-links--row">
                <a href="{{ route('add_tecnologia.edit', $tecnologia) }}" class="nav-item">✏️ Editar</a>
                <a href="{{ route('dashboard') }}" class="nav-item">← Dashboard</a>
            </div>
        </div>
    </section>

    <div class="dashboard-layout">
        <aside class="sidebar">
            <h3>Menu</h3>
            <a href="{{ route('dashboard') }}" class="menu-item">📊 Dashboard</a>
            <a href="{{ route('add_tecnologia.index') }}" class="menu-item">➕ Nova tecnologia</a>
        </aside>

        <main class="main-panel">
            @if(session('success'))
                <div class="form-alert form-alert--success">{{ session('success') }}</div>
            @endif

            <div class="crud-card">
                <div class="crud-card__header">
                    <div>
                        <h2 class="crud-card__title">Detalhes da tecnologia</h2>
                        <p class="crud-card__desc">
                            Cadastrada em {{ optional($tecnologia->data_submissao)->format('d/m/Y') }}
                        </p>
                    </div>
                </div>

                <div class="tecnologia-meta">
                    <div class="tecnologia-meta__item">
                        <span class="tecnologia-meta__label">Situação</span>
                        <span>{{ $tecnologia->situacao?->nome ?? '—' }}</span>
                    </div>
                    <div class="tecnologia-meta__item">
                        <span class="tecnologia-meta__label">Estágio</span>
                        <span>{{ $tecnologia->estagio?->nome ?? '—' }}</span>
                    </div>
                    <div class="tecnologia-meta__item">
                        <span class="tecnologia-meta__label">Possui PI</span>
                        <span>{{ $tecnologia->possui_pi ? 'Sim' : 'Não' }}</span>
                    </div>
                    <div class="tecnologia-meta__item">
                        <span class="tecnologia-meta__label">Idioma</span>
                        <span>{{ $tecnologia->idioma === 'en' ? 'English' : 'Português (Brasil)' }}</span>
                    </div>
                </div>

                <div class="tecnologia-block">
                    <h3 class="form-section-title">Resumo da solução</h3>
                    <p>{{ $tecnologia->resumo_solucao }}</p>
                </div>

                <div class="tecnologia-block">
                    <h3 class="form-section-title">Problema</h3>
                    <p>{{ $tecnologia->problema }}</p>
                </div>

                <div class="tecnologia-block">
                    <h3 class="form-section-title">Solução</h3>
                    <p>{{ $tecnologia->solucao }}</p>
                </div>

                @if($tecnologia->o_que_buscam)
                <div class="tecnologia-block">
                    <h3 class="form-section-title">O que buscamos</h3>
                    <p>{{ $tecnologia->o_que_buscam }}</p>
                </div>
                @endif

                @if($tecnologia->imagem_lateral)
                <div class="tecnologia-block">
                    <a href="{{ asset('storage/' . $tecnologia->imagem_lateral) }}" download class="tecnologia-image-link">
                     <img src="{{ asset('storage/' . $tecnologia->imagem_lateral) }}" alt="Imagem da tecnologia" class="tecnologia-image">
                   </a>
                </div>
                @else
                    <div class="tecnologia-block">
                        <p class="text-muted">Nenhuma imagem cadastrada.</p>
                    </div>
                @endif

                @if($tecnologia->inventores->isNotEmpty())
                <div class="tecnologia-block">
                    <h3 class="form-section-title">Inventores</h3>
                    <ul class="tecnologia-list">
                        @foreach($tecnologia->inventores as $inventor)
                            <li>{{ $inventor->nome }}{{ $inventor->coordenador ? ' (coordenador)' : '' }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="form-actions form-actions--inline">
                    <a href="{{ route('add_tecnologia.edit', $tecnologia) }}" class="btn-form btn-form--primary">Editar</a>
                    <a href="{{ route('dashboard') }}" class="btn-form btn-form--outline">Voltar</a>
                </div>
            </div>
        </main>
    </div>

    @include('layouts.footer')
</body>
</html>
