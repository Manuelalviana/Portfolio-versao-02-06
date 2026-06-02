<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Usuário #{{ $user->id }} - Portfólio de Inovação</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;600;700;800&family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body class="antialiased">
    @include('layouts.header')

    <section class="header-banner header-banner--compact">
        <div class="header-content">
            <h1>{{ $user->name ?? $user->nome ?? 'Usuário' }}</h1>
            <span class="header-content__subtitle">{{ $user->email }}</span>
        </div>
        <div class="top-actions">
            <div class="top-links top-links--row">
                <a href="{{ route('admin.usuarios.index') }}" class="nav-item">← Lista de usuários</a>
                <a href="{{ route('dashboard') }}" class="nav-item">Dashboard</a>
            </div>
        </div>
    </section>

    <div class="dashboard-layout">
        <aside class="sidebar">
            <h3>Menu Administrativo</h3>
            <a href="{{ route('dashboard') }}" class="menu-item">📊 Dashboard</a>
            <a href="{{ route('admin.usuarios.index') }}" class="menu-item active">👥 Usuários</a>
        </aside>

        <main class="main-panel">
            @if(session('success'))
                <div class="form-alert form-alert--success">{{ session('success') }}</div>
            @endif

            <div class="crud-card">
                <h2 class="crud-card__title">Detalhes do usuário</h2>

                <div class="tecnologia-meta">
                    <div class="tecnologia-meta__item">
                        <span class="tecnologia-meta__label">ID</span>
                        <span>{{ $user->id }}</span>
                    </div>
                    <div class="tecnologia-meta__item">
                        <span class="tecnologia-meta__label">Nome</span>
                        <span>{{ $user->name ?? $user->nome ?? '—' }}</span>
                    </div>
                    <div class="tecnologia-meta__item">
                        <span class="tecnologia-meta__label">Administrador</span>
                        <span>{{ $user->isAdmin() ? 'Sim' : 'Não' }}</span>
                    </div>
                    <div class="tecnologia-meta__item">
                        <span class="tecnologia-meta__label">Criado em</span>
                        <span>{{ $user->created_at?->format('d/m/Y H:i') ?? '—' }}</span>
                    </div>
                </div>

                <div class="form-actions form-actions--inline">
                    <a href="{{ route('admin.usuarios.atividades', $user) }}" class="btn-form btn-form--secondary">Ver atividades</a>
                    <a href="{{ route('admin.usuarios.index') }}" class="btn-form btn-form--outline">Voltar</a>

                    @if(Auth::id() !== $user->id)
                        <form action="{{ route('admin.usuarios.toggleAdmin', $user) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn-form btn-form--secondary">
                                {{ $user->isAdmin() ? 'Remover admin' : 'Tornar admin' }}
                            </button>
                        </form>

                        <form action="{{ route('admin.usuarios.destroy', $user) }}" method="POST"
                              onsubmit="return confirm('Tem certeza que deseja excluir este usuário?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-form btn-form--primary" style="background:#dc2626;">Excluir usuário</button>
                        </form>
                    @endif
                </div>
            </div>
        </main>
    </div>

    @include('layouts.footer')
</body>
</html>
