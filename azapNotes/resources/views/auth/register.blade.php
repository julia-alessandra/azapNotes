<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Nome -->
        <div>
            <x-input-label for="name" :value="__('Nome')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Telefone -->
        <div class="mt-4">
            <x-input-label for="phone" :value="__('Telefone')" />
            <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" required />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <!-- Cargo -->
        <div class="mt-4">
            <x-input-label for="position" :value="__('Cargo')" />
            <x-text-input id="position" class="block mt-1 w-full" type="text" name="position" :value="old('position')" required />
            <x-input-error :messages="$errors->get('position')" class="mt-2" />
        </div>

        <!-- Departamento -->
        <select name="departamento_id" id="departamento_id" class="block mt-1 w-full">
            <x-input-label for="departamento" :value="__('Departamento')" />
            <option value="">Selecione...</option>
            @foreach ($departamentos as $departamento)
            <option value="{{ $departamento->_id }}">{{ $departamento->nome }}</option>
            @endforeach
        </select>

        <!-- Tipo de usuário -->
        <div class="mt-4">
            <x-input-label for="role" :value="__('Tipo de usuário')" />
            <select id="role" name="role" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                <option value="funcionario">Funcionário</option>
                <option value="admin">Administrador</option>
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <!-- Senha -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Senha')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirmar senha -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirmar senha')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Botões -->
        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
                {{ __('Já registrado?') }}
            </a>

            <x-primary-button class="ml-4">
                {{ __('Cadastrar') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>