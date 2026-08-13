<div class="max-w-3xl">
    <x-financeiro.tabs ativo="conciliacao" />

    <h1 class="text-xl font-semibold text-text-primary mb-6">Conciliação Bancária</h1>

    <div class="bg-bg border border-border rounded-card p-6 mb-6">
        <p class="text-sm text-text-secondary mb-4">
            Importe o extrato OFX do banco - lançamentos com mesmo valor e vencimento próximo (±3 dias)
            de uma conta a pagar/receber pendente são conciliados automaticamente.
        </p>
        <label class="block">
            <div class="border-2 border-dashed border-border rounded-card p-6 text-center cursor-pointer hover:border-primary transition-colors">
                <x-heroicon-o-building-library class="w-8 h-8 mx-auto text-text-secondary mb-2" />
                <p class="text-sm text-text-secondary">Selecione o arquivo .ofx do extrato</p>
                <input type="file" wire:model="arquivo" accept=".ofx" class="hidden">
            </div>
        </label>
        @error('arquivo') <p class="text-xs text-error mt-2">{{ $message }}</p> @enderror

        @if ($arquivo)
            <button wire:click="processarOfx" wire:loading.attr="disabled" type="button"
                    class="mt-4 px-4 py-2 rounded-control bg-primary text-white text-sm font-medium hover:bg-primary-light disabled:opacity-60">
                <span wire:loading.remove wire:target="processarOfx">Processar</span>
                <span wire:loading wire:target="processarOfx">Processando...</span>
            </button>
        @endif
    </div>

    @if ($importacaoAtual)
        <div class="bg-bg border border-border rounded-card p-6 mb-6">
            <div class="flex items-center gap-2 text-success mb-2">
                <x-heroicon-o-check-circle class="w-5 h-5" />
                <span class="text-sm font-semibold">Importação concluída</span>
            </div>
            <p class="text-sm text-text-secondary">
                {{ $importacaoAtual->total_lancamentos }} lançamento(s) encontrado(s),
                {{ $importacaoAtual->total_conciliados }} conciliado(s) automaticamente.
            </p>
        </div>
    @endif

    @if ($historico->isNotEmpty())
        <x-admin.data-table>
            <x-slot:head>
                <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Data</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Lançamentos</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Conciliados</th>
            </x-slot:head>
            @foreach ($historico as $item)
                <tr>
                    <td class="px-4 py-3 text-text-secondary">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-4 py-3 tabular-nums text-text-primary">{{ $item->total_lancamentos }}</td>
                    <td class="px-4 py-3 tabular-nums text-text-primary">{{ $item->total_conciliados }}</td>
                </tr>
            @endforeach
        </x-admin.data-table>
    @endif
</div>
