<?php

use App\Services\Cep\ConsultaCepException;
use App\Services\Cep\ConsultaCepService;
use Illuminate\Support\Facades\Http;

// Sem RefreshDatabase de propósito: o service não toca banco.
uses(Tests\TestCase::class);

it('normaliza a resposta do viacep pro endereco usado no cadastro de cliente', function () {
    Http::fake([
        'viacep.com.br/ws/*' => Http::response([
            'cep' => '01001-000',
            'logradouro' => 'Praça da Sé',
            'complemento' => 'lado ímpar',
            'bairro' => 'Sé',
            'localidade' => 'São Paulo',
            'uf' => 'SP',
        ]),
    ]);

    $dados = (new ConsultaCepService)->consultar('01001-000');

    expect($dados)->toBe([
        'endereco' => 'Praça da Sé - Sé',
        'cidade' => 'São Paulo',
        'uf' => 'SP',
    ]);
});

it('lanca excecao clara quando o cep nao e encontrado', function () {
    Http::fake([
        'viacep.com.br/ws/*' => Http::response(['erro' => true]),
    ]);

    expect(fn () => (new ConsultaCepService)->consultar('00000-000'))
        ->toThrow(ConsultaCepException::class, 'CEP não encontrado.');
});

it('lanca excecao clara quando o cep tem formato invalido', function () {
    expect(fn () => (new ConsultaCepService)->consultar('123'))
        ->toThrow(ConsultaCepException::class, 'CEP inválido. Use o formato 00000-000.');
});
