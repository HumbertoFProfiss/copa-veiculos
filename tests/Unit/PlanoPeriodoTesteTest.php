<?php

use App\Livewire\Configuracoes\Index as ConfiguracoesIndex;
use App\Models\Empresa;
use Livewire\Livewire;

uses(Tests\TestCase::class);

beforeEach(fn () => usarMysqlRealDeDev());

it('o plano completo_opcionais custa R$0 e e exibido como periodo de teste com a contagem regressiva de dias', function () {
    expect(Empresa::PRECOS_PLANOS['completo_opcionais'])->toBe(0.00);

    $empresa = Empresa::where('plano', 'completo_opcionais')->firstOrFail();
    $trialOriginal = $empresa->trial_termina_em;

    $empresa->update(['trial_termina_em' => now()->addDays(7)]);

    expect($empresa->nomePlanoExibicao())->toBe('Período de teste - 7 dias')
        ->and($empresa->diasRestantesTrial())->toBe(7);

    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    Livewire::actingAs($user)->test(ConfiguracoesIndex::class)
        ->assertSee('Período de teste - 7 dias')
        ->assertSee('R$ 0,00')
        ->assertSee('7 dias restantes de teste');

    $empresa->update(['trial_termina_em' => $trialOriginal]);
});

it('mostra periodo de teste encerrado quando a data ja passou', function () {
    $empresa = Empresa::where('plano', 'completo_opcionais')->firstOrFail();
    $trialOriginal = $empresa->trial_termina_em;

    $empresa->update(['trial_termina_em' => now()->subDay()]);

    expect($empresa->diasRestantesTrial())->toBe(0);

    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    Livewire::actingAs($user)->test(ConfiguracoesIndex::class)
        ->assertSee('Período de teste encerrado');

    $empresa->update(['trial_termina_em' => $trialOriginal]);
});

it('plano completo continua com nome e preco normais, sem contagem de teste', function () {
    $empresa = Empresa::where('plano', 'completo')->firstOrFail();

    expect($empresa->nomePlanoExibicao())->toBe('Completo')
        ->and($empresa->diasRestantesTrial())->toBeNull();
});

it('empresa nova no plano completo_opcionais ja nasce com 7 dias de teste definidos automaticamente', function () {
    $empresa = Empresa::create([
        'nome' => 'Empresa Trial Nova Teste',
        'slug' => 'trial-nova-teste-'.\Illuminate\Support\Str::random(8),
        'plano' => 'completo_opcionais',
        'status' => 'ativo',
    ]);

    expect($empresa->trial_termina_em)->not->toBeNull()
        ->and($empresa->diasRestantesTrial())->toBe(7);

    $empresa->delete();
});
