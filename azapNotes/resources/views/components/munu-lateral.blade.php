<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<div class="sidebar">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <a href="{{ route('noticias.index') }}">Notícias</a>
        <a href="{{ route('tutoriais.index') }}">Tutoriais</a>
        <a href="{{ route('links.create') }}">Links úteis</a>
    </div>
</body>
</html>