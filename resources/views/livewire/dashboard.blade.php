<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold text-text-primary">Olá, {{ explode(' ', auth()->user()->name)[0] }}</h1>
            <p class="text-sm text-text-secondary mt-0.5">Resumo da operação em {{ now()->translatedFormat('d \d\e F \d\e Y') }}</p>
        </div>
    </div>

    @if ($leadsEmAtraso > 0)
        <a href="{{ route('admin.leads.inbox') }}"
           class="flex items-center gap-3 mb-4 px-4 py-3 rounded-card bg-error/10 border border-error/20 text-error text-sm hover:bg-error/15 transition-colors">
            <x-heroicon-o-exclamation-circle class="w-5 h-5 shrink-0" />
            <span>
                <strong>{{ $leadsEmAtraso }}</strong>
                {{ $leadsEmAtraso === 1 ? 'lead está' : 'leads estão' }}
                sem primeiro atendimento há mais de {{ \App\Livewire\Dashboard::SLA_MINUTOS }} minutos.
            </span>
        </a>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
        <div class="bg-bg border border-border rounded-card p-5 shadow-soft hover:shadow-soft-md transition-shadow">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-control bg-primary-soft text-primary flex items-center justify-center">
                    <x-heroicon-o-truck class="w-5 h-5" />
                </div>
            </div>
            <div class="text-xs font-medium text-text-secondary uppercase tracking-wide mb-1">Veículos disponíveis</div>
            <div class="text-3xl font-semibold text-text-primary tabular-nums">{{ $totalDisponiveis }}</div>
        </div>

        <div class="bg-bg border border-border rounded-card p-5 shadow-soft hover:shadow-soft-md transition-shadow">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-control bg-success/10 text-success flex items-center justify-center">
                    <x-heroicon-o-banknotes class="w-5 h-5" />
                </div>
            </div>
            <div class="text-xs font-medium text-text-secondary uppercase tracking-wide mb-1">Valor em estoque</div>
            <div class="text-3xl font-semibold text-text-primary tabular-nums">R$ {{ number_format($valorEmEstoque, 0, ',', '.') }}</div>
        </div>

        <div class="bg-bg border border-border rounded-card p-5 shadow-soft hover:shadow-soft-md transition-shadow">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-control bg-primary-soft text-primary flex items-center justify-center">
                    <x-heroicon-o-currency-dollar class="w-5 h-5" />
                </div>
            </div>
            <div class="text-xs font-medium text-text-secondary uppercase tracking-wide mb-1">Vendas do mês</div>
            <div class="text-3xl font-semibold text-text-primary tabular-nums">{{ $vendasDoMes }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
        <div class="bg-bg border border-border rounded-card p-5 shadow-soft hover:shadow-soft-md transition-shadow">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-control bg-success/10 text-success flex items-center justify-center">
                    <x-heroicon-o-banknotes class="w-5 h-5" />
                </div>
            </div>
            <div class="text-xs font-medium text-text-secondary uppercase tracking-wide mb-1">Faturamento bruto (mês)</div>
            <div class="text-2xl font-semibold text-text-primary tabular-nums">R$ {{ number_format($faturamentoBrutoMes, 2, ',', '.') }}</div>
        </div>

        <div class="bg-bg border border-border rounded-card p-5 shadow-soft hover:shadow-soft-md transition-shadow">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-control flex items-center justify-center {{ $lucroLiquidoMes >= 0 ? 'bg-success/10 text-success' : 'bg-error/10 text-error' }}">
                    <x-heroicon-o-chart-bar class="w-5 h-5" />
                </div>
            </div>
            <div class="text-xs font-medium text-text-secondary uppercase tracking-wide mb-1">Lucro líquido (mês)</div>
            <div class="text-2xl font-semibold tabular-nums {{ $lucroLiquidoMes >= 0 ? 'text-success' : 'text-error' }}">R$ {{ number_format($lucroLiquidoMes, 2, ',', '.') }}</div>
        </div>

        <div class="bg-bg border border-border rounded-card p-5 shadow-soft hover:shadow-soft-md transition-shadow">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-control bg-warning/10 text-warning flex items-center justify-center">
                    <x-heroicon-o-wrench-screwdriver class="w-5 h-5" />
                </div>
            </div>
            <div class="text-xs font-medium text-text-secondary uppercase tracking-wide mb-1">Custos pós-venda (mês)</div>
            <div class="text-2xl font-semibold text-text-primary tabular-nums">R$ {{ number_format($custosPosVendaMes, 2, ',', '.') }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-bg border border-border rounded-card p-4 shadow-soft flex items-center gap-3">
            <div class="w-9 h-9 shrink-0 rounded-control bg-surface text-text-secondary flex items-center justify-center">
                <x-heroicon-o-clock class="w-4.5 h-4.5" />
            </div>
            <div class="min-w-0">
                <div class="text-xs text-text-secondary truncate">Dias médios em pátio</div>
                <div class="text-lg font-semibold text-text-primary tabular-nums">{{ $diasMedioPatio }}</div>
            </div>
        </div>

        <div class="bg-bg border border-border rounded-card p-4 shadow-soft flex items-center gap-3">
            <div class="w-9 h-9 shrink-0 rounded-control flex items-center justify-center {{ $margemMedia >= 0 ? 'bg-success/10 text-success' : 'bg-error/10 text-error' }}">
                <x-heroicon-o-chart-bar class="w-4.5 h-4.5" />
            </div>
            <div class="min-w-0">
                <div class="text-xs text-text-secondary truncate">Margem média</div>
                <div class="text-lg font-semibold tabular-nums {{ $margemMedia >= 0 ? 'text-success' : 'text-error' }}">{{ $margemMedia }}%</div>
            </div>
        </div>

        <div class="bg-bg border border-border rounded-card p-4 shadow-soft flex items-center gap-3">
            <div class="w-9 h-9 shrink-0 rounded-control bg-primary-soft text-primary flex items-center justify-center">
                <x-heroicon-o-chat-bubble-left-right class="w-4.5 h-4.5" />
            </div>
            <div class="min-w-0">
                <div class="text-xs text-text-secondary truncate">Leads abertos</div>
                <div class="text-lg font-semibold text-text-primary tabular-nums">{{ $leadsAbertos }}</div>
            </div>
        </div>

        <div class="bg-bg border border-border rounded-card p-4 shadow-soft flex items-center gap-3">
            <div class="w-9 h-9 shrink-0 rounded-control flex items-center justify-center {{ $contasAVencer > 0 ? 'bg-warning/10 text-warning' : 'bg-surface text-text-secondary' }}">
                <x-heroicon-o-exclamation-triangle class="w-4.5 h-4.5" />
            </div>
            <div class="min-w-0">
                <div class="text-xs text-text-secondary truncate">Contas a vencer (7 dias)</div>
                <div class="text-lg font-semibold tabular-nums {{ $contasAVencer > 0 ? 'text-warning' : 'text-text-primary' }}">R$ {{ number_format($contasAVencer, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-4">
        <div class="bg-bg border border-border rounded-card p-5 shadow-soft">
            <h2 class="text-sm font-semibold text-text-primary mb-4">Vendas por mês</h2>
            <div wire:ignore x-data="graficoVendas(@js($series))" x-init="montar($refs.canvas)" class="h-64">
                <canvas x-ref="canvas"></canvas>
            </div>
        </div>

        <div class="bg-bg border border-border rounded-card p-5 shadow-soft">
            <h2 class="text-sm font-semibold text-text-primary mb-4">Leads recebidos por mês</h2>
            <div wire:ignore x-data="graficoLeads(@js($series))" x-init="montar($refs.canvas)" class="h-64">
                <canvas x-ref="canvas"></canvas>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-4">
        <div class="bg-bg border border-border rounded-card p-5 shadow-soft">
            <h2 class="text-sm font-semibold text-text-primary mb-4">Ranking de vendedores (mês)</h2>
            @if ($rankingVendedores->isNotEmpty())
                <div class="space-y-1">
                    @foreach ($rankingVendedores as $i => $item)
                        <div class="flex items-center gap-3 px-2 py-2 rounded-control {{ $i === 0 ? 'bg-warning/10' : '' }}">
                            <div class="w-6 text-center shrink-0">
                                @if ($i === 0)
                                    <x-heroicon-s-trophy class="w-4 h-4 text-warning inline" />
                                @else
                                    <span class="text-xs text-text-secondary tabular-nums">{{ $i + 1 }}º</span>
                                @endif
                            </div>
                            <span class="text-sm text-text-primary flex-1 min-w-0 truncate">{{ $item['vendedor']?->name ?? '—' }}</span>
                            <span class="text-xs text-text-secondary shrink-0">{{ $item['quantidade'] }} venda(s)</span>
                            <span class="text-sm font-medium text-text-primary tabular-nums shrink-0">R$ {{ number_format($item['faturamento'], 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-text-secondary py-4">Nenhuma venda confirmada este mês ainda.</p>
            @endif
        </div>

        <div class="bg-bg border border-border rounded-card p-5 shadow-soft">
            <h2 class="text-sm font-semibold text-text-primary mb-4">Atividades recentes</h2>
        @if ($atividades->isNotEmpty())
            <div class="space-y-1">
                @foreach ($atividades as $atividade)
                    <a href="{{ $atividade['url'] }}" class="flex items-center gap-3 px-2 py-2 rounded-control hover:bg-surface transition-colors">
                        <div class="w-8 h-8 shrink-0 rounded-control flex items-center justify-center {{ $atividade['cor'] }}">
                            <x-dynamic-component :component="'heroicon-o-'.$atividade['icone']" class="w-4 h-4" />
                        </div>
                        <span class="text-sm text-text-primary flex-1 min-w-0 truncate">{{ $atividade['texto'] }}</span>
                        <span class="text-xs text-text-secondary shrink-0">{{ $atividade['data']->diffForHumans() }}</span>
                    </a>
                @endforeach
            </div>
        @else
            <p class="text-sm text-text-secondary py-4">Nenhuma atividade registrada ainda.</p>
        @endif
        </div>
    </div>

    <script>
        function graficoVendas(series) {
            return {
                montar(canvas) {
                    new Chart(canvas, {
                        data: {
                            labels: series.labels,
                            datasets: [
                                {
                                    type: 'bar',
                                    label: 'Vendas (qtd)',
                                    data: series.vendasQtd,
                                    backgroundColor: '#1D4ED8',
                                    yAxisID: 'y',
                                },
                                {
                                    type: 'line',
                                    label: 'Faturamento (R$)',
                                    data: series.vendasReceita,
                                    borderColor: '#059669',
                                    backgroundColor: '#059669',
                                    tension: 0.3,
                                    yAxisID: 'y1',
                                },
                            ],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: { beginAtZero: true, ticks: { precision: 0 }, position: 'left' },
                                y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false } },
                            },
                        },
                    });
                },
            };
        }

        function graficoLeads(series) {
            return {
                montar(canvas) {
                    new Chart(canvas, {
                        type: 'bar',
                        data: {
                            labels: series.labels,
                            datasets: [{
                                label: 'Leads recebidos',
                                data: series.leadsQtd,
                                backgroundColor: '#3B82F6',
                            }],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
                        },
                    });
                },
            };
        }
    </script>
</div>
