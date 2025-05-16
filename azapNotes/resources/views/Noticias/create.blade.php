<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Adicionar Nova Notícia
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                <form action="{{ route('noticias.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label for="titulo" class="block text-gray-700 font-bold mb-2">Título</label>
                        <input type="text" name="titulo" id="titulo" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight" value="{{ old('titulo') }}" required>
                    </div>

                    <div class="mb-4">
                        <label for="conteudo" class="block text-gray-700 font-bold mb-2">Conteúdo</label>
                        <textarea name="conteudo" id="conteudo" rows="6" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight" required>{{ old('conteudo') }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label for="categoria" class="block text-gray-700 font-bold mb-2">Categoria</label>
                        <input type="text" name="categoria" id="categoria" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight" value="{{ old('categoria') }}" required>
                    </div>

                    <div class="mb-4">
                        <label for="status" class="block text-gray-700 font-bold mb-2">Status</label>
                        <select name="status" id="status" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight" required>
                            <option value="publicada" {{ old('status') === 'publicada' ? 'selected' : '' }}>Publicada</option>
                            <option value="rascunho" {{ old('status') === 'rascunho' ? 'selected' : '' }}>Rascunho</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="data_publicacao" class="block text-gray-700 font-bold mb-2">Data de Publicação</label>
                        <input type="date" name="data_publicacao" id="data_publicacao" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight" value="{{ old('data_publicacao', now()->format('Y-m-d')) }}" required>
                    </div>

                    <div class="flex items-center justify-between">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Salvar Notícia
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
