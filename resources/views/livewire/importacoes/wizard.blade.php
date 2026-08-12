<div class="max-w-4xl">
    <h1 class="text-xl font-semibold text-text-primary mb-6">Importar estoque</h1>

    @if ($etapa === 'upload')
        <div class="bg-bg border border-border rounded-card p-6">
            <div class="mb-4">
                <label class="block text-xs font-medium text-text-secondary mb-1">Origem do arquivo</label>
                <select wire:model="origem" class="w-full max-w-xs rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                    <option value="csv_generico">CSV genérico</option>
                    <option value="boom">Export do Boom Sistemas</option>
                </select>
            </div>

            <label class="block">
                <div class="border-2 border-dashed border-border rounded-card p-8 text-center cursor-pointer hover:border-primary transition-colors">
                    <x-heroicon-o-arrow-up-tray class="w-8 h-8 mx-auto text-text-secondary mb-2" />
                    <p class="text-sm text-text-secondary">Selecione o arquivo CSV do estoque</p>
                    <input type="file" wire:model="arquivo" accept=".csv,.txt" class="hidden">
                </div>
            </label>
            @error('arquivo') <p class="text-xs text-error mt-2">{{ $message }}</p> @enderror

            @if ($arquivo)
                <p class="mt-3 text-sm text-text-primary">{{ $arquivo->getClientOriginalName() }}</p>
                <button wire:click="processarUpload" type="button"
                        class="mt-4 px-4 py-2 rounded-control bg-primary text-white text-sm font-medium hover:bg-primary-light">
                    Continuar
                </button>
            @endif
        </div>
    @endif

    @if ($etapa === 'mapeamento')
        <div class="bg-bg border border-border rounded-card p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-sm font-semibold text-text-primary">Confira o mapeamento de colunas</h2>
                    <p class="text-xs text-text-secondary mt-1">{{ count($linhas) }} linha(s) encontrada(s). Ajuste o de-para se necessário.</p>
                </div>
            </div>

            <div class="space-y-2 mb-6">
                @foreach ($colunas as $coluna)
                    <div class="flex items-center gap-3">
                        <span class="w-48 text-sm text-text-primary font-medium truncate">{{ $coluna }}</span>
                        <x-heroicon-o-arrow-right class="w-4 h-4 text-text-secondary shrink-0" />
                        <select wire:change="atualizarMapeamento('{{ $coluna }}', $event.target.value)"
                                class="flex-1 rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                            <option value="">— Ignorar coluna —</option>
                            @foreach ($camposDestino as $campo => $label)
                                <option value="{{ $campo }}" @selected(($mapeamento[$coluna] ?? null) === $campo)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                @endforeach
            </div>

            @if (count($linhas) > 0)
                <div class="mb-6 overflow-x-auto">
                    <p class="text-xs font-medium text-text-secondary uppercase tracking-wide mb-2">Prévia (3 primeiras linhas)</p>
                    <table class="w-full text-xs border border-border rounded-control overflow-hidden">
                        <thead class="bg-surface">
                            <tr>
                                @foreach ($colunas as $coluna)
                                    <th class="px-2 py-1 text-left">{{ $coluna }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach (array_slice($linhas, 0, 3) as $linha)
                                <tr>
                                    @foreach ($colunas as $coluna)
                                        <td class="px-2 py-1 text-text-secondary">{{ $linha[$coluna] ?? '' }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <div class="flex items-center gap-3">
                <button wire:click="confirmarMapeamento" wire:loading.attr="disabled" type="button"
                        class="px-4 py-2 rounded-control bg-primary text-white text-sm font-medium hover:bg-primary-light disabled:opacity-60">
                    <span wire:loading.remove wire:target="confirmarMapeamento">Importar {{ count($linhas) }} veículo(s)</span>
                    <span wire:loading wire:target="confirmarMapeamento">Importando...</span>
                </button>
                <button wire:click="reiniciar" type="button" class="px-4 py-2 rounded-control border border-border text-sm text-text-secondary hover:bg-surface">
                    Cancelar
                </button>
            </div>
        </div>
    @endif

    @if ($etapa === 'concluido' && $importacao)
        <div class="bg-bg border border-border rounded-card p-6">
            <div class="flex items-center gap-2 text-success mb-4">
                <x-heroicon-o-check-circle class="w-6 h-6" />
                <h2 class="text-sm font-semibold">Importação concluída</h2>
            </div>
            <div class="grid grid-cols-3 gap-4 mb-6">
                <div class="bg-surface rounded-control p-4 text-center">
                    <div class="text-2xl font-semibold text-success tabular-nums">{{ $importacao->total_importados }}</div>
                    <div class="text-xs text-text-secondary mt-1">Importados</div>
                </div>
                <div class="bg-surface rounded-control p-4 text-center">
                    <div class="text-2xl font-semibold text-warning tabular-nums">{{ $importacao->total_duplicados }}</div>
                    <div class="text-xs text-text-secondary mt-1">Duplicados (ignorados)</div>
                </div>
                <div class="bg-surface rounded-control p-4 text-center">
                    <div class="text-2xl font-semibold text-error tabular-nums">{{ $importacao->total_erros }}</div>
                    <div class="text-xs text-text-secondary mt-1">Com erro</div>
                </div>
            </div>

            @if ($importacao->erros->isNotEmpty())
                <div class="mb-6">
                    <p class="text-xs font-medium text-text-secondary uppercase tracking-wide mb-2">Linhas com erro</p>
                    <ul class="text-sm text-text-secondary space-y-1">
                        @foreach ($importacao->erros as $erro)
                            <li>Linha {{ $erro->numero_linha }}: {{ $erro->motivo }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.veiculos.index') }}" class="px-4 py-2 rounded-control bg-primary text-white text-sm font-medium hover:bg-primary-light">
                    Ver estoque
                </a>
                <button wire:click="reiniciar" type="button" class="px-4 py-2 rounded-control border border-border text-sm text-text-secondary hover:bg-surface">
                    Nova importação
                </button>
            </div>
        </div>
    @endif

    @if ($historico->isNotEmpty())
        <div class="mt-8">
            <h2 class="text-sm font-semibold text-text-primary mb-3">Importações recentes</h2>
            <x-admin.data-table>
                <x-slot:head>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Arquivo</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Origem</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Resultado</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Data</th>
                </x-slot:head>
                @foreach ($historico as $item)
                    <tr>
                        <td class="px-4 py-3 text-text-primary">{{ $item->nome_arquivo_original }}</td>
                        <td class="px-4 py-3 text-text-secondary">{{ $item->origem }}</td>
                        <td class="px-4 py-3 text-text-secondary tabular-nums">
                            {{ $item->total_importados }} ok / {{ $item->total_duplicados }} dup / {{ $item->total_erros }} erro
                        </td>
                        <td class="px-4 py-3 text-text-secondary">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                @endforeach
            </x-admin.data-table>
        </div>
    @endif
</div>
