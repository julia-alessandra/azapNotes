@extends('layouts.app')


<div class="container">
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Erro!</strong> Corrija os campos abaixo:<br><br>
            <ul>
                @foreach ($errors->all() as $erro)
                    <li>{{ $erro }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('links.store') }}" method="POST">
        @csrf

        <div>
            <label for="titulo" class="form-label">Título</label>
            <input type="text" name="titulo" class="form-control" value="{{ old('titulo') }}" required>
        </div>

        <div>
            <label for="url" class="form-label">URL</label>
            <input type="url" name="url" class="form-control" value="{{ old('url') }}" required>
        </div>

        <div>
            <label for="descricao" class="form-label">Descrição</label>
            <textarea name="descricao" class="form-control" rows="3">{{ old('descricao') }}</textarea>
        </div>

        <div>
            <label for="categoria" class="form-label">Categoria</label>
            <input type="text" name="categoria" class="form-control" value="{{ old('categoria') }}">
        </div>

        <div>
            <label for="status" class="form-label">Status</label>
            <select name="status" class="form-control" required>
                <option value="ativo" {{ old('status') === 'ativo' ? 'selected' : '' }}>Ativo</option>
                <option value="inativo" {{ old('status') === 'inativo' ? 'selected' : '' }}>Inativo</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Salvar</button>
    </form>
</div>
