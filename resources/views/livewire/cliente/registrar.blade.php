<div class="max-w-sm mx-auto">
    <h1 class="font-heading text-2xl font-semibold text-brand-900 dark:text-white mb-1 text-center">Criar conta</h1>
    <p class="text-sm text-muted dark:text-duskmuted text-center mb-8">Favorite carros e acompanhe suas compras</p>

    <form wire:submit="registrar" class="rounded-2xl border border-brand-100 dark:border-duskborder bg-white dark:bg-duskcard p-6 shadow-brand space-y-4">
        <div>
            <label class="block text-xs text-muted dark:text-duskmuted mb-1">Nome</label>
            <input type="text" wire:model="nome" placeholder="Seu nome" class="input">
            @error('nome') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-xs text-muted dark:text-duskmuted mb-1">E-mail</label>
            <input type="email" wire:model="email" placeholder="seu@email.com" class="input">
            @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-xs text-muted dark:text-duskmuted mb-1">Telefone / WhatsApp</label>
            <input type="text" wire:model="telefone" placeholder="(11) 99999-9999" class="input">
            @error('telefone') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-xs text-muted dark:text-duskmuted mb-1">Senha</label>
            <input type="password" wire:model="password" placeholder="Mínimo 6 caracteres" class="input">
            @error('password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-xs text-muted dark:text-duskmuted mb-1">Confirmar senha</label>
            <input type="password" wire:model="password_confirmation" placeholder="Repita a senha" class="input">
        </div>

        <button type="submit" wire:loading.attr="disabled"
                class="w-full py-3 rounded-full bg-brand-700 text-white text-sm font-semibold shadow-brand transition-transform hover:scale-[1.02] disabled:opacity-60">
            <span wire:loading.remove>Criar conta</span>
            <span wire:loading>Criando...</span>
        </button>

        <p class="text-center text-sm text-muted dark:text-duskmuted">
            Já tem conta? <a href="{{ route('cliente.login') }}" class="text-brand-700 font-medium hover:underline">Entrar</a>
        </p>
    </form>
</div>
