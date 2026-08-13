<?php

use App\Livewire\Configuracoes\Index as ConfiguracoesIndex;
use App\Models\Empresa;
use App\Models\Fatura;
use App\Services\Faturamento\GeradorFaturas;
use Illuminate\Support\Facades\Artisan;
use Livewire\Livewire;

uses(Tests\TestCase::class);

beforeEach(function () {
    usarMysqlRealDeDev();
    // Limpeza defensiva: faturas:gerar cria pra TODA empresa ativa de uma vez,
    // entao qualquer teste que chame o comando precisa garantir que nao
    // sobra fatura de outra empresa poluindo a unique(empresa_id, referencia)
    // pra proxima execucao da suite inteira.
    Fatura::withoutGlobalScopes()->whereDate('referencia', now()->startOfMonth())->delete();
});

it('gera a fatura do mes com o valor certo do plano e nao duplica se rodar de novo', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);

    Fatura::where('empresa_id', $empresa->id)->whereDate('referencia', now()->startOfMonth())->delete();

    $gerador = new GeradorFaturas;

    $fatura = $gerador->gerarProximaFatura($empresa);
    expect($fatura)->not->toBeNull()
        ->and((float) $fatura->valor)->toBe(Empresa::PRECOS_PLANOS[$empresa->plano])
        ->and($fatura->status)->toBe('pendente')
        ->and($fatura->vencimento->format('Y-m-d'))->toBe(now()->startOfMonth()->addDays(9)->format('Y-m-d'));

    $segunda = $gerador->gerarProximaFatura($empresa);
    expect($segunda)->toBeNull();

    expect(Fatura::where('empresa_id', $empresa->id)->whereDate('referencia', now()->startOfMonth())->count())->toBe(1);

    $fatura->delete();
});

it('comando faturas:gerar cria fatura pra empresa ativa e o comando faturas:marcar-atrasadas marca vencidas', function () {
    $empresa = Empresa::where('status', 'ativo')->first();
    app()->instance('tenant', $empresa);

    Fatura::where('empresa_id', $empresa->id)->whereDate('referencia', now()->startOfMonth())->delete();

    Artisan::call('faturas:gerar');
    $saida = Artisan::output();
    expect($saida)->toContain('fatura(s) gerada(s)');

    $fatura = Fatura::where('empresa_id', $empresa->id)->whereDate('referencia', now()->startOfMonth())->first();
    expect($fatura)->not->toBeNull();

    // Forca vencimento no passado pra testar a marcacao de atrasada.
    $fatura->update(['vencimento' => now()->subDays(3)]);

    Artisan::call('faturas:marcar-atrasadas');
    $fatura->refresh();
    expect($fatura->status)->toBe('atrasada');

    $fatura->delete();
});

it('comando faturas:marcar-paga da baixa manual corretamente', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);

    $fatura = Fatura::create([
        'empresa_id' => $empresa->id,
        'plano' => $empresa->plano,
        'valor' => 300,
        'referencia' => now()->addMonth()->startOfMonth(),
        'vencimento' => now()->addMonth()->startOfMonth()->addDays(9),
        'status' => 'pendente',
    ]);

    Artisan::call('faturas:marcar-paga', ['fatura' => $fatura->id, '--forma' => 'pix']);
    $saida = Artisan::output();
    expect($saida)->toContain('marcada como paga');

    $fatura->refresh();
    expect($fatura->status)->toBe('paga')
        ->and($fatura->forma_pagamento)->toBe('pix')
        ->and($fatura->paga_em)->not->toBeNull();

    $fatura->delete();
});

it('a tela de configuracoes mostra o plano, o valor e o historico de faturas do tenant', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $fatura = Fatura::create([
        'empresa_id' => $empresa->id,
        'plano' => $empresa->plano,
        'valor' => 300,
        'referencia' => now()->startOfMonth(),
        'vencimento' => now()->startOfMonth()->addDays(9),
        'status' => 'pendente',
    ]);

    Livewire::actingAs($user)
        ->test(ConfiguracoesIndex::class)
        ->assertSee('Assinatura')
        ->assertSee('R$ 300,00')
        ->assertSee('Pendente');

    $fatura->delete();
});
