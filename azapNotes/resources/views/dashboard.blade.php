<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        :root {
            --orange-light: #FFE5B4;
            --orange: #FFA94D;
            --orange-dark: #FF8500;
            --white: #ffffff;
            --gray-bg: #f9f9f9;
            --text: #333333;
            --text-muted: #777;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--gray-bg);
            color: var(--text);
        }

        .sidebar {
            width: 240px;
            height: 100vh;
            background-color: var(--orange);
            color: white;
            position: fixed;
            padding: 30px 20px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .sidebar a {
            text-decoration: none;
            color: white;
            padding: 12px 16px;
            border-radius: 6px;
            transition: background-color 0.3s;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background-color: var(--orange-dark);
        }

        .main {
            margin-left: 240px;
            padding: 40px;
        }

        .greeting {
            font-size: 26px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .subtitle {
            font-size: 16px;
            color: var(--text-muted);
            margin-bottom: 30px;
        }

        .section {
            margin-bottom: 50px;
        }

        .section h2 {
            font-size: 20px;
            margin-bottom: 20px;
            border-left: 5px solid var(--orange-dark);
            padding-left: 10px;
        }

        .card {
            background-color: var(--white);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
            margin-bottom: 15px;
        }

        .card h3 {
            margin: 0 0 8px 0;
            font-size: 16px;
        }

        .card small {
            color: var(--text-muted);
        }

        .badge {
            background-color: var(--orange-dark);
            color: white;
            padding: 4px 10px;
            font-size: 12px;
            border-radius: 12px;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <a href="{{ route('noticias.index') }}">Notícias</a>
        <a href="{{ route('tutoriais.index') }}">Tutoriais</a>
        <a href="{{ route('links.index') }}">Links úteis</a>
    </div>

    <div class="main">
        <div class="greeting">{{ $cumprimento }}{{ Auth::user()->name ?? 'usuário' }}!</div>
        <div class="subtitle">Tenha um ótimo dia de trabalho!</div>

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
