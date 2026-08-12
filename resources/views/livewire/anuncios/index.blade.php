<div>
    <h1 class="text-xl font-semibold text-text-primary mb-6">Anúncios</h1>

    <div class="mb-4">
        <select wire:model.live="filtroStatus" class="rounded-control border-border text-sm focus:border-primary focus:ring-primary">
            <option value="">Todos os status</option>
            <option value="publicado">Publicado</option>
            <option value="pendente">Pendente</option>
            <option value="erro">Erro</option>
            <option value="despublicado">Removido</option>
        </select>
    </div>

    <x-admin.data-table>
        <x-slot:head>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Veículo</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Canal</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Status</th>
            <x-admin.th-sort coluna="ultima_sincronizacao_em" :ordenar-por="$ordenarPor" :ordenar-direcao="$ordenarDirecao">Última sincronização</x-admin.th-sort>
            <th class="px-4 py-3"></th>
        </x-slot:head>
        @forelse ($publicacoes as $publicacao)
            <tr wire:key="pub-{{ $publicacao->id }}" class="hover:bg-surface">
                <td class="px-4 py-3 font-medium text-text-primary">
                    <a href="{{ route('admin.veiculos.editar', $publicacao->veiculo) }}" class="hover:text-primary">
                        {{ $publicacao->veiculo->marca }} {{ $publicacao->veiculo->modelo }}
                    </a>
                </td>
                <td class="px-4 py-3 text-text-secondary">{{ $publicacao->canal->nome }}</td>
                <td class="px-4 py-3">
                    @php
                        $variant = match($publicacao->status) { 'publicado' => 'success', 'erro' => 'error', 'despublicado' => 'neutral', default => 'warning' };
                    @endphp
                    <span title="{{ $publicacao->ultimo_erro }}">
                        <x-admin.status-badge :variant="$variant" :label="ucfirst($publicacao->status)" />
                    </span>
                </td>
                <td class="px-4 py-3 text-text-secondary">{{ $publicacao->ultima_sincronizacao_em?->diffForHumans() ?? '—' }}</td>
                <td class="px-4 py-3 text-right">
                    @if ($publicacao->status === 'erro')
                        <button wire:click="reprocessar({{ $publicacao->id }})" type="button" class="text-xs text-primary hover:underline">Reprocessar</button>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="px-4 py-12 text-center text-text-secondary text-sm">Nenhuma publicação ainda.</td></tr>
        @endforelse
    </x-admin.data-table>
    <div class="mt-4">{{ $publicacoes->links() }}</div>
</div>
