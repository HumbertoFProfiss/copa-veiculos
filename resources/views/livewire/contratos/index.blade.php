<div>
    <h1 class="text-xl font-semibold text-text-primary mb-6">Contratos</h1>

    <div class="flex flex-wrap items-center gap-3 mb-4">
        <div class="relative flex-1 min-w-[240px]">
            <x-heroicon-o-magnifying-glass class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-text-secondary" />
            <input type="text" wire:model.live.debounce.300ms="busca"
                   placeholder="Buscar por número do contrato, carro vendido ou nome do cliente..."
                   class="w-full pl-9 pr-3 py-2 rounded-control border-border text-sm focus:border-primary focus:ring-primary">
        </div>
    </div>

    <x-admin.data-table>
        <x-slot:head>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Número</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Modelo</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Cliente</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Veículo</th>
            <x-admin.th-sort coluna="created_at" :ordenar-por="$ordenarPor" :ordenar-direcao="$ordenarDirecao">Gerado em</x-admin.th-sort>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Status</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Assinatura</th>
            <th class="px-4 py-3"></th>
        </x-slot:head>

        @forelse ($contratos as $contrato)
            <tr wire:key="contrato-{{ $contrato->id }}" class="hover:bg-surface">
                <td class="px-4 py-3 font-medium text-text-primary">{{ $contrato->numero }}</td>
                <td class="px-4 py-3 text-text-secondary">{{ $contrato->modelo->nome }}</td>
                <td class="px-4 py-3 text-text-secondary">{{ $contrato->cliente?->nome }}</td>
                <td class="px-4 py-3 text-text-secondary">{{ $contrato->veiculo?->marca }} {{ $contrato->veiculo?->modelo }}</td>
                <td class="px-4 py-3 text-text-secondary">{{ $contrato->created_at->format('d/m/Y H:i') }}</td>
                <td class="px-4 py-3">
                    <x-admin.status-badge :variant="$contrato->statusVariant()" :label="$contrato->statusLabel()" />
                </td>
                <td class="px-4 py-3">
                    <x-admin.status-badge :variant="$contrato->assinaturaStatusVariant()" :label="$contrato->assinaturaStatusLabel()" />
                </td>
                <td class="px-4 py-3 text-right">
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.contratos.pdf', $contrato) }}" target="_blank" class="text-xs text-primary hover:underline">PDF</a>
                        @can('contratos.criar')
                            @if ($contrato->status === 'rascunho')
                                <button wire:click="enviarParaAssinatura({{ $contrato->id }})" wire:confirm="Enviar pra assinatura SIMULADA? Nenhum e-mail real é enviado — é só uma simulação do fluxo."
                                        type="button" class="text-xs text-primary hover:underline">Enviar p/ assinatura</button>
                            @elseif ($contrato->assinatura_status === 'pendente')
                                <button wire:click="marcarAssinado({{ $contrato->id }})" type="button" class="text-xs text-success hover:underline">Marcar assinado</button>
                                <button wire:click="recusarAssinatura({{ $contrato->id }})" type="button" class="text-xs text-error hover:underline">Recusar</button>
                            @endif
                        @endcan
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="8" class="px-4 py-12 text-center text-text-secondary text-sm">Nenhum contrato gerado.</td></tr>
        @endforelse
    </x-admin.data-table>

    <div class="mt-4">{{ $contratos->links() }}</div>

    <p class="mt-3 text-xs text-warning font-medium">SIMULAÇÃO — a assinatura eletrônica acima não usa um provedor real (ClickSign/D4Sign/etc). Assinar de verdade depende de contratar um desses serviços.</p>
</div>
