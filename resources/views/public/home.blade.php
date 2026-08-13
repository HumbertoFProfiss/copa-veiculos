<x-layouts.public :title="($empresa->nome ?? config('app.name')).' - Loja de Carros Online'" :hero-transparente="true">

    @php
        $heroFotos = $destaques->flatMap(fn ($v) => $v->fotos->take(1))->take(5);
        if ($heroFotos->isEmpty()) {
            $heroFotos = $ultimasAdicoes->flatMap(fn ($v) => $v->fotos->take(1))->take(5);
        }
    @endphp

    <!-- ===== HERO ===== -->
    <section id="home" class="relative flex min-h-[600px] items-center overflow-hidden bg-brand-900">
        @if ($heroFotos->isNotEmpty())
            <img src="{{ $heroFotos->first()->url() }}" alt="Veículo em destaque"
                 class="absolute inset-0 h-full w-full object-cover object-center">
        @endif
        <div class="absolute inset-0"
             style="background: linear-gradient(115deg, rgba(11,58,93,0.92) 10%, rgba(18,104,163,0.75) 50%, rgba(79,168,232,0.35) 100%)"></div>

        <div class="relative z-10 max-w-7xl mx-auto px-6 sm:px-8 pt-32 pb-16 w-full">
            <div class="max-w-2xl">
                <p class="mb-5 text-sm font-medium tracking-widest text-brand-300 uppercase">
                    @if ($empresa?->cidade)
                        {{ $empresa->cidade }}@if($empresa->uf) · {{ $empresa->uf }}@endif —
                    @endif
                    Multimarcas e Seminovos
                </p>

                <h1 class="font-heading text-white text-4xl sm:text-5xl lg:text-6xl font-semibold leading-tight">
                    Aqui você encontra<br>o carro certo!
                </h1>

                <p class="mt-6 max-w-lg text-lg text-brand-100">
                    Multimarcas zero KM e Seminovos. Encontre o carro perfeito para você, com atendimento próximo e transparente.
                </p>

                <div class="mt-10 flex flex-col gap-4 sm:flex-row">
                    <a href="{{ route('estoque') }}"
                       class="rounded-full bg-brand-500 px-7 py-3.5 text-center text-sm font-semibold text-brand-900 shadow-brand transition-transform hover:scale-[1.03]">
                        Ver Estoque Completo
                    </a>
                    <a href="#financiamento"
                       class="rounded-full border border-white/70 px-7 py-3.5 text-center text-sm font-semibold text-white transition-colors hover:bg-white/10">
                        Simular Financiamento
                    </a>
                </div>
            </div>

            <div class="mt-12 flex flex-col gap-6 border-t border-white/20 pt-8 sm:flex-row sm:gap-12">
                <p class="text-sm font-medium text-brand-100">{{ $totalEstoque }} veículos em estoque</p>
                @if ($empresa?->horario_funcionamento)
                    <p class="text-sm font-medium text-brand-100">{{ $empresa->horario_funcionamento }}</p>
                @endif
                <p class="text-sm font-medium text-brand-100">Financiamento facilitado</p>
            </div>
        </div>
    </section>

    <!-- ===== DESTAQUES DA SEMANA ===== -->
    <section class="bg-white py-24">
        <div class="max-w-7xl mx-auto px-6 sm:px-8">
            <p class="mb-3 text-sm font-medium tracking-wide uppercase text-brand-700">Estoque</p>
            <h2 class="font-heading text-brand-900 text-3xl sm:text-4xl font-semibold">Destaques da Semana</h2>

            @if ($destaques->isNotEmpty())
                <div class="mt-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($destaques as $veiculo)
                        <a href="{{ route('veiculo.show', $veiculo) }}"
                           class="group block rounded-2xl border border-brand-100 bg-white overflow-hidden transition-colors hover:border-brand-500">
                            <div class="relative aspect-[4/3] bg-brand-100/50 overflow-hidden">
                                @if ($veiculo->fotos->isNotEmpty())
                                    <img src="{{ $veiculo->fotos->first()->url() }}" alt="{{ $veiculo->marca }} {{ $veiculo->modelo }}"
                                         loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-muted">
                                        <x-heroicon-o-photo class="w-12 h-12" />
                                    </div>
                                @endif
                                <span class="absolute top-3 left-3 px-2.5 py-1 rounded-full bg-brand-500 text-brand-900 text-xs font-semibold">Destaque</span>
                                <div class="absolute top-3 right-3" onclick="event.preventDefault()">
                                    @livewire('cliente.favorito-botao', ['veiculo' => $veiculo], key('fav-destaque-'.$veiculo->id))
                                </div>
                            </div>
                            <div class="p-6">
                                <h3 class="font-heading font-medium text-brand-900">{{ $veiculo->marca }} {{ $veiculo->modelo }}</h3>
                                <p class="text-sm text-muted mt-0.5">{{ $veiculo->ano_fabricacao }}/{{ $veiculo->ano_modelo }}</p>
                                <div class="flex items-center gap-3 mt-2 text-xs text-muted">
                                    <span>{{ number_format($veiculo->km, 0, ',', '.') }} km</span>
                                    <span>&middot;</span>
                                    <span class="capitalize">{{ $veiculo->combustivel }}</span>
                                </div>
                                <p class="mt-3 font-heading text-xl font-semibold text-brand-700 tabular-nums">
                                    @if ($veiculo->preco_venda)
                                        R$ {{ number_format($veiculo->preco_venda, 2, ',', '.') }}
                                    @else
                                        Consulte
                                    @endif
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <p class="mt-12 text-center text-muted">Nenhum destaque disponível no momento.</p>
            @endif
        </div>
    </section>

    <!-- ===== ÚLTIMAS ADIÇÕES ===== -->
    @if ($ultimasAdicoes->isNotEmpty())
        <section class="bg-brand-100/50 py-24">
            <div class="max-w-7xl mx-auto px-6 sm:px-8">
                <p class="mb-3 text-sm font-medium tracking-wide uppercase text-brand-700">Novidades</p>
                <h2 class="font-heading text-brand-900 text-3xl sm:text-4xl font-semibold">Últimas Adições</h2>

                <div class="mt-12 grid grid-cols-1 sm:grid-cols-3 gap-6">
                    @foreach ($ultimasAdicoes as $veiculo)
                        <a href="{{ route('veiculo.show', $veiculo) }}"
                           class="group block rounded-2xl border border-brand-100 bg-white overflow-hidden transition-colors hover:border-brand-500">
                            <div class="relative aspect-[4/3] bg-brand-100/50 overflow-hidden">
                                @if ($veiculo->fotos->isNotEmpty())
                                    <img src="{{ $veiculo->fotos->first()->url() }}" alt="{{ $veiculo->marca }} {{ $veiculo->modelo }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-muted">
                                        <x-heroicon-o-photo class="w-10 h-10" />
                                    </div>
                                @endif
                                <div class="absolute top-3 right-3" onclick="event.preventDefault()">
                                    @livewire('cliente.favorito-botao', ['veiculo' => $veiculo], key('fav-ultima-'.$veiculo->id))
                                </div>
                            </div>
                            <div class="p-5">
                                <h3 class="font-heading font-medium text-brand-900 text-sm">{{ $veiculo->marca }} {{ $veiculo->modelo }}</h3>
                                <p class="font-heading text-brand-700 font-semibold mt-1 tabular-nums">
                                    @if ($veiculo->preco_venda)
                                        R$ {{ number_format($veiculo->preco_venda, 2, ',', '.') }}
                                    @else
                                        Consulte
                                    @endif
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="text-center mt-12">
                    <a href="{{ route('estoque') }}"
                       class="inline-block rounded-full bg-brand-700 px-7 py-3.5 text-sm font-semibold text-white shadow-brand transition-transform hover:scale-[1.03]">
                        Ver Todo o Estoque ({{ $totalEstoque }})
                    </a>
                </div>
            </div>
        </section>
    @endif

    <!-- ===== DIFERENCIAIS ===== -->
    <section class="bg-white py-24">
        <div class="max-w-7xl mx-auto px-6 sm:px-8">
            <p class="mb-3 text-sm font-medium tracking-wide uppercase text-brand-700">Por que a gente</p>
            <h2 class="font-heading text-brand-900 text-3xl sm:text-4xl font-semibold">Sobre Nós</h2>

            <div class="mt-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <div class="group rounded-2xl border border-brand-100 bg-white p-7 transition-colors hover:bg-brand-100/50">
                    <span class="mb-5 flex size-11 items-center justify-center rounded-xl text-white" style="background-image: linear-gradient(135deg, #1268A3 0%, #4FA8E8 55%, #7FD3F0 100%)">
                        <x-heroicon-o-banknotes class="w-5 h-5" />
                    </span>
                    <h3 class="font-heading font-medium text-brand-900">Financiamento</h3>
                    <p class="mt-2 text-sm text-muted">Financie a compra do seu veículo. O carro dos seus sonhos pode ser seu.</p>
                </div>
                <div class="group rounded-2xl border border-brand-100 bg-white p-7 transition-colors hover:bg-brand-100/50">
                    <span class="mb-5 flex size-11 items-center justify-center rounded-xl text-white" style="background-image: linear-gradient(135deg, #1268A3 0%, #4FA8E8 55%, #7FD3F0 100%)">
                        <x-heroicon-o-truck class="w-5 h-5" />
                    </span>
                    <h3 class="font-heading font-medium text-brand-900">Venda seu veículo</h3>
                    <p class="mt-2 text-sm text-muted">A maneira mais rápida de vender o seu carro, com segurança na negociação.</p>
                </div>
                <div class="group rounded-2xl border border-brand-100 bg-white p-7 transition-colors hover:bg-brand-100/50">
                    <span class="mb-5 flex size-11 items-center justify-center rounded-xl text-white" style="background-image: linear-gradient(135deg, #1268A3 0%, #4FA8E8 55%, #7FD3F0 100%)">
                        <x-heroicon-o-building-storefront class="w-5 h-5" />
                    </span>
                    <h3 class="font-heading font-medium text-brand-900">Empresa</h3>
                    <p class="mt-2 text-sm text-muted">
                        Somos uma empresa familiar
                        @if ($empresa?->cidade)
                            , localizada em {{ $empresa->cidade }}@if($empresa->uf) — {{ $empresa->uf }}@endif.
                        @else
                            .
                        @endif
                    </p>
                </div>
                <div class="group rounded-2xl border border-brand-100 bg-white p-7 transition-colors hover:bg-brand-100/50">
                    <span class="mb-5 flex size-11 items-center justify-center rounded-xl text-white" style="background-image: linear-gradient(135deg, #1268A3 0%, #4FA8E8 55%, #7FD3F0 100%)">
                        <x-heroicon-o-chat-bubble-left-right class="w-5 h-5" />
                    </span>
                    <h3 class="font-heading font-medium text-brand-900">Fale conosco</h3>
                    <p class="mt-2 text-sm text-muted">Precisa falar conosco? Envie um formulário ou nos ligue. Estamos esperando por você.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== SOBRE ===== -->
    <section class="bg-brand-100/50 py-24">
        <div class="max-w-7xl mx-auto px-6 sm:px-8 grid grid-cols-1 lg:grid-cols-2 gap-10">
            <div>
                <p class="mb-3 text-sm font-medium tracking-wide uppercase text-brand-700">Quem somos</p>
                <h2 class="font-heading text-brand-900 text-3xl sm:text-4xl font-semibold mb-5">Sobre {{ $empresa->nome ?? config('app.name') }}</h2>
                <p class="text-muted leading-relaxed">
                    {{ $empresa->sobre_texto ?? 'Buscamos os melhores veículos do mercado para atender nossos clientes, com atendimento diferenciado, explicando todos os detalhes do veículo e esclarecendo todas as dúvidas.' }}
                </p>
            </div>
            <div class="rounded-2xl border border-brand-100 bg-white p-7 shadow-brand">
                <h3 class="font-heading font-medium text-brand-900 mb-4">Missão, Visão e Valores</h3>
                <ul class="space-y-3 text-sm text-muted">
                    <li><strong class="text-brand-900">Missão:</strong> Oferecer um serviço de qualidade, com total satisfação e confiança para o cliente.</li>
                    <li><strong class="text-brand-900">Visão:</strong> Ser referência no relacionamento com os clientes e na comercialização de veículos.</li>
                    <li><strong class="text-brand-900">Valores:</strong> Ética, transparência, respeito e compromisso com o resultado.</li>
                </ul>
            </div>
        </div>
    </section>

    <!-- ===== SIMULADOR DE FINANCIAMENTO ===== -->
    <section id="financiamento" class="bg-white py-24">
        <div class="max-w-2xl mx-auto px-6 text-center">
            <p class="mb-3 text-sm font-medium tracking-wide uppercase text-brand-700">Simulação</p>
            <h2 class="font-heading text-brand-900 text-3xl sm:text-4xl font-semibold mb-2">Simulador de Financiamento</h2>
            <p class="text-xs text-muted mb-10">* Valores sujeitos a análise de crédito</p>

            <form id="formSimulador" class="rounded-2xl border border-brand-100 bg-white p-6 sm:p-8 grid grid-cols-1 sm:grid-cols-3 gap-4 text-left shadow-brand">
                <div>
                    <label class="block text-xs text-muted mb-1">Valor do Carro</label>
                    <input type="number" id="simValor" placeholder="Ex: 50000" required class="input">
                </div>
                <div>
                    <label class="block text-xs text-muted mb-1">Entrada (R$)</label>
                    <input type="number" id="simEntrada" min="0" placeholder="Ex: 10000" required class="input">
                </div>
                <div>
                    <label class="block text-xs text-muted mb-1">Prazo (meses)</label>
                    <select id="simPrazo" class="input">
                        <option value="12">12 meses</option>
                        <option value="24" selected>24 meses</option>
                        <option value="36">36 meses</option>
                        <option value="48">48 meses</option>
                        <option value="60">60 meses</option>
                    </select>
                </div>
                <div class="sm:col-span-3 text-center mt-2">
                    <button type="submit" class="rounded-full bg-brand-700 px-8 py-3.5 text-sm font-semibold text-white shadow-brand transition-transform hover:scale-[1.03]">
                        Simular
                    </button>
                </div>
            </form>

            <div id="resultadoSimulador" class="mt-6 rounded-2xl border border-brand-100 bg-white p-6 text-left shadow-brand" style="display:none">
                <p class="flex justify-between text-sm text-muted py-1.5 border-b border-brand-100">
                    <span>Valor da Entrada</span> <span id="resEntrada" class="text-brand-900 font-medium tabular-nums">R$ 0,00</span>
                </p>
                <p class="flex justify-between text-sm text-muted py-1.5 border-b border-brand-100">
                    <span>Valor Financiado</span> <span id="resFinanciado" class="text-brand-900 font-medium tabular-nums">R$ 0,00</span>
                </p>
                <p class="flex justify-between text-base py-2">
                    <span class="text-brand-900 font-semibold">Parcela Mensal</span> <span id="resParcela" class="font-heading text-brand-700 font-semibold tabular-nums">R$ 0,00</span>
                </p>
                @if ($empresa?->whatsappUrl())
                    <a href="{{ $empresa->whatsappUrl() }}" target="_blank" rel="noopener"
                       class="mt-4 flex items-center justify-center gap-2 rounded-full bg-brand-500 px-6 py-3.5 text-sm font-semibold text-brand-900 shadow-brand transition-transform hover:scale-[1.03]">
                        Quero Mais Informações
                    </a>
                @endif
            </div>
        </div>
    </section>

    <!-- ===== VENDA SEU CARRO ===== -->
    <section id="venda-seu-carro" class="bg-brand-100/50 py-24">
        <div class="max-w-7xl mx-auto px-6 sm:px-8">
            <p class="mb-3 text-sm font-medium tracking-wide uppercase text-brand-700 text-center">Avaliação</p>
            <h2 class="font-heading text-brand-900 text-3xl sm:text-4xl font-semibold text-center mb-2">Venda seu Carro</h2>
            <p class="text-center text-muted mb-12 max-w-xl mx-auto">Avaliamos seu veículo de forma rápida e transparente. Preencha os dados e entraremos em contato.</p>

            @livewire('public.venda-seu-carro-form')
        </div>
    </section>

    <!-- ===== LOCALIZAÇÃO ===== -->
    <section id="contato" class="bg-white py-24">
        <div class="max-w-7xl mx-auto px-6 sm:px-8">
            <p class="mb-3 text-sm font-medium tracking-wide uppercase text-brand-700 text-center">Visite a loja</p>
            <h2 class="font-heading text-brand-900 text-3xl sm:text-4xl font-semibold text-center mb-2">Onde Estamos</h2>
            <p class="text-center text-muted mb-12">{{ $empresa?->endereco ?? 'Endereço a configurar em Configurações' }}</p>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="rounded-2xl overflow-hidden border border-brand-100 shadow-brand">
                    @if ($empresa?->endereco)
                        <iframe
                            src="https://maps.google.com/maps?q={{ urlencode($empresa->endereco.', '.$empresa->cidade.' - '.$empresa->uf) }}&output=embed&z=15"
                            width="100%" height="360" style="border:0" allowfullscreen loading="lazy"
                            title="Localização {{ $empresa->nome }}"></iframe>
                    @else
                        <div class="h-[360px] bg-brand-100/50 flex items-center justify-center text-muted text-sm">
                            Mapa aparece aqui quando o endereço for configurado
                        </div>
                    @endif
                </div>
                <div class="space-y-5">
                    <div class="flex items-start gap-4">
                        <span class="flex size-10 shrink-0 items-center justify-center rounded-xl text-white" style="background-image: linear-gradient(135deg, #1268A3 0%, #4FA8E8 55%, #7FD3F0 100%)"><x-heroicon-o-map-pin class="w-5 h-5" /></span>
                        <div><strong class="text-brand-900 block text-sm">Endereço</strong><p class="text-muted text-sm">{{ $empresa?->endereco ?? 'A configurar' }}</p></div>
                    </div>
                    <div class="flex items-start gap-4">
                        <span class="flex size-10 shrink-0 items-center justify-center rounded-xl text-white" style="background-image: linear-gradient(135deg, #1268A3 0%, #4FA8E8 55%, #7FD3F0 100%)"><x-heroicon-o-phone class="w-5 h-5" /></span>
                        <div><strong class="text-brand-900 block text-sm">Telefone</strong><p class="text-muted text-sm">
                            @if ($empresa?->telefone)<a href="tel:{{ $empresa->telefone }}" class="hover:text-brand-700">{{ $empresa->telefone }}</a>@else A configurar @endif
                        </p></div>
                    </div>
                    <div class="flex items-start gap-4">
                        <span class="flex size-10 shrink-0 items-center justify-center rounded-xl text-white" style="background-image: linear-gradient(135deg, #1268A3 0%, #4FA8E8 55%, #7FD3F0 100%)"><x-heroicon-o-chat-bubble-left-right class="w-5 h-5" /></span>
                        <div><strong class="text-brand-900 block text-sm">WhatsApp</strong><p class="text-muted text-sm">
                            @if ($empresa?->whatsappUrl())<a href="{{ $empresa->whatsappUrl() }}" target="_blank" rel="noopener" class="hover:text-brand-700">{{ $empresa->whatsapp }}</a>@else A configurar @endif
                        </p></div>
                    </div>
                    <div class="flex items-start gap-4">
                        <span class="flex size-10 shrink-0 items-center justify-center rounded-xl text-white" style="background-image: linear-gradient(135deg, #1268A3 0%, #4FA8E8 55%, #7FD3F0 100%)"><x-heroicon-o-clock class="w-5 h-5" /></span>
                        <div><strong class="text-brand-900 block text-sm">Horário</strong><p class="text-muted text-sm">{{ $empresa?->horario_funcionamento ?? 'A configurar' }}</p></div>
                    </div>
                    <div class="flex items-start gap-4">
                        <span class="flex size-10 shrink-0 items-center justify-center rounded-xl text-white" style="background-image: linear-gradient(135deg, #1268A3 0%, #4FA8E8 55%, #7FD3F0 100%)"><x-heroicon-o-envelope class="w-5 h-5" /></span>
                        <div><strong class="text-brand-900 block text-sm">E-mail</strong><p class="text-muted text-sm">
                            @if ($empresa?->email_contato)<a href="mailto:{{ $empresa->email_contato }}" class="hover:text-brand-700">{{ $empresa->email_contato }}</a>@else A configurar @endif
                        </p></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('formSimulador');
            const resultado = document.getElementById('resultadoSimulador');
            if (!form || !resultado) return;

            const TAXA_JUROS_MENSAL = 0.0149; // 1,49% ao mês (CDC)

            function calcularParcela(valorFinanciado, prazo) {
                const taxa = TAXA_JUROS_MENSAL;
                return valorFinanciado * (taxa * Math.pow(1 + taxa, prazo)) / (Math.pow(1 + taxa, prazo) - 1);
            }

            function formatarMoeda(valor) {
                return valor.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
            }

            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const valorCarro = parseFloat(document.getElementById('simValor').value);
                const valorEntrada = parseFloat(document.getElementById('simEntrada').value) || 0;
                const prazo = parseInt(document.getElementById('simPrazo').value);

                if (!valorCarro || valorCarro <= 0) {
                    document.getElementById('simValor').focus();
                    return;
                }

                const valorFinanciado = Math.max(valorCarro - valorEntrada, 0);
                const valorParcela = calcularParcela(valorFinanciado, prazo);

                document.getElementById('resEntrada').textContent = formatarMoeda(valorEntrada);
                document.getElementById('resFinanciado').textContent = formatarMoeda(valorFinanciado);
                document.getElementById('resParcela').textContent = formatarMoeda(valorParcela);

                resultado.style.display = 'block';
                resultado.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            });
        });
    </script>
</x-layouts.public>
