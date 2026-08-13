<div>
    @if ($enviado)
        <div class="max-w-lg mx-auto flex flex-col items-center text-center gap-3 p-8 rounded-2xl bg-white border border-brand-100 shadow-brand">
            <x-heroicon-o-check-circle class="w-10 h-10 text-brand-700" />
            <h3 class="font-heading text-lg font-semibold text-brand-900">Proposta recebida!</h3>
            <p class="text-sm text-muted">Recebemos as informações do seu veículo e entraremos em contato em breve. Ou fale agora pelo WhatsApp!</p>
            @if ($whatsappUrl ?? false)
                <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-2 mt-2 rounded-full bg-brand-500 px-6 py-3 text-sm font-semibold text-brand-900 shadow-brand transition-transform hover:scale-[1.03]">
                    Falar no WhatsApp
                </a>
            @endif
        </div>
    @else
        <form wire:submit="enviar" class="max-w-3xl mx-auto rounded-2xl border border-brand-100 bg-white p-6 sm:p-8 shadow-brand">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs text-muted mb-1">Nome *</label>
                    <input type="text" wire:model="nome" placeholder="Seu nome" class="input">
                    @error('nome') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs text-muted mb-1">Telefone / WhatsApp *</label>
                    <input type="text" wire:model="telefone" placeholder="(11) 99999-9999" class="input">
                    @error('telefone') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs text-muted mb-1">E-mail</label>
                    <input type="email" wire:model="email" placeholder="seu@email.com" class="input">
                    @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs text-muted mb-1">Marca do veículo</label>
                    <input type="text" wire:model="marca" placeholder="Ex: Toyota" class="input">
                </div>
                <div>
                    <label class="block text-xs text-muted mb-1">Modelo</label>
                    <input type="text" wire:model="modelo" placeholder="Ex: Corolla" class="input">
                </div>
                <div>
                    <label class="block text-xs text-muted mb-1">Ano</label>
                    <input type="text" wire:model="ano" placeholder="2020" class="input">
                </div>
                <div>
                    <label class="block text-xs text-muted mb-1">Quilometragem</label>
                    <input type="text" wire:model="km" placeholder="Ex: 50.000 km" class="input">
                </div>
                <div>
                    <label class="block text-xs text-muted mb-1">Valor pretendido (R$)</label>
                    <input type="text" wire:model="valorPretendido" placeholder="Ex: R$ 45.000" class="input">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs text-muted mb-1">Observações</label>
                    <textarea wire:model="observacoes" rows="3" placeholder="Estado do carro, opcionais, histórico..." class="input"></textarea>
                </div>
            </div>

            <div class="text-center mt-6">
                <button type="submit" wire:loading.attr="disabled"
                        class="rounded-full bg-brand-700 px-8 py-3.5 text-sm font-semibold text-white shadow-brand transition-transform hover:scale-[1.03] disabled:opacity-60">
                    <span wire:loading.remove>Enviar Proposta</span>
                    <span wire:loading>Enviando...</span>
                </button>
                <p class="text-xs text-muted mt-3">* Campos obrigatórios. Seus dados são usados apenas para contato.</p>
            </div>
        </form>
    @endif
</div>
