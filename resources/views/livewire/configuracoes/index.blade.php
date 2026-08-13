<div>
    <h1 class="text-xl font-semibold text-text-primary mb-1">Configurações</h1>
    <p class="text-sm text-text-secondary mb-6">Dados da empresa, conteúdo da home pública e status das integrações.</p>

    <div class="bg-bg border border-border rounded-card p-5 shadow-soft mb-6">
        <h2 class="text-sm font-semibold text-text-primary mb-4">Dados cadastrais</h2>
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

    <div class="bg-bg border border-border rounded-card p-5 shadow-soft mb-8">
        <div class="flex items-center justify-between mb-1">
            <h2 class="text-sm font-semibold text-text-primary">Home pública</h2>
        </div>
        <p class="text-xs text-text-secondary mb-4">Esses dados aparecem no rodapé, na seção de localização e no botão de WhatsApp da sua vitrine.</p>

        @if (session('sucesso'))
            <div class="mb-4 flex items-center gap-2.5 px-4 py-3 rounded-card bg-success/10 text-success text-sm border border-success/20">
                <x-heroicon-o-check-circle class="w-5 h-5 shrink-0" />
                {{ session('sucesso') }}
            </div>
        @endif

        <form wire:submit="salvar" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-text-secondary mb-1">Domínio próprio (opcional)</label>
                <input type="text" wire:model="dominio_customizado" placeholder="worldcred.com.br"
                       class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                <p class="text-xs text-text-secondary mt-1">Se preenchido, esse domínio (apontado via DNS pra este servidor) mostra essa revenda direto, sem precisar de subdomínio.</p>
                @error('dominio_customizado') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-text-secondary mb-1">Telefone</label>
                <input type="text" wire:model="telefone" placeholder="(14) 3882-5011"
                       class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                @error('telefone') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-text-secondary mb-1">WhatsApp</label>
                <input type="text" wire:model="whatsapp" placeholder="(14) 99754-2803"
                       class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                @error('whatsapp') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-text-secondary mb-1">E-mail de contato</label>
                <input type="email" wire:model="email_contato" placeholder="contato@suaempresa.com.br"
                       class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                @error('email_contato') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-text-secondary mb-1">Horário de funcionamento</label>
                <input type="text" wire:model="horario_funcionamento" placeholder="Seg-Sex: 08h às 18h30"
                       class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                @error('horario_funcionamento') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-text-secondary mb-1">Endereço</label>
                <input type="text" wire:model="endereco" placeholder="Av. Exemplo, 1000 - Bairro"
                       class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                @error('endereco') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-text-secondary mb-1">Cidade</label>
                <input type="text" wire:model="cidade" placeholder="Botucatu"
                       class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                @error('cidade') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-text-secondary mb-1">UF</label>
                <input type="text" wire:model="uf" placeholder="SP" maxlength="2"
                       class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary uppercase">
                @error('uf') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-text-secondary mb-1">Instagram (URL)</label>
                <input type="text" wire:model="instagram_url" placeholder="https://instagram.com/suaempresa"
                       class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                @error('instagram_url') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-text-secondary mb-1">Facebook (URL)</label>
                <input type="text" wire:model="facebook_url" placeholder="https://facebook.com/suaempresa"
                       class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                @error('facebook_url') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-text-secondary mb-1">Texto "Sobre" (aparece na home)</label>
                <textarea wire:model="sobre_texto" rows="3" placeholder="Conte brevemente sobre a empresa..."
                       class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary"></textarea>
                @error('sobre_texto') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="sm:col-span-2">
                <button type="submit" wire:loading.attr="disabled"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-control bg-primary text-white text-sm font-medium hover:bg-primary-light disabled:opacity-60">
                    <span wire:loading.remove>Salvar</span>
                    <span wire:loading>Salvando...</span>
                </button>
            </div>
        </form>
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
