<div>
    @if ($enviado)
        <div class="flex items-start gap-3 p-4 rounded-card bg-success/10 text-success text-sm">
            <x-heroicon-o-check-circle class="w-5 h-5 shrink-0" />
            <span>Recebemos seu interesse! Um vendedor vai entrar em contato em breve.</span>
        </div>
    @else
        <h3 class="text-sm font-semibold text-text-primary mb-3">Tenho interesse</h3>
        <form wire:submit="enviar" class="space-y-3">
            <div>
                <input type="text" wire:model="nome" placeholder="Seu nome"
                       class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                @error('nome') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <input type="text" wire:model="telefone" placeholder="Telefone / WhatsApp"
                       class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                @error('telefone') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <input type="email" wire:model="email" placeholder="E-mail (opcional)"
                       class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                @error('email') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
            </div>
            <button type="submit" wire:loading.attr="disabled"
                    class="w-full py-2 rounded-control bg-primary text-white text-sm font-medium hover:bg-primary-light disabled:opacity-60">
                <span wire:loading.remove>Enviar</span>
                <span wire:loading>Enviando...</span>
            </button>
        </form>
    @endif
</div>
