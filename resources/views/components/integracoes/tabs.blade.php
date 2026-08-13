@props(['ativo'])

<div class="flex gap-1 mb-6 border-b border-border overflow-x-auto">
    @foreach ([
        'index' => 'API e Webhooks',
        'canais' => 'Canais de Anúncio',
    ] as $rota => $label)
        <a href="{{ route('admin.integracoes.'.$rota) }}"
           class="px-4 py-2.5 text-sm font-medium border-b-2 -mb-px whitespace-nowrap {{ $ativo === $rota ? 'border-primary text-primary' : 'border-transparent text-text-secondary hover:text-text-primary' }}">
            {{ $label }}
        </a>
    @endforeach
</div>
