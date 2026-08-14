@props(['icon', 'titulo', 'requisito', 'configurada' => false, 'detalhe' => null, 'demo' => false, 'linkDemo' => null])

<div class="bg-bg border border-border rounded-card p-5 shadow-soft flex flex-col gap-3">
    <div class="flex items-start justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 shrink-0 rounded-control flex items-center justify-center
                {{ $configurada ? 'bg-success/10 text-success' : 'bg-surface text-text-secondary' }}">
                <x-dynamic-component :component="'heroicon-o-'.$icon" class="w-5 h-5" />
            </div>
            <div class="font-medium text-text-primary text-sm">{{ $titulo }}</div>
        </div>

        <x-admin.status-badge :variant="$configurada ? 'success' : 'neutral'" :label="$configurada ? ($demo ? 'Configurada (demo)' : 'Configurada') : 'Não configurada'" />
    </div>

    @if ($configurada && $detalhe)
        <p class="text-xs text-text-secondary">{{ $detalhe }}</p>
    @else
        <p class="text-xs text-text-secondary">
            <span class="font-medium text-text-primary">Pra ativar:</span> {{ $requisito }}
        </p>
    @endif

    <div>
        @if ($configurada && $linkDemo)
            <a href="{{ $linkDemo }}"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-control text-xs font-medium bg-success/10 text-success hover:bg-success/20">
                <x-heroicon-o-check class="w-3.5 h-3.5" /> Ver demonstração
            </a>
        @elseif ($configurada)
            <button type="button" disabled
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-control text-xs font-medium bg-success/10 text-success cursor-default">
                <x-heroicon-o-check class="w-3.5 h-3.5" /> Ativa
            </button>
        @else
            <button type="button" disabled title="Configuração via .env / painel — depende de credencial externa"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-control text-xs font-medium border border-border text-text-secondary cursor-not-allowed opacity-70">
                Conectar
            </button>
        @endif
    </div>
</div>
