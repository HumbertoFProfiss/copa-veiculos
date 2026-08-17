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
                <x-admin.status-badge variant="info" :label="$empresa->nomePlanoExibicao()" />
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
                <input type="text" wire:model="telefone" placeholder="(14) 3882-5011" maxlength="15"
                       x-on:input="$el.value = maskTelefone($el.value)"
                       class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                @error('telefone') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-text-secondary mb-1">WhatsApp</label>
                <input type="text" wire:model="whatsapp" placeholder="(14) 99754-2803" maxlength="15"
                       x-on:input="$el.value = maskTelefone($el.value)"
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
            <div>
                <label class="block text-xs font-medium text-text-secondary mb-1">Limite de veículos em destaque</label>
                <input type="number" min="1" max="50" wire:model="limite_destaques"
                       class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary tabular-nums">
                <p class="text-xs text-text-secondary mt-1">Quantos veículos marcados com estrela aparecem na home do site.</p>
                @error('limite_destaques') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-text-secondary mb-1">Curva ABC — corte da Classe A (%)</label>
                <input type="number" min="1" max="99" wire:model="abc_limite_a"
                       class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary tabular-nums">
                <p class="text-xs text-text-secondary mt-1">Veículos que somam até esse % do valor em estoque viram Classe A.</p>
                @error('abc_limite_a') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-text-secondary mb-1">Curva ABC — corte da Classe B (%)</label>
                <input type="number" min="2" max="100" wire:model="abc_limite_b"
                       class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary tabular-nums">
                <p class="text-xs text-text-secondary mt-1">Da Classe A até esse %, os veículos viram Classe B. O resto é Classe C.</p>
                @error('abc_limite_b') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
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

            <div class="sm:col-span-2 pt-2 border-t border-border">
                <h3 class="text-sm font-semibold text-text-primary mb-1 mt-3">Analytics (opcional)</h3>
                <p class="text-xs text-text-secondary mb-3">Cole os IDs das suas contas — você cria as contas direto no Google/Meta, a gente só injeta o código no site.</p>
            </div>
            <div>
                <label class="block text-xs font-medium text-text-secondary mb-1">Google Analytics 4 (Measurement ID)</label>
                <input type="text" wire:model="analytics_ga4_id" placeholder="G-XXXXXXXXXX"
                       class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                @error('analytics_ga4_id') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-text-secondary mb-1">Google Tag Manager (Container ID)</label>
                <input type="text" wire:model="analytics_gtm_id" placeholder="GTM-XXXXXXX"
                       class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                @error('analytics_gtm_id') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-text-secondary mb-1">Meta Pixel (ID)</label>
                <input type="text" wire:model="analytics_meta_pixel_id" placeholder="123456789012345"
                       class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                @error('analytics_meta_pixel_id') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
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
            :configurada="true"
            :demo="true"
            detalhe="Emissão simulada disponível em cada venda confirmada — número e chave de acesso gerados automaticamente, com aviso de simulação no documento. Emitir de verdade ainda depende de certificado digital A1/A3 e credenciamento na SEFAZ do seu estado."
            requisito="certificado digital A1/A3 da empresa + credenciamento na SEFAZ do seu estado."
            :link-demo="route('admin.vendas.index')" />

        <x-admin.integracao-card
            icon="truck"
            titulo="Renave"
            :configurada="true"
            :demo="true"
            detalhe="Registro de transferência simulado disponível em cada venda confirmada — protocolo fictício gerado automaticamente, com aviso de simulação no comprovante. Registrar de verdade ainda depende de credenciamento no Registro Nacional de Veículos Automotores (DENATRAN)."
            requisito="credenciamento no Registro Nacional de Veículos Automotores (DENATRAN)."
            :link-demo="route('admin.vendas.index')" />

        <x-admin.integracao-card
            icon="chat-bubble-left-right"
            titulo="WhatsApp Business"
            :configurada="true"
            detalhe="Funciona de verdade via link direto (wa.me) em Leads, CRM e na página pública — abre a conversa já com a mensagem certa. A versão Cloud API (enviar/receber mensagem dentro do painel) depende de conta Meta Business verificada."
            requisito="conta Meta Business verificada + número de telefone dedicado. Por enquanto, o CRM usa link manual (wa.me)."
            :link-demo="route('admin.crm.pipeline')" />

        <x-admin.integracao-card
            icon="pencil-square"
            titulo="Assinatura eletrônica"
            :configurada="true"
            :demo="true"
            detalhe="Fluxo de envio, assinatura e recusa simulado disponível em Contratos — sem provedor real conectado ainda. Assinar de verdade depende de contratar um serviço como ClickSign ou D4Sign."
            requisito="conta em um provedor de assinatura (ex: ClickSign, D4Sign) e a chave de API dele."
            :link-demo="route('admin.contratos.index')" />

        <x-admin.integracao-card
            icon="building-library"
            titulo="MultiBanco (financiamento)"
            :configurada="true"
            :demo="true"
            detalhe="Simulação de proposta de financiamento disponível em cada venda, com parcela calculada de verdade (Tabela Price) sobre taxas cadastradas manualmente por banco. Sem envio real ao banco ainda."
            requisito="parceria/API com os bancos — hoje as taxas de financiamento são cadastradas manualmente."
            :link-demo="route('admin.vendas.index')" />

        <x-admin.integracao-card
            icon="globe-alt"
            titulo="Portais com API (WebMotors, iCarros...)"
            :configurada="true"
            detalhe="Publicação real via CSV/feed funcionando pra WebMotors, iCarros, Facebook Marketplace e outros — direto na tela do veículo, com status de publicado/erro por canal. Integração via API/OAuth de cada portal depende de contrato comercial com eles."
            requisito="contrato comercial + credencial de desenvolvedor de cada portal. Hoje a publicação funciona via CSV."
            :link-demo="route('admin.integracoes.canais')" />

        <x-admin.integracao-card
            icon="credit-card"
            titulo="Cobrança recorrente do plano"
            :configurada="true"
            detalhe="Fatura mensal gerada automaticamente (dia 1º) e baixa manual funcionam de verdade — só falta conectar um gateway (Stripe/Asaas) pra cobrar cartão/boleto sozinho." />
    </div>

    <h2 class="text-sm font-semibold text-text-primary mb-1 mt-8">Assinatura</h2>
    <p class="text-xs text-text-secondary mb-4">Plano contratado e histórico de faturas dessa revenda.</p>

    <div class="bg-bg border border-border rounded-card p-5 shadow-soft mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
            <div>
                <div class="text-xs text-text-secondary mb-1">Plano atual</div>
                <x-admin.status-badge variant="info" :label="$empresa->nomePlanoExibicao()" />
            </div>
            <div>
                <div class="text-xs text-text-secondary mb-1">Valor mensal</div>
                <div class="text-sm font-medium text-text-primary tabular-nums">
                    {{ $precoPlanoAtual !== null ? 'R$ '.number_format($precoPlanoAtual, 2, ',', '.') : '—' }}
                </div>
                @if ($empresa->plano === 'completo_opcionais' && $empresa->diasRestantesTrial() !== null)
                    <div class="text-xs {{ $empresa->diasRestantesTrial() > 0 ? 'text-warning' : 'text-error' }} mt-1">
                        @if ($empresa->diasRestantesTrial() > 0)
                            {{ $empresa->diasRestantesTrial() }} {{ $empresa->diasRestantesTrial() === 1 ? 'dia restante' : 'dias restantes' }} de teste
                        @else
                            Período de teste encerrado
                        @endif
                    </div>
                @endif
            </div>
            @php $proximaPendente = $faturas->firstWhere('status', 'pendente') ?? $faturas->firstWhere('status', 'atrasada'); @endphp
            <div>
                <div class="text-xs text-text-secondary mb-1">Próximo vencimento</div>
                <div class="text-sm font-medium text-text-primary">
                    {{ $proximaPendente ? $proximaPendente->vencimento->format('d/m/Y') : '—' }}
                </div>
            </div>
        </div>

        @if ($faturas->isEmpty())
            <p class="text-sm text-text-secondary">Nenhuma fatura gerada ainda.</p>
        @else
            <x-admin.data-table>
                <x-slot:head>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Referência</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Vencimento</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Valor</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Status</th>
                </x-slot:head>

                @foreach ($faturas as $fatura)
                    <tr class="hover:bg-surface">
                        <td class="px-4 py-3 text-text-primary">{{ $fatura->referencia->translatedFormat('M/Y') }}</td>
                        <td class="px-4 py-3 text-text-secondary">{{ $fatura->vencimento->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-text-secondary tabular-nums">R$ {{ number_format((float) $fatura->valor, 2, ',', '.') }}</td>
                        <td class="px-4 py-3">
                            <x-admin.status-badge
                                :variant="match($fatura->status) { 'paga' => 'success', 'atrasada' => 'error', 'cancelada' => 'neutral', default => 'warning' }"
                                :label="$fatura->statusLabel()" />
                        </td>
                    </tr>
                @endforeach
            </x-admin.data-table>
        @endif
    </div>
</div>
