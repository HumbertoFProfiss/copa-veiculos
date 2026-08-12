<div class="max-w-4xl">
    <h1 class="text-xl font-semibold text-text-primary mb-6">Gerar contrato</h1>

    @if ($contratoGerado)
        <div class="bg-bg border border-border rounded-card p-6">
            <div class="flex items-center gap-2 text-success mb-4">
                <x-heroicon-o-check-circle class="w-6 h-6" />
                <h2 class="text-sm font-semibold">Contrato {{ $contratoGerado->numero }} gerado</h2>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.contratos.pdf', $contratoGerado) }}" target="_blank"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-control bg-primary text-white text-sm font-medium hover:bg-primary-light">
                    <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
                    Baixar PDF
                </a>
                <a href="{{ route('admin.contratos.index') }}" class="px-4 py-2 rounded-control border border-border text-sm text-text-secondary hover:bg-surface">
                    Ver todos os contratos
                </a>
            </div>
        </div>
    @else
        <div class="bg-bg border border-border rounded-card p-6 mb-4">
            <p class="text-sm text-text-secondary mb-4">
                Venda: <strong class="text-text-primary">{{ $venda->veiculo->marca }} {{ $venda->veiculo->modelo }}</strong>
                para <strong class="text-text-primary">{{ $venda->cliente->nome }}</strong>
            </p>
            <label class="block text-xs font-medium text-text-secondary mb-1">Modelo de contrato</label>
            <select wire:model.live="modeloId" class="w-full max-w-sm rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                @foreach ($modelos as $modelo)
                    <option value="{{ $modelo->id }}">{{ $modelo->nome }}</option>
                @endforeach
            </select>
        </div>

        @if ($this->preview)
            <div class="bg-bg border border-border rounded-card p-6 mb-4 max-h-[500px] overflow-y-auto prose prose-sm max-w-none">
                {!! $this->preview !!}
            </div>
        @endif

        <button wire:click="gerar" wire:loading.attr="disabled" type="button"
                class="px-4 py-2 rounded-control bg-primary text-white text-sm font-medium hover:bg-primary-light disabled:opacity-60">
            <span wire:loading.remove wire:target="gerar">Gerar PDF</span>
            <span wire:loading wire:target="gerar">Gerando...</span>
        </button>
    @endif
</div>
