<div>
    <h1 class="text-xl font-semibold text-text-primary mb-6">Contratos</h1>

    <x-admin.data-table>
        <x-slot:head>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Número</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Modelo</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Cliente</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Veículo</th>
            <x-admin.th-sort coluna="created_at" :ordenar-por="$ordenarPor" :ordenar-direcao="$ordenarDirecao">Gerado em</x-admin.th-sort>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Status</th>
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
                <td class="px-4 py-3 text-right">
                    <a href="{{ route('admin.contratos.pdf', $contrato) }}" target="_blank" class="text-xs text-primary hover:underline">PDF</a>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="px-4 py-12 text-center text-text-secondary text-sm">Nenhum contrato gerado.</td></tr>
        @endforelse
    </x-admin.data-table>

    <div class="mt-4">{{ $contratos->links() }}</div>
</div>
