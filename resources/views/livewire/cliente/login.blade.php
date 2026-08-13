<div class="max-w-sm mx-auto">
    <h1 class="text-2xl font-bold text-white mb-1 text-center">Entrar</h1>
    <p class="text-sm text-white/50 text-center mb-8">Acesse sua área do cliente</p>

    <form wire:submit="entrar" class="bg-night-card border border-night-border rounded-xl p-6 space-y-4">
        <div>
            <label class="block text-xs text-white/60 mb-1">E-mail</label>
            <input type="email" wire:model="email" placeholder="seu@email.com"
                   class="w-full rounded-control bg-night border border-night-border-light text-white placeholder-white/30 text-sm focus:border-gold focus:ring-gold">
            @error('email') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-xs text-white/60 mb-1">Senha</label>
            <input type="password" wire:model="password" placeholder="••••••••"
                   class="w-full rounded-control bg-night border border-night-border-light text-white placeholder-white/30 text-sm focus:border-gold focus:ring-gold">
            @error('password') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
        </div>
        <label class="flex items-center gap-2 text-sm text-white/60">
            <input type="checkbox" wire:model="lembrar" class="rounded border-night-border-light bg-night text-gold focus:ring-gold">
            Lembrar de mim
        </label>

        <button type="submit" wire:loading.attr="disabled"
                class="w-full py-2.5 rounded-control bg-gold text-night font-semibold text-sm hover:bg-gold-dark transition-colors disabled:opacity-60">
            <span wire:loading.remove>Entrar</span>
            <span wire:loading>Entrando...</span>
        </button>

        <p class="text-center text-sm text-white/50">
            Ainda não tem conta? <a href="{{ route('cliente.cadastro') }}" class="text-gold hover:underline">Criar conta</a>
        </p>
    </form>
</div>
