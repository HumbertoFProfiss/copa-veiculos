<x-layouts.public-home :title="($empresa->nome ?? config('app.name')).' - Loja de Carros Online'" :empresa="$empresa">

    @php
        $heroFotos = $destaques->flatMap(fn ($v) => $v->fotos->take(1))->take(5);
    @endphp

    <!-- ===== HERO ===== -->
    <section id="home" class="relative min-h-[600px] flex items-center overflow-hidden">
        <div class="absolute inset-0">
            @forelse ($heroFotos as $foto)
                <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ $foto->url() }}')"></div>
            @empty
                <div class="absolute inset-0 bg-gradient-to-br from-night-soft to-night"></div>
            @endforelse
            <div class="absolute inset-0 bg-gradient-to-t from-night via-night/80 to-night/40"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-6 py-24 w-full">
            <div class="max-w-xl">
                <h1 class="text-4xl sm:text-5xl font-bold text-white leading-tight">Aqui você encontra o carro certo!</h1>
                <p class="mt-4 text-lg text-white/70">Multimarcas zero KM e Seminovos. Encontre o carro perfeito para você.</p>

                <div class="flex flex-wrap gap-3 mt-8">
                    <a href="{{ route('estoque') }}"
                       class="inline-flex items-center gap-2 px-6 py-3 rounded-control bg-gold text-night font-semibold hover:bg-gold-dark transition-colors">
                        Ver Estoque Completo
                    </a>
                    <a href="#financiamento"
                       class="inline-flex items-center gap-2 px-6 py-3 rounded-control border border-white/30 text-white font-semibold hover:border-gold hover:text-gold transition-colors">
                        Simular Financiamento
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== DESTAQUES DA SEMANA ===== -->
    <section class="py-16 border-t border-night-border">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="text-2xl sm:text-3xl font-bold text-white text-center mb-10">Destaques da Semana</h2>

            @if ($destaques->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($destaques as $veiculo)
                        <a href="{{ route('veiculo.show', $veiculo) }}"
                           class="group block bg-night-card border border-night-border rounded-xl overflow-hidden hover:border-gold/50 transition-colors">
                            <div class="relative aspect-[4/3] bg-night-soft overflow-hidden">
                                @if ($veiculo->fotos->isNotEmpty())
                                    <img src="{{ $veiculo->fotos->first()->url() }}" alt="{{ $veiculo->marca }} {{ $veiculo->modelo }}"
                                         loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-white/20">
                                        <x-heroicon-o-photo class="w-12 h-12" />
                                    </div>
                                @endif
                                <span class="absolute top-3 left-3 px-2.5 py-1 rounded-control bg-gold text-night text-xs font-semibold">Destaque</span>
                            </div>
                            <div class="p-5">
                                <h3 class="font-semibold text-white">{{ $veiculo->marca }} {{ $veiculo->modelo }}</h3>
                                <p class="text-sm text-white/50 mt-0.5">{{ $veiculo->ano_fabricacao }}/{{ $veiculo->ano_modelo }}</p>
                                <div class="flex items-center gap-3 mt-2 text-xs text-white/50">
                                    <span>{{ number_format($veiculo->km, 0, ',', '.') }} km</span>
                                    <span>&middot;</span>
                                    <span class="capitalize">{{ $veiculo->combustivel }}</span>
                                </div>
                                <p class="mt-3 text-xl font-bold text-gold tabular-nums">
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
                <p class="text-center text-white/40">Nenhum destaque disponível no momento.</p>
            @endif
        </div>
    </section>

    <!-- ===== ÚLTIMAS ADIÇÕES ===== -->
    @if ($ultimasAdicoes->isNotEmpty())
        <section class="py-16 bg-night-soft border-t border-night-border">
            <div class="max-w-7xl mx-auto px-6">
                <h2 class="text-2xl sm:text-3xl font-bold text-white text-center mb-10">Últimas Adições</h2>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    @foreach ($ultimasAdicoes as $veiculo)
                        <a href="{{ route('veiculo.show', $veiculo) }}"
                           class="group block bg-night-card border border-night-border rounded-xl overflow-hidden hover:border-gold/50 transition-colors">
                            <div class="aspect-[4/3] bg-night overflow-hidden">
                                @if ($veiculo->fotos->isNotEmpty())
                                    <img src="{{ $veiculo->fotos->first()->url() }}" alt="{{ $veiculo->marca }} {{ $veiculo->modelo }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-white/20">
                                        <x-heroicon-o-photo class="w-10 h-10" />
                                    </div>
                                @endif
                            </div>
                            <div class="p-4">
                                <h3 class="font-semibold text-white text-sm">{{ $veiculo->marca }} {{ $veiculo->modelo }}</h3>
                                <p class="text-gold font-bold mt-1 tabular-nums">
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

                <div class="text-center mt-10">
                    <a href="{{ route('estoque') }}"
                       class="inline-flex items-center gap-2 px-6 py-3 rounded-control bg-gold text-night font-semibold hover:bg-gold-dark transition-colors">
                        Ver Todo o Estoque ({{ $totalEstoque }})
                    </a>
                </div>
            </div>
        </section>
    @endif

    <!-- ===== DIFERENCIAIS ===== -->
    <section class="py-16 border-t border-night-border">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="text-2xl sm:text-3xl font-bold text-white text-center mb-10">Sobre Nós</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-night-card border border-night-border rounded-xl p-6 text-center">
                    <div class="w-12 h-12 rounded-full bg-gold/10 text-gold flex items-center justify-center mx-auto mb-4">
                        <x-heroicon-o-banknotes class="w-6 h-6" />
                    </div>
                    <h3 class="font-semibold text-white mb-2">Financiamento</h3>
                    <p class="text-sm text-white/50">Financie a compra do seu veículo. O carro dos seus sonhos pode ser seu.</p>
                </div>
                <div class="bg-night-card border border-night-border rounded-xl p-6 text-center">
                    <div class="w-12 h-12 rounded-full bg-gold/10 text-gold flex items-center justify-center mx-auto mb-4">
                        <x-heroicon-o-truck class="w-6 h-6" />
                    </div>
                    <h3 class="font-semibold text-white mb-2">Venda seu veículo</h3>
                    <p class="text-sm text-white/50">A maneira mais rápida de vender o seu carro. Velocidade e segurança na hora de negociar.</p>
                </div>
                <div class="bg-night-card border border-night-border rounded-xl p-6 text-center">
                    <div class="w-12 h-12 rounded-full bg-gold/10 text-gold flex items-center justify-center mx-auto mb-4">
                        <x-heroicon-o-building-storefront class="w-6 h-6" />
                    </div>
                    <h3 class="font-semibold text-white mb-2">Empresa</h3>
                    <p class="text-sm text-white/50">
                        Somos uma empresa familiar
                        @if ($empresa?->cidade)
                            , localizada em {{ $empresa->cidade }}@if($empresa->uf) — {{ $empresa->uf }} @endif.
                        @else
                            . Cidade a configurar.
                        @endif
                    </p>
                </div>
                <div class="bg-night-card border border-night-border rounded-xl p-6 text-center">
                    <div class="w-12 h-12 rounded-full bg-gold/10 text-gold flex items-center justify-center mx-auto mb-4">
                        <x-heroicon-o-chat-bubble-left-right class="w-6 h-6" />
                    </div>
                    <h3 class="font-semibold text-white mb-2">Fale conosco</h3>
                    <p class="text-sm text-white/50">Precisa falar conosco? Envie um formulário ou nos ligue. Estamos esperando por você.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== SOBRE ===== -->
    <section class="py-16 bg-night-soft border-t border-night-border">
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 lg:grid-cols-2 gap-10">
            <div>
                <h2 class="text-2xl sm:text-3xl font-bold text-white mb-5">Sobre {{ $empresa->nome ?? config('app.name') }}</h2>
                <p class="text-white/60 leading-relaxed">
                    {{ $empresa->sobre_texto ?? 'Buscamos os melhores veículos do mercado para atender nossos clientes, com atendimento diferenciado, explicando todos os detalhes do veículo e esclarecendo todas as dúvidas.' }}
                </p>
            </div>
            <div class="bg-night-card border border-night-border rounded-xl p-6">
                <h3 class="font-semibold text-white mb-4">Missão, Visão e Valores</h3>
                <ul class="space-y-3 text-sm text-white/60">
                    <li><strong class="text-white">Missão:</strong> Oferecer um serviço de qualidade, com total satisfação e confiança para o cliente.</li>
                    <li><strong class="text-white">Visão:</strong> Ser referência no relacionamento com os clientes e na comercialização de veículos.</li>
                    <li><strong class="text-white">Valores:</strong> Ética, transparência, respeito e compromisso com o resultado.</li>
                </ul>
            </div>
        </div>
    </section>

    <!-- ===== SIMULADOR DE FINANCIAMENTO ===== -->
    <section id="financiamento" class="py-16 border-t border-night-border">
        <div class="max-w-2xl mx-auto px-6 text-center">
            <h2 class="text-2xl sm:text-3xl font-bold text-white mb-2">Simulador de Financiamento</h2>
            <p class="text-xs text-white/40 mb-8">* Valores sujeitos a análise de crédito</p>

            <form id="formSimulador" class="bg-night-card border border-night-border rounded-xl p-6 sm:p-8 grid grid-cols-1 sm:grid-cols-3 gap-4 text-left">
                <div>
                    <label class="block text-xs text-white/60 mb-1">Valor do Carro</label>
                    <input type="number" id="simValor" placeholder="Ex: 50000" required
                           class="w-full rounded-control bg-night border border-night-border-light text-white placeholder-white/30 text-sm focus:border-gold focus:ring-gold">
                </div>
                <div>
                    <label class="block text-xs text-white/60 mb-1">Entrada (R$)</label>
                    <input type="number" id="simEntrada" min="0" placeholder="Ex: 10000" required
                           class="w-full rounded-control bg-night border border-night-border-light text-white placeholder-white/30 text-sm focus:border-gold focus:ring-gold">
                </div>
                <div>
                    <label class="block text-xs text-white/60 mb-1">Prazo (meses)</label>
                    <select id="simPrazo" class="w-full rounded-control bg-night border border-night-border-light text-white text-sm focus:border-gold focus:ring-gold">
                        <option value="12">12 meses</option>
                        <option value="24" selected>24 meses</option>
                        <option value="36">36 meses</option>
                        <option value="48">48 meses</option>
                        <option value="60">60 meses</option>
                    </select>
                </div>
                <div class="sm:col-span-3 text-center mt-2">
                    <button type="submit" class="px-8 py-3 rounded-control bg-gold text-night font-semibold text-sm hover:bg-gold-dark transition-colors">
                        Simular
                    </button>
                </div>
            </form>

            <div id="resultadoSimulador" class="mt-6 bg-night-card border border-gold/30 rounded-xl p-6 text-left" style="display:none">
                <p class="flex justify-between text-sm text-white/60 py-1.5 border-b border-night-border">
                    <span>Valor da Entrada</span> <span id="resEntrada" class="text-white font-medium tabular-nums">R$ 0,00</span>
                </p>
                <p class="flex justify-between text-sm text-white/60 py-1.5 border-b border-night-border">
                    <span>Valor Financiado</span> <span id="resFinanciado" class="text-white font-medium tabular-nums">R$ 0,00</span>
                </p>
                <p class="flex justify-between text-base py-2">
                    <span class="text-white font-semibold">Parcela Mensal</span> <span id="resParcela" class="text-gold font-bold tabular-nums">R$ 0,00</span>
                </p>
                @if ($empresa?->whatsappUrl())
                    <a href="{{ $empresa->whatsappUrl() }}" target="_blank" rel="noopener"
                       class="mt-4 flex items-center justify-center gap-2 px-6 py-3 rounded-control bg-gold text-night font-semibold text-sm hover:bg-gold-dark transition-colors">
                        Quero Mais Informações
                    </a>
                @endif
            </div>
        </div>
    </section>

    <!-- ===== VENDA SEU CARRO ===== -->
    <section id="venda-seu-carro" class="py-16 bg-night-soft border-t border-night-border">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="text-2xl sm:text-3xl font-bold text-white text-center mb-2">Venda seu Carro</h2>
            <p class="text-center text-white/50 mb-10 max-w-xl mx-auto">Avaliamos seu veículo de forma rápida e transparente. Preencha os dados e entraremos em contato.</p>

            @livewire('public.venda-seu-carro-form')
        </div>
    </section>

    <!-- ===== LOCALIZAÇÃO ===== -->
    <section id="contato" class="py-16 border-t border-night-border">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="text-2xl sm:text-3xl font-bold text-white text-center mb-2">Onde Estamos</h2>
            <p class="text-center text-white/50 mb-10">{{ $empresa?->endereco ?? 'Endereço a configurar em Configurações' }}</p>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="rounded-xl overflow-hidden border border-night-border">
                    @if ($empresa?->endereco)
                        <iframe
                            src="https://maps.google.com/maps?q={{ urlencode($empresa->endereco.', '.$empresa->cidade.' - '.$empresa->uf) }}&output=embed&z=15"
                            width="100%" height="360" style="border:0" allowfullscreen loading="lazy"
                            title="Localização {{ $empresa->nome }}"></iframe>
                    @else
                        <div class="h-[360px] bg-night-card flex items-center justify-center text-white/30 text-sm">
                            Mapa aparece aqui quando o endereço for configurado
                        </div>
                    @endif
                </div>
                <div class="space-y-5">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 shrink-0 rounded-control bg-gold/10 text-gold flex items-center justify-center"><x-heroicon-o-map-pin class="w-5 h-5" /></div>
                        <div><strong class="text-white block text-sm">Endereço</strong><p class="text-white/50 text-sm">{{ $empresa?->endereco ?? 'A configurar' }}</p></div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 shrink-0 rounded-control bg-gold/10 text-gold flex items-center justify-center"><x-heroicon-o-phone class="w-5 h-5" /></div>
                        <div><strong class="text-white block text-sm">Telefone</strong><p class="text-white/50 text-sm">
                            @if ($empresa?->telefone)<a href="tel:{{ $empresa->telefone }}" class="hover:text-gold">{{ $empresa->telefone }}</a>@else A configurar @endif
                        </p></div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 shrink-0 rounded-control bg-gold/10 text-gold flex items-center justify-center"><x-heroicon-o-chat-bubble-left-right class="w-5 h-5" /></div>
                        <div><strong class="text-white block text-sm">WhatsApp</strong><p class="text-white/50 text-sm">
                            @if ($empresa?->whatsappUrl())<a href="{{ $empresa->whatsappUrl() }}" target="_blank" rel="noopener" class="hover:text-gold">{{ $empresa->whatsapp }}</a>@else A configurar @endif
                        </p></div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 shrink-0 rounded-control bg-gold/10 text-gold flex items-center justify-center"><x-heroicon-o-clock class="w-5 h-5" /></div>
                        <div><strong class="text-white block text-sm">Horário</strong><p class="text-white/50 text-sm">{{ $empresa?->horario_funcionamento ?? 'A configurar' }}</p></div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 shrink-0 rounded-control bg-gold/10 text-gold flex items-center justify-center"><x-heroicon-o-envelope class="w-5 h-5" /></div>
                        <div><strong class="text-white block text-sm">E-mail</strong><p class="text-white/50 text-sm">
                            @if ($empresa?->email_contato)<a href="mailto:{{ $empresa->email_contato }}" class="hover:text-gold">{{ $empresa->email_contato }}</a>@else A configurar @endif
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
</x-layouts.public-home>
