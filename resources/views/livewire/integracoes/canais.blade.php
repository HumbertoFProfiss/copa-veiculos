<div>
    <x-integracoes.tabs ativo="canais" />

    <h1 class="text-xl font-semibold text-text-primary mb-1">Canais de Anúncio</h1>
    <p class="text-sm text-text-secondary mb-6">Status de cada canal, credenciais e histórico de publicação — cada link é a evidência de que o anúncio saiu de verdade.</p>

    @if (session('sucesso'))
        <div class="mb-4 flex items-center gap-2.5 px-4 py-3 rounded-card bg-success/10 text-success text-sm border border-success/20">
            <x-heroicon-o-check-circle class="w-5 h-5 shrink-0" />
            {{ session('sucesso') }}
        </div>
    @endif

    <div class="space-y-3 mb-8">
        @foreach ($canais as $item)
            @php $canal = $item['canal']; @endphp
            <div class="bg-bg border border-border rounded-card p-5 shadow-soft">
                <div class="flex items-center justify-between flex-wrap gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-control flex items-center justify-center {{ $item['configurado'] ? 'bg-success/10 text-success' : 'bg-warning/10 text-warning' }}">
                            <x-heroicon-o-signal class="w-4.5 h-4.5" />
                        </div>
                        <div>
                            <p class="font-medium text-text-primary">{{ $canal->nome }}</p>
                            <p class="text-xs text-text-secondary">
                                {{ $item['totalPublicados'] }} publicado(s) · {{ $item['totalErros'] }} erro(s)
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-admin.status-badge :variant="$item['configurado'] ? 'success' : 'warning'" :label="$item['configurado'] ? 'Configurado' : 'Requer credencial'" />
                        <button wire:click="testarConexao({{ $canal->id }})" type="button"
                                class="px-3 py-1.5 rounded-control border border-border text-xs font-medium text-text-primary hover:bg-surface">
                            Testar conexão
                        </button>
                        <button wire:click="abrirCredenciais({{ $canal->id }})" type="button"
                                class="px-3 py-1.5 rounded-control border border-border text-xs font-medium text-text-primary hover:bg-surface">
                            Credenciais
                        </button>
                    </div>
                </div>

                @if (isset($resultadoTeste[$canal->id]))
                    <div class="mt-3 flex items-start gap-2 text-sm p-3 rounded-control {{ $resultadoTeste[$canal->id]['ok'] ? 'bg-success/10 text-success' : 'bg-error/10 text-error' }}">
                        <x-heroicon-o-{{ $resultadoTeste[$canal->id]['ok'] ? 'check-circle' : 'x-circle' }} class="w-4 h-4 mt-0.5 shrink-0" />
                        <span>{{ $resultadoTeste[$canal->id]['mensagem'] }}</span>
                    </div>
                @endif

                @if ($canalCredencialAberto === $canal->id)
                    <div class="mt-4 pt-4 border-t border-border">
                        <p class="text-xs font-semibold text-text-secondary uppercase tracking-wide mb-2">Credenciais salvas</p>
                        @forelse ($item['credenciais'] as $credencial)
                            <div class="flex items-center justify-between px-3 py-2 rounded-control bg-surface mb-2 text-sm">
                                <span class="font-medium text-text-primary">{{ $credencial->chave }}</span>
                                <div class="flex items-center gap-3">
                                    <span class="text-text-secondary tabular-nums">••••••••{{ substr($credencial->valor, -4) }}</span>
                                    <button wire:click="removerCredencial({{ $credencial->id }})" wire:confirm="Remover esta credencial?" type="button" class="text-text-secondary hover:text-error">
                                        <x-heroicon-o-trash class="w-3.5 h-3.5" />
                                    </button>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-text-secondary mb-2">Nenhuma credencial cadastrada ainda.</p>
                        @endforelse

                        <form wire:submit="salvarCredencial" class="flex gap-2 mt-3">
                            <input type="text" wire:model="novaCredencialChave" placeholder="Ex: api_key, client_secret"
                                   class="w-40 rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                            <input type="text" wire:model="novaCredencialValor" placeholder="Valor"
                                   class="flex-1 rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                            <button type="submit" class="px-4 py-2 rounded-control bg-primary text-white text-sm font-medium hover:bg-primary-light whitespace-nowrap">
                                Salvar
                            </button>
                        </form>
                        @error('novaCredencialChave') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                        @error('novaCredencialValor') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <div class="bg-bg border border-border rounded-card p-5 shadow-soft">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-semibold text-text-primary">Histórico de publicações</h2>
            <select wire:model.live="filtroCanalLog" class="rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                <option value="">Todos os canais</option>
                @foreach ($todosCanais as $canal)
                    <option value="{{ $canal->id }}">{{ $canal->nome }}</option>
                @endforeach
            </select>
        </div>

        <x-admin.data-table>
            <x-slot:head>
                <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Veículo</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Canal</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Status</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Evidência</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Quando</th>
            </x-slot:head>
            @forelse ($log as $publicacao)
                <tr class="hover:bg-surface">
                    <td class="px-4 py-3 font-medium text-text-primary">{{ $publicacao->veiculo?->marca }} {{ $publicacao->veiculo?->modelo }}</td>
                    <td class="px-4 py-3 text-text-secondary">{{ $publicacao->canal?->nome }}</td>
                    <td class="px-4 py-3">
                        <x-admin.status-badge
                            :variant="match($publicacao->status) { 'publicado' => 'success', 'erro' => 'error', 'despublicado' => 'neutral', default => 'warning' }"
                            :label="ucfirst($publicacao->status)" />
                    </td>
                    <td class="px-4 py-3 text-text-secondary">
                        @if ($publicacao->url_anuncio)
                            <a href="{{ $publicacao->url_anuncio }}" target="_blank" rel="noopener" class="text-primary hover:underline">Ver anúncio</a>
                        @elseif ($publicacao->ultimo_erro)
                            <span class="text-error text-xs">{{ \Illuminate\Support\Str::limit($publicacao->ultimo_erro, 60) }}</span>
                        @else
                            —
                        @endif
                    </td>
                    <td class="px-4 py-3 text-text-secondary tabular-nums">{{ $publicacao->ultima_sincronizacao_em?->diffForHumans() }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-12 text-center text-text-secondary text-sm">Nenhuma publicação registrada ainda.</td></tr>
            @endforelse
        </x-admin.data-table>
    </div>
</div>
