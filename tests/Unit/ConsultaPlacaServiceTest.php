<?php

use App\Services\ConsultaPlaca\ConsultaPlacaException;
use App\Services\ConsultaPlaca\ConsultaPlacaService;
use Illuminate\Support\Facades\Http;

// Sem RefreshDatabase de propósito: o service não toca banco, e as
// migrations do projeto têm DDL exclusivo de MySQL (ALTER ... MODIFY) que
// quebra no sqlite :memory: usado por padrão nos testes.
uses(Tests\TestCase::class);

it('lanca excecao clara quando o token nao esta configurado', function () {
    config(['services.apiplacas.token' => null]);

    expect(fn () => (new ConsultaPlacaService)->consultar('INT8C36'))
        ->toThrow(ConsultaPlacaException::class, 'Token da API de consulta de placa não configurado.');
});

it('normaliza a resposta da api pro formato usado no formulario de veiculo, escolhendo a fipe de maior score', function () {
    config(['services.apiplacas.token' => 'token-de-teste']);

    Http::fake([
        'wdapi2.com.br/consulta/*' => Http::response([
            'marca' => 'VW',
            'modelo' => 'CROSSFOX',
            'VERSAO' => 'CROSSFOX',
            'ano' => '2007',
            'anoModelo' => '2007',
            'chassi' => '*****10137',
            'cor' => 'Prata',
            'situacao' => 'Sem restrição',
            'extra' => [
                'ano_fabricacao' => '2007',
                'combustivel' => 'Alcool / Gasolina',
                'caixa_cambio' => '',
            ],
            'fipe' => [
                'dados' => [
                    ['score' => 80, 'texto_valor' => 'R$ 20.000,00', 'mes_referencia' => 'abril de 2022'],
                    ['score' => 101, 'texto_valor' => 'R$ 28.799,00', 'mes_referencia' => 'maio de 2022'],
                ],
            ],
        ], 200),
    ]);

    $dados = (new ConsultaPlacaService)->consultar('int-8c36');

    Http::assertSent(fn ($request) => $request->url() === 'https://wdapi2.com.br/consulta/INT8C36/token-de-teste');

    expect($dados)->toMatchArray([
        'marca' => 'VW',
        'modelo' => 'CROSSFOX',
        'versao' => 'CROSSFOX',
        'ano_fabricacao' => 2007,
        'ano_modelo' => 2007,
        'cor' => 'Prata',
        'combustivel' => 'Alcool / Gasolina',
        'cambio' => null,
        'numero_chassi' => '*****10137',
        'preco_tabela_fipe' => 28799.0,
        'fipe_referencia' => 'maio de 2022',
        'restricao' => false,
    ]);
});

it('marca restricao quando a situacao nao e "sem restricao"', function () {
    config(['services.apiplacas.token' => 'token-de-teste']);

    Http::fake([
        'wdapi2.com.br/consulta/*' => Http::response([
            'marca' => 'VW',
            'modelo' => 'GOL',
            'situacao' => 'Roubo/Furto',
        ], 200),
    ]);

    $dados = (new ConsultaPlacaService)->consultar('ABC1234');

    expect($dados['restricao'])->toBeTrue()
        ->and($dados['situacao'])->toBe('Roubo/Furto');
});

it('traduz codigos de erro da api pra mensagens claras', function () {
    config(['services.apiplacas.token' => 'token-de-teste']);

    Http::fake([
        'wdapi2.com.br/consulta/*' => Http::response(['message' => 'Placa Invalida'], 401),
    ]);

    expect(fn () => (new ConsultaPlacaService)->consultar('XXX0000'))
        ->toThrow(ConsultaPlacaException::class, 'Placa inválida. Use o formato AAA0A00 ou AAA0000.');
});
