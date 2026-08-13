<div class="max-w-sm mx-auto">
    <h1 class="text-2xl font-bold text-white mb-1 text-center">Criar conta</h1>
    <p class="text-sm text-white/50 text-center mb-8">Favorite carros e acompanhe suas compras</p>

    <form wire:submit="registrar" class="bg-night-card border border-night-border rounded-xl p-6 space-y-4">
        <div>
            <label class="block text-xs text-white/60 mb-1">Nome</label>
            <input type="text" wire:model="nome" placeholder="Seu nome"
                   class="w-full rounded-control bg-night border border-night-border-light text-white placeholder-white/30 text-sm focus:border-gold focus:ring-gold">
            @error('nome') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-xs text-white/60 mb-1">E-mail</label>
            <input type="email" wire:model="email" placeholder="seu@email.com"
                   class="w-full rounded-control bg-night border border-night-border-light text-white placeholder-white/30 text-sm focus:border-gold focus:ring-gold">
            @error('email') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-xs text-white/60 mb-1">Telefone / WhatsApp</label>
            <input type="text" wire:model="telefone" placeholder="(11) 99999-9999"
                   class="w-full rounded-control bg-night border border-night-border-light text-white placeholder-white/30 text-sm focus:border-gold focus:ring-gold">
            @error('telefone') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-xs text-white/60 mb-1">Senha</label>
            <input type="password" wire:model="password" placeholder="Mínimo 6 caracteres"
                   class="w-full rounded-control bg-night border border-night-border-light text-white placeholder-white/30 text-sm focus:border-gold focus:ring-gold">
            @error('password') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-xs text-white/60 mb-1">Confirmar senha</label>
            <input type="password" wire:model="password_confirmation" placeholder="Repita a senha"
                   class="w-full rounded-control bg-night border border-night-border-light text-white placeholder-white/30 text-sm focus:border-gold focus:ring-gold">
        </div>

        <button type="submit" wire:loading.attr="disabled"
                class="w-full py-2.5 rounded-control bg-gold text-night font-semibold text-sm hover:bg-gold-dark transition-colors disabled:opacity-60">
            <span wire:loading.remove>Criar conta</span>
            <span wire:loading>Criando...</span>
        </button>

        <p class="text-center text-sm text-white/50">
            Já tem conta? <a href="{{ route('cliente.login') }}" class="text-gold hover:underline">Entrar</a>
        </p>
    </form>
</div>
