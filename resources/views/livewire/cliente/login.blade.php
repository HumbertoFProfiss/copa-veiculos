<div class="max-w-sm mx-auto">
    <h1 class="font-heading text-2xl font-semibold text-brand-900 mb-1 text-center">Entrar</h1>
    <p class="text-sm text-muted text-center mb-8">Acesse sua área do cliente</p>

    <form wire:submit="entrar" class="rounded-2xl border border-brand-100 bg-white p-6 shadow-brand space-y-4">
        <div>
            <label class="block text-xs text-muted mb-1">E-mail</label>
            <input type="email" wire:model="email" placeholder="seu@email.com" class="input">
            @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-xs text-muted mb-1">Senha</label>
            <input type="password" wire:model="password" placeholder="••••••••" class="input">
            @error('password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
        <label class="flex items-center gap-2 text-sm text-muted">
            <input type="checkbox" wire:model="lembrar" class="rounded border-brand-100 text-brand-700 focus:ring-brand-500">
            Lembrar de mim
        </label>

        <button type="submit" wire:loading.attr="disabled"
                class="w-full py-3 rounded-full bg-brand-700 text-white text-sm font-semibold shadow-brand transition-transform hover:scale-[1.02] disabled:opacity-60">
            <span wire:loading.remove>Entrar</span>
            <span wire:loading>Entrando...</span>
        </button>

        <p class="text-center text-sm text-muted">
            Ainda não tem conta? <a href="{{ route('cliente.cadastro') }}" class="text-brand-700 font-medium hover:underline">Criar conta</a>
        </p>
    </form>
</div>
