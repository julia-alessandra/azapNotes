<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    </head>

<body>
    <div class="sidebar">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <a href="{{ route('noticias.index') }}">Notícias</a>
        <a href="{{ route('tutoriais.index') }}">Tutoriais</a>
        <a href="{{ route('links.create') }}">Links úteis</a>
        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
            @csrf
            <button type="submit">
                Sair
            </button>
        </form>
    </div>

    <div class="main">
        <div class="greeting">{{ $cumprimento }}{{ Auth::user()->name ?? 'usuário' }}!</div>
        <div class="subtitle">Tenha um ótimo dia de trabalho!</div>

        <div class="section">
            <h2>Painel de controle</h2>
            <h3>Funcionarios</h3>

            @forelse($users ?? [] as $user)
            <div class="card">
                <h3>{{ $user->name }}</h3>
                <small>Id: {{ $user->id }}</small>
            </div>
            @empty
            <div class="card">Nenhum funcionário cadastrado.</div>
            @endforelse


        </div>

        <div class="section">
            <h2>Últimas Notícias</h2>
            @forelse($ultimasNoticias ?? [] as $noticia)
            <div class="card">
                <h3>{{ $noticia->titulo }}</h3>
                <small>
                    Publicado em {{ $noticia->data_publicacao->format('d/m/Y H:i') }}
                    <span class="badge">{{ ucfirst($noticia->categoria) }}</span>
                </small>
            </div>
            @empty
            <div class="card">
                Nenhuma notícia publicada ainda.
            </div>
            @endforelse
        </div>

        <div class="section">
            <h2>Links Úteis</h2>
            @forelse($links ?? [] as $link)
            <div class="card">
                <h3><a href="{{ $link->url }}" target="_blank" style="color: var(--text); text-decoration: none;">{{ $link->titulo }}</a></h3>
                <small>{{ $link->descricao }}</small>
            </div>
            @empty
            <div class="card">
                Nenhum link cadastrado.
            </div>
            @endforelse
        </div>

        <div class="section">
            <h2>Tutoriais</h2>
            @forelse($tutoriais ?? [] as $tutorial)
            <div class="card">
                <h3>{{ $tutorial->titulo }}</h3>
                <small>{{ $tutorial->descricao }}</small>
            </div>
            @empty
            <div class="card">
                Nenhum tutorial disponível.
            </div>
            @endforelse
        </div>
    </div>
</body>
<script>
    setTimeout(() => {
        location.reload();
    }, 3600000);
</script>

</html>