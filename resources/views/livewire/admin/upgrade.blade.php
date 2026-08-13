<div class="max-w-lg mx-auto text-center py-16">
    <div class="w-16 h-16 rounded-full bg-warning/10 text-warning flex items-center justify-center mx-auto mb-6">
        <x-heroicon-o-lock-closed class="w-8 h-8" />
    </div>
    <h1 class="text-xl font-semibold text-text-primary mb-2">Módulo não incluído no seu plano</h1>
    <p class="text-text-secondary mb-1">
        Seu plano atual é
        <x-admin.status-badge variant="info" :label="ucfirst(str_replace('_', ' ', app('tenant')->plano))" />
    </p>
    <p class="text-text-secondary mt-4">
        O módulo <strong class="text-text-primary">{{ ucfirst($modulo) }}</strong> não está incluído no seu plano
        atual. Fale com a gente pra fazer upgrade e desbloquear.
    </p>

    <div class="flex items-center justify-center gap-3 mt-8">
        <a href="{{ route('dashboard') }}" class="px-5 py-2.5 rounded-control border border-border text-sm text-text-secondary hover:bg-surface">
            Voltar ao início
        </a>
        @if (app('tenant')?->whatsappUrl())
            <a href="{{ app('tenant')->whatsappUrl() }}?text=Ol%C3%A1%2C%20quero%20fazer%20upgrade%20do%20meu%20plano%20no%20Copa%20Ve%C3%ADculos"
               target="_blank" rel="noopener"
               class="px-5 py-2.5 rounded-control bg-primary text-white text-sm font-medium hover:bg-primary-light">
                Falar sobre upgrade
            </a>
        @endif
    </div>
</div>
