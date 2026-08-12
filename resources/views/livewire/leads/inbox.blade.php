<div>
    <h1 class="text-xl font-semibold text-text-primary mb-6">Caixa de Leads</h1>

    @if (count($relatorioQualidade))
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
            @foreach ($relatorioQualidade as $linha)
                <div class="bg-bg border border-border rounded-card p-4">
                    <div class="text-xs font-medium text-text-secondary uppercase tracking-wide">{{ $linha['portal'] }}</div>
                    <div class="text-lg font-semibold text-text-primary tabular-nums mt-1">{{ $linha['percentualValido'] }}% válidos</div>
                    <div class="text-xs text-text-secondary tabular-nums">{{ $linha['validos'] }} de {{ $linha['total'] }}</div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="flex flex-wrap items-center gap-3 mb-4">
        <div class="relative flex-1 min-w-[200px]">
            <x-heroicon-o-magnifying-glass class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-text-secondary" />
            <input type="text" wire:model.live.debounce.300ms="busca" placeholder="Buscar por nome ou telefone..."
                   class="w-full pl-9 pr-3 py-2 rounded-control border-border text-sm focus:border-primary focus:ring-primary">
        </div>
        <select wire:model.live="filtroPortal" class="rounded-control border-border text-sm focus:border-primary focus:ring-primary">
            <option value="">Todos os portais</option>
            @foreach (['webmotors','icarros','chavesnamao','mobautos','napista','carrossp','mercadolivre','facebook','site_proprio'] as $portal)
                <option value="{{ $portal }}">{{ $portal }}</option>
            @endforeach
        </select>
        <label class="inline-flex items-center gap-2 text-sm text-text-secondary">
            <input type="checkbox" wire:model.live="mostrarFalsos" class="rounded-control border-border text-primary focus:ring-primary">
            Mostrar marcados como falso
        </label>
    </div>

    <x-admin.data-table>
        <x-slot:head>
            <x-admin.th-sort coluna="nome" :ordenar-por="$ordenarPor" :ordenar-direcao="$ordenarDirecao">Nome</x-admin.th-sort>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Contato</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Portal</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Origens do contato</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Status</th>
            <th class="px-4 py-3"></th>
        </x-slot:head>

        @forelse ($leads as $lead)
            <tr wire:key="lead-{{ $lead->id }}" class="hover:bg-surface">
                <td class="px-4 py-3 font-medium text-text-primary">{{ $lead->nome }}</td>
                <td class="px-4 py-3 text-text-secondary">
                    <div>{{ $lead->telefone ?: '—' }}</div>
                    <div class="text-xs">{{ $lead->email ?: '' }}</div>
                </td>
                <td class="px-4 py-3 text-text-secondary">{{ $lead->portal_origem ?? $lead->origem }}</td>
                <td class="px-4 py-3 text-text-secondary text-xs">
                    @if ($lead->contato)
                        {{ implode(', ', $lead->contato->portaisDeOrigem()) }}
                    @else
                        —
                    @endif
                </td>
                <td class="px-4 py-3">
                    @if ($lead->lead_falso)
                        <x-admin.status-badge variant="error" label="Falso" />
                    @else
                        <x-admin.status-badge variant="success" label="Válido" />
                    @endif
                </td>
                <td class="px-4 py-3 text-right">
                    @can('leads.editar')
                        @if ($lead->lead_falso)
                            <button wire:click="reverterFalso({{ $lead->id }})" type="button" class="text-xs text-primary hover:underline">Reverter</button>
                        @else
                            <button wire:click="marcarFalso({{ $lead->id }})" wire:confirm="Marcar este lead como falso?" type="button" class="text-xs text-error hover:underline">Marcar falso</button>
                        @endif
                    @endcan
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="px-4 py-12 text-center text-text-secondary text-sm">Nenhum lead encontrado.</td></tr>
        @endforelse
    </x-admin.data-table>

    <div class="mt-4">{{ $leads->links() }}</div>
</div>
