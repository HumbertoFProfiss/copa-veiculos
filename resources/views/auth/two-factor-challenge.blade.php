<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600" x-data="{ recuperacao: false }">
        <p x-show="!recuperacao">Informe o código de 6 dígitos do seu aplicativo autenticador.</p>
        <p x-show="recuperacao" x-cloak>Informe um dos seus códigos de recuperação.</p>

        <form method="POST" action="{{ route('two-factor-challenge') }}" class="mt-4">
            @csrf

            <div x-show="!recuperacao">
                <x-input-label for="codigo" value="Código" />
                <x-text-input id="codigo" class="block mt-1 w-full tracking-widest" type="text"
                              name="codigo" inputmode="numeric" autocomplete="one-time-code" autofocus />
            </div>

            <div x-show="recuperacao" x-cloak>
                <x-input-label for="codigo_recuperacao" value="Código de recuperação" />
                <x-text-input id="codigo_recuperacao" class="block mt-1 w-full" type="text"
                              name="codigo_recuperacao" autocomplete="off" />
            </div>

            <input type="hidden" name="recuperacao" x-bind:value="recuperacao ? '1' : '0'">

            <x-input-error :messages="$errors->get('codigo')" class="mt-2" />

            <div class="flex items-center justify-between mt-4">
                <button type="button" @click="recuperacao = !recuperacao"
                        class="underline text-sm text-gray-600 hover:text-gray-900">
                    <span x-show="!recuperacao">Usar código de recuperação</span>
                    <span x-show="recuperacao" x-cloak>Usar código do aplicativo</span>
                </button>

                <x-primary-button>Confirmar</x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>
