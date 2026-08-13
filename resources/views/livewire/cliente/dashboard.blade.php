<div>
    <h1 class="text-2xl font-bold text-white mb-1">Meu Perfil</h1>
    <p class="text-sm text-white/50 mb-8">Seus dados e um resumo da sua conta</p>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <a href="{{ route('cliente.favoritos') }}" class="bg-night-card border border-night-border rounded-xl p-5 hover:border-gold/40 transition-colors">
            <p class="text-xs text-white/50 uppercase tracking-wide mb-1">Favoritos</p>
            <p class="text-2xl font-bold text-gold tabular-nums">{{ $totalFavoritos }}</p>
        </a>
        <a href="{{ route('cliente.compras') }}" class="bg-night-card border border-night-border rounded-xl p-5 hover:border-gold/40 transition-colors">
            <p class="text-xs text-white/50 uppercase tracking-wide mb-1">Compras</p>
            <p class="text-2xl font-bold text-gold tabular-nums">{{ $totalCompras }}</p>
        </a>
        <a href="{{ route('cliente.garantias') }}" class="bg-night-card border border-night-border rounded-xl p-5 hover:border-gold/40 transition-colors">
            <p class="text-xs text-white/50 uppercase tracking-wide mb-1">Garantias abertas</p>
            <p class="text-2xl font-bold text-gold tabular-nums">{{ $garantiasAbertas }}</p>
        </a>
    </div>

    @if (session('sucesso'))
        <div class="mb-6 flex items-center gap-2.5 px-4 py-3 rounded-card bg-green-500/10 text-green-400 text-sm border border-green-500/20">
            <x-heroicon-o-check-circle class="w-5 h-5 shrink-0" />
            {{ session('sucesso') }}
        </div>
    @endif

    <form wire:submit="salvar" class="bg-night-card border border-night-border rounded-xl p-6 space-y-4 max-w-2xl">
        <h2 class="text-sm font-semibold text-white mb-2">Dados pessoais</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs text-white/60 mb-1">Nome</label>
                <input type="text" wire:model="nome" class="w-full rounded-control bg-night border border-night-border-light text-white text-sm focus:border-gold focus:ring-gold">
                @error('nome') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs text-white/60 mb-1">E-mail</label>
                <input type="email" value="{{ $email }}" disabled
                       class="w-full rounded-control bg-night border border-night-border-light text-white/40 text-sm cursor-not-allowed">
            </div>
            <div>
                <label class="block text-xs text-white/60 mb-1">Telefone / WhatsApp</label>
                <input type="text" wire:model="telefone" class="w-full rounded-control bg-night border border-night-border-light text-white text-sm focus:border-gold focus:ring-gold">
            </div>
            <div>
                <label class="block text-xs text-white/60 mb-1">Cidade</label>
                <input type="text" wire:model="cidade" class="w-full rounded-control bg-night border border-night-border-light text-white text-sm focus:border-gold focus:ring-gold">
            </div>
            <div>
                <label class="block text-xs text-white/60 mb-1">UF</label>
                <input type="text" wire:model="uf" maxlength="2" class="w-full rounded-control bg-night border border-night-border-light text-white text-sm focus:border-gold focus:ring-gold uppercase">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-xs text-white/60 mb-1">Endereço</label>
                <input type="text" wire:model="endereco" class="w-full rounded-control bg-night border border-night-border-light text-white text-sm focus:border-gold focus:ring-gold">
            </div>
        </div>

        <h2 class="text-sm font-semibold text-white pt-2">Alterar senha</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs text-white/60 mb-1">Nova senha</label>
                <input type="password" wire:model="novaSenha" placeholder="Deixe em branco pra manter"
                       class="w-full rounded-control bg-night border border-night-border-light text-white placeholder-white/30 text-sm focus:border-gold focus:ring-gold">
                @error('novaSenha') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs text-white/60 mb-1">Confirmar nova senha</label>
                <input type="password" wire:model="novaSenha_confirmation"
                       class="w-full rounded-control bg-night border border-night-border-light text-white text-sm focus:border-gold focus:ring-gold">
            </div>
        </div>

        <button type="submit" wire:loading.attr="disabled"
                class="px-6 py-2.5 rounded-control bg-gold text-night font-semibold text-sm hover:bg-gold-dark transition-colors disabled:opacity-60">
            <span wire:loading.remove>Salvar</span>
            <span wire:loading>Salvando...</span>
        </button>
    </form>
</div>
