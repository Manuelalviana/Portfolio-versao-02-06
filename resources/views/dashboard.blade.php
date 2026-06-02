<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>    
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - Portfólio de Inovação</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800&family=Raleway:wght@400;600;700;800&family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body class="antialiased">
    @include('layouts.header')

    <section class="header-banner">
        <div class="header-content">
            <h1>Dashboard</h1>
            <span class="header-content__subtitle">Área Administrativa Fiocruz</span>
        </div>

        <div class="top-actions">
            <div class="welcome">Olá, {{ Auth::user()->name }}!</div>
            <div class="top-links top-links--row">
                <a href="{{ route('user') }}" class="nav-item">🏡 Voltar ao Home</a>
                @if(Auth::user()->isAdmin() || Auth::user()->email === 'manuela.viana@fiocruz.br')
                    <a href="{{ route('admin.usuarios.index') }}" class="nav-item">👥 Usuários ({{ \App\Models\User::count() }})</a>
                    <a href="{{ route('admin.atividades.index') }}" class="nav-item">📋 Monitoramento</a>
                @endif
                <form method="POST" action="{{ route('logout') }}" class="is-hidden" id="logout-form-dashboard">
                    @csrf
                </form>
                <button type="submit" form="logout-form-dashboard" class="nav-item nav-item--plain">🚪 Sair</button>
            </div>
        </div>
    </section>

    <div class="dashboard-layout">
        <aside class="sidebar">
            <h3>Menu Administrativo</h3>
            <a href="{{ route('dashboard') }}" class="menu-item active">📊 Visão Geral</a>
            <a href="#lista-tecnologias" class="menu-item">💾 Tecnologias cadastradas</a>
            <a href="{{ route('add_tecnologia.index') }}" class="menu-item">➕ Nova tecnologia</a>
            @if(Auth::user()->isAdmin() || Auth::user()->email === 'manuela.viana@fiocruz.br')
                <a href="{{ route('admin.atividades.index') }}" class="menu-item">📋 Monitoramento</a>
                <a href="{{ route('admin.usuarios.index') }}" class="menu-item">👥 Gerenciar Usuários</a>
            @endif
        </aside>

        <main class="main-panel">
            @if(session('success'))
                <div class="dashboard-card dashboard-card--success">
                    <h3>✅ {{ session('success') }}</h3>
                </div>
            @endif

            <div class="dashboard-card">
                <h3 class="dashboard-card__title">📊 Visão Geral</h3>
                <div class="dashboard-stats">
                    <div>
                        <p class="dashboard-stat__label">Total Usuários</p>
                        <p class="dashboard-stat__value dashboard-stat__value--blue">{{ \App\Models\User::count() }}</p>
                    </div>
                    <div>
                        <p class="dashboard-stat__label">Logins Hoje</p>
                        <p class="dashboard-stat__value dashboard-stat__value--green">{{ \App\Models\User::whereDate('updated_at', today())->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="dashboard-card dashboard-card--center">
                <div class="dashboard-card__icon"></div>
                <h3 class="dashboard-card__title">
                    @if(Auth::user()->isAdmin() || Auth::user()->email === 'manuela.viana@fiocruz.br')
                        SUPER ADMINISTRADOR
                    @else
                        Usuário Autorizado
                    @endif
                </h3>
                <p class="dashboard-card__email">{{ Auth::user()->email }}</p>
                @if(Auth::user()->isAdmin() || Auth::user()->email === 'manuela.viana@fiocruz.br')
                    <span class="badge-admin">ACESSO TOTAL ✅</span>
                @endif
            </div>

            <div class="crud-card" id="lista-tecnologias">
                <div class="crud-card__header">
                    <div>
                        <h3 class="crud-card__title">Tecnologias cadastradas</h3>
                        <p class="crud-card__desc">Lista de tecnologias registradas no sistema.</p>
                    </div>
                    <a href="{{ route('add_tecnologia.index') }}" class="action-btn action-view">➕ Nova tecnologia</a>
                </div>

                <div class="table-scroll">
                    <table class="crud-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nome da Tecnologia</th>
                                <th>Número do Caso</th>
                                <th>Situação</th>
                                <th>Data</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tecnologias as $tecnologia)
                                <tr>
                                    <td>{{ $tecnologia->id }}</td>
                                    <td>{{ $tecnologia->titulo ?? $tecnologia->nome ?? '—' }}</td>
                                    <td>{{ $tecnologia->numero_caso }}</td>
                                    <td>{{ $tecnologia->situacao?->nome ?? '—' }}</td>
                                    <td>{{ optional($tecnologia->data_submissao)->format('d/m/Y') }}</td>
                                    <td class="action-group">
                                        <a href="{{ route('add_tecnologia.show', $tecnologia) }}" class="action-btn action-view">Ver</a>
                                        <a href="{{ route('add_tecnologia.edit', $tecnologia) }}" class="action-btn action-edit">Editar</a>
                                        <form action="{{ route('add_tecnologia.destroy', $tecnologia) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir essa tecnologia?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-btn action-delete">Excluir</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="crud-table__empty">Nenhuma tecnologia cadastrada ainda.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    @include('layouts.footer')
</body>
</html>
