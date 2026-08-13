<div>
    @if ($enviado)
        <div class="flex items-start gap-3 p-4 rounded-xl bg-brand-100/50 dark:bg-duskcard/60 text-brand-700 dark:text-brand-300 text-sm">
            <x-heroicon-o-check-circle class="w-5 h-5 shrink-0" />
            <span>Recebemos seu interesse! Um vendedor vai entrar em contato em breve.</span>
        </div>
    @else
        <h3 class="font-heading text-sm font-semibold text-brand-900 dark:text-white mb-3">Tenho interesse</h3>
        <form wire:submit="enviar" class="space-y-3">
            <div>
                <input type="text" wire:model="nome" placeholder="Seu nome" class="input">
                @error('nome') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <input type="text" wire:model="telefone" placeholder="Telefone / WhatsApp" class="input">
                @error('telefone') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <input type="email" wire:model="email" placeholder="E-mail (opcional)" class="input">
                @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <button type="submit" wire:loading.attr="disabled"
                    class="w-full py-3 rounded-full bg-brand-700 text-white text-sm font-semibold shadow-brand transition-transform hover:scale-[1.02] disabled:opacity-60">
                <span wire:loading.remove>Enviar</span>
                <span wire:loading>Enviando...</span>
            </button>
        </form>
    @endif
</div>
