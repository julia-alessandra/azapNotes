<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Adicionar Novo Tutorial
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                <form action="{{ route('tutoriais.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-4">
                        <label for="titulo" class="block text-gray-700 font-bold mb-2">Título</label>
                        <input type="text" name="titulo" id="titulo" class="shadow border rounded w-full py-2 px-3 text-gray-700" value="{{ old('titulo') }}" required>
                    </div>

                    <div class="mb-4">
                        <label for="descricao" class="block text-gray-700 font-bold mb-2">Descrição</label>
                        <textarea name="descricao" id="descricao" rows="3" class="shadow border rounded w-full py-2 px-3 text-gray-700" required>{{ old('descricao') }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label for="conteudo" class="block text-gray-700 font-bold mb-2">Conteúdo</label>
                        <textarea name="conteudo" id="conteudo" rows="6" class="shadow border rounded w-full py-2 px-3 text-gray-700" required>{{ old('conteudo') }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label for="categoria" class="block text-gray-700 font-bold mb-2">Categoria</label>
                        <input type="text" name="categoria" id="categoria" class="shadow border rounded w-full py-2 px-3 text-gray-700" value="{{ old('categoria') }}" required>
                    </div>

                    <div class="mb-4">
                        <label for="tags" class="block text-gray-700 font-bold mb-2">Tags (separadas por vírgula)</label>
                        <input type="text" name="tags" id="tags" class="shadow border rounded w-full py-2 px-3 text-gray-700" value="{{ old('tags') }}">
                    </div>

                    <div class="mb-4">
                        <label for="observacoes" class="block text-gray-700 font-bold mb-2">Observações</label>
                        <textarea name="observacoes" id="observacoes" rows="2" class="shadow border rounded w-full py-2 px-3 text-gray-700">{{ old('observacoes') }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label for="status" class="block text-gray-700 font-bold mb-2">Status</label>
                        <select name="status" id="status" class="shadow border rounded w-full py-2 px-3 text-gray-700" required>
                            <option value="ativo" {{ old('status') === 'ativo' ? 'selected' : '' }}>Ativo</option>
                            <option value="inativo" {{ old('status') === 'inativo' ? 'selected' : '' }}>Inativo</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="anexos[]" class="block text-gray-700 font-bold mb-2">Anexos</label>
                        <input type="file" name="anexos[]" id="anexos" class="block w-full text-gray-700" multiple>
                        <small class="text-gray-500">Você pode selecionar vários arquivos</small>
                    </div>

                    <div class="mb-4">
                        <label for="data_criacao" class="block text-gray-700 font-bold mb-2">Data de Criação</label>
                        <input type="date" name="data_criacao" id="data_criacao" class="shadow border rounded w-full py-2 px-3 text-gray-700" value="{{ old('data_criacao', now()->format('Y-m-d')) }}" required>
                    </div>

                    <div class="flex items-center justify-end">
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            Salvar Tutorial
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
