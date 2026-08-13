<div>
    <h1 class="text-xl font-semibold text-text-primary mb-1">Configurações</h1>
    <p class="text-sm text-text-secondary mb-6">Dados da empresa e status das integrações.</p>

    <div class="bg-bg border border-border rounded-card p-5 shadow-soft mb-8">
        <h2 class="text-sm font-semibold text-text-primary mb-4">Dados da empresa</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <div class="text-xs text-text-secondary mb-1">Nome</div>
                <div class="text-sm font-medium text-text-primary">{{ $empresa->nome }}</div>
            </div>
            <div>
                <div class="text-xs text-text-secondary mb-1">CNPJ</div>
                <div class="text-sm font-medium text-text-primary">{{ $empresa->cnpj ?? '—' }}</div>
            </div>
            <div>
                <div class="text-xs text-text-secondary mb-1">Subdomínio</div>
                <div class="text-sm font-medium text-text-primary">{{ $empresa->slug }}.{{ config('tenancy.central_domain') }}</div>
            </div>
            <div>
                <div class="text-xs text-text-secondary mb-1">Plano</div>
                <x-admin.status-badge variant="info" :label="ucfirst(str_replace('_', ' ', $empresa->plano))" />
            </div>
        </div>
    </div>

    <h2 class="text-sm font-semibold text-text-primary mb-1">Integrações</h2>
    <p class="text-xs text-text-secondary mb-4">
        O que já funciona de verdade e o que ainda depende de credencial/homologação de terceiro pra ligar.
    </p>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <x-admin.integracao-card
            icon="sparkles"
            titulo="Assistente de IA"
            :configurada="$iaConfigurada"
            :detalhe="$iaConfigurada ? 'Provedor ativo: '.($iaProvider === 'openai_compatible' ? 'Gemini (endpoint compatível OpenAI)' : $iaProvider).' — modelo '.$iaModelo : null"
            requisito="defina AI_PROVIDER, AI_API_KEY, AI_BASE_URL e AI_MODELO no arquivo .env (testado com Gemini)." />

        <x-admin.integracao-card
            icon="document-text"
            titulo="Nota Fiscal Eletrônica (NF-e)"
            requisito="certificado digital A1/A3 da empresa + credenciamento na SEFAZ do seu estado." />

        <x-admin.integracao-card
            icon="truck"
            titulo="Renave"
            requisito="credenciamento no Registro Nacional de Veículos Automotores (DENATRAN)." />

        <x-admin.integracao-card
            icon="chat-bubble-left-right"
            titulo="WhatsApp Business (Cloud API)"
            requisito="conta Meta Business verificada + número de telefone dedicado. Por enquanto, o CRM usa link manual (wa.me)." />

        <x-admin.integracao-card
            icon="pencil-square"
            titulo="Assinatura eletrônica"
            requisito="conta em um provedor de assinatura (ex: ClickSign, D4Sign) e a chave de API dele." />

        <x-admin.integracao-card
            icon="building-library"
            titulo="MultiBanco (financiamento)"
            requisito="parceria/API com os bancos — hoje as taxas de financiamento são cadastradas manualmente." />

        <x-admin.integracao-card
            icon="globe-alt"
            titulo="Portais com API (WebMotors, iCarros...)"
            requisito="contrato comercial + credencial de desenvolvedor de cada portal. Hoje a publicação funciona via CSV." />

        <x-admin.integracao-card
            icon="credit-card"
            titulo="Cobrança recorrente do plano"
            requisito="conta em um processador de pagamento (ex: Stripe, Asaas) pra cobrar as revendas automaticamente." />
    </div>
</div>
