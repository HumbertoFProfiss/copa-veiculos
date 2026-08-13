<div>
    <p class="mb-3 text-sm font-medium tracking-wide uppercase text-brand-700">Sua conta</p>
    <h1 class="font-heading text-2xl font-semibold text-brand-900 dark:text-white mb-1">Meu Perfil</h1>
    <p class="text-sm text-muted dark:text-duskmuted mb-8">Seus dados e um resumo da sua conta</p>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <a href="{{ route('cliente.favoritos') }}" class="rounded-2xl border border-brand-100 dark:border-duskborder bg-white dark:bg-duskcard p-5 hover:border-brand-500 transition-colors">
            <p class="text-xs text-muted dark:text-duskmuted uppercase tracking-wide mb-1">Favoritos</p>
            <p class="font-heading text-2xl font-semibold text-brand-700 tabular-nums">{{ $totalFavoritos }}</p>
        </a>
        <a href="{{ route('cliente.compras') }}" class="rounded-2xl border border-brand-100 dark:border-duskborder bg-white dark:bg-duskcard p-5 hover:border-brand-500 transition-colors">
            <p class="text-xs text-muted dark:text-duskmuted uppercase tracking-wide mb-1">Compras</p>
            <p class="font-heading text-2xl font-semibold text-brand-700 tabular-nums">{{ $totalCompras }}</p>
        </a>
        <a href="{{ route('cliente.garantias') }}" class="rounded-2xl border border-brand-100 dark:border-duskborder bg-white dark:bg-duskcard p-5 hover:border-brand-500 transition-colors">
            <p class="text-xs text-muted dark:text-duskmuted uppercase tracking-wide mb-1">Garantias abertas</p>
            <p class="font-heading text-2xl font-semibold text-brand-700 tabular-nums">{{ $garantiasAbertas }}</p>
        </a>
    </div>

    @if (session('sucesso'))
        <div class="mb-6 flex items-center gap-2.5 px-4 py-3 rounded-xl bg-brand-100/50 dark:bg-duskcard/60 text-brand-700 text-sm">
            <x-heroicon-o-check-circle class="w-5 h-5 shrink-0" />
            {{ session('sucesso') }}
        </div>
    @endif

    <form wire:submit="salvar" class="rounded-2xl border border-brand-100 dark:border-duskborder bg-white dark:bg-duskcard p-6 shadow-brand space-y-4 max-w-2xl">
        <h2 class="font-heading text-sm font-semibold text-brand-900 dark:text-white mb-2">Dados pessoais</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs text-muted dark:text-duskmuted mb-1">Nome</label>
                <input type="text" wire:model="nome" class="input">
                @error('nome') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs text-muted dark:text-duskmuted mb-1">E-mail</label>
                <input type="email" value="{{ $email }}" disabled class="input opacity-60 cursor-not-allowed">
            </div>
            <div>
                <label class="block text-xs text-muted dark:text-duskmuted mb-1">Telefone / WhatsApp</label>
                <input type="text" wire:model="telefone" class="input">
            </div>
            <div>
                <label class="block text-xs text-muted dark:text-duskmuted mb-1">Cidade</label>
                <input type="text" wire:model="cidade" class="input">
            </div>
            <div>
                <label class="block text-xs text-muted dark:text-duskmuted mb-1">UF</label>
                <input type="text" wire:model="uf" maxlength="2" class="input uppercase">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-xs text-muted dark:text-duskmuted mb-1">Endereço</label>
                <input type="text" wire:model="endereco" class="input">
            </div>
        </div>

        <h2 class="font-heading text-sm font-semibold text-brand-900 dark:text-white pt-2">Alterar senha</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs text-muted dark:text-duskmuted mb-1">Nova senha</label>
                <input type="password" wire:model="novaSenha" placeholder="Deixe em branco pra manter" class="input">
                @error('novaSenha') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs text-muted dark:text-duskmuted mb-1">Confirmar nova senha</label>
                <input type="password" wire:model="novaSenha_confirmation" class="input">
            </div>
        </div>

        <button type="submit" wire:loading.attr="disabled"
                class="rounded-full bg-brand-700 px-6 py-3 text-sm font-semibold text-white shadow-brand transition-transform hover:scale-[1.02] disabled:opacity-60">
            <span wire:loading.remove>Salvar</span>
            <span wire:loading>Salvando...</span>
        </button>
    </form>
</div>
