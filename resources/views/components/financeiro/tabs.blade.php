@props(['ativo'])

<div class="flex gap-1 mb-6 border-b border-border overflow-x-auto">
    @foreach ([
        'index' => 'Contas a Pagar',
        'receber' => 'Contas a Receber',
        'dre' => 'DRE',
        'conciliacao' => 'Conciliação',
        'categorias' => 'Categorias',
    ] as $rota => $label)
        <a href="{{ route('admin.financeiro.'.$rota) }}"
           class="px-4 py-2.5 text-sm font-medium border-b-2 -mb-px whitespace-nowrap {{ $ativo === $rota ? 'border-primary text-primary' : 'border-transparent text-text-secondary hover:text-text-primary' }}">
            {{ $label }}
        </a>
    @endforeach
</div>
