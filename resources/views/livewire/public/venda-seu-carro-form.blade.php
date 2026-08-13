<div>
    @if ($enviado)
        <div class="max-w-lg mx-auto flex flex-col items-center text-center gap-3 p-8 rounded-card bg-bg border border-border">
            <x-heroicon-o-check-circle class="w-10 h-10 text-success" />
            <h3 class="text-lg font-semibold text-text-primary">Proposta recebida!</h3>
            <p class="text-sm text-text-secondary">Recebemos as informações do seu veículo e entraremos em contato em breve. Ou fale agora pelo WhatsApp!</p>
            @if ($whatsappUrl ?? false)
                <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-2 mt-2 px-5 py-2.5 rounded-control bg-primary text-white font-medium text-sm hover:bg-primary-light transition-colors">
                    Falar no WhatsApp
                </a>
            @endif
        </div>
    @else
        <form wire:submit="enviar" class="max-w-3xl mx-auto bg-bg border border-border rounded-card p-6 sm:p-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs text-text-secondary mb-1">Nome *</label>
                    <input type="text" wire:model="nome" placeholder="Seu nome"
                           class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                    @error('nome') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs text-text-secondary mb-1">Telefone / WhatsApp *</label>
                    <input type="text" wire:model="telefone" placeholder="(11) 99999-9999"
                           class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                    @error('telefone') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs text-text-secondary mb-1">E-mail</label>
                    <input type="email" wire:model="email" placeholder="seu@email.com"
                           class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                    @error('email') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs text-text-secondary mb-1">Marca do veículo</label>
                    <input type="text" wire:model="marca" placeholder="Ex: Toyota"
                           class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <label class="block text-xs text-text-secondary mb-1">Modelo</label>
                    <input type="text" wire:model="modelo" placeholder="Ex: Corolla"
                           class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <label class="block text-xs text-text-secondary mb-1">Ano</label>
                    <input type="text" wire:model="ano" placeholder="2020"
                           class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <label class="block text-xs text-text-secondary mb-1">Quilometragem</label>
                    <input type="text" wire:model="km" placeholder="Ex: 50.000 km"
                           class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <label class="block text-xs text-text-secondary mb-1">Valor pretendido (R$)</label>
                    <input type="text" wire:model="valorPretendido" placeholder="Ex: R$ 45.000"
                           class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs text-text-secondary mb-1">Observações</label>
                    <textarea wire:model="observacoes" rows="3" placeholder="Estado do carro, opcionais, histórico..."
                           class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary"></textarea>
                </div>
            </div>

            <div class="text-center mt-6">
                <button type="submit" wire:loading.attr="disabled"
                        class="px-8 py-3 rounded-control bg-primary text-white font-medium text-sm hover:bg-primary-light transition-colors disabled:opacity-60">
                    <span wire:loading.remove>Enviar Proposta</span>
                    <span wire:loading>Enviando...</span>
                </button>
                <p class="text-xs text-text-secondary mt-3">* Campos obrigatórios. Seus dados são usados apenas para contato.</p>
            </div>
        </form>
    @endif
</div>
