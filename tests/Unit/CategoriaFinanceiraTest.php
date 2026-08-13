<?php

use App\Livewire\Financeiro\Categorias;
use App\Models\CategoriaFinanceira;
use App\Models\Empresa;
use Database\Seeders\CategoriaFinanceiraSeeder;
use Illuminate\Support\Str;
use Livewire\Livewire;

uses(Tests\TestCase::class);

beforeEach(fn () => usarMysqlRealDeDev());

it('cria categoria principal, subcategoria, e monta o nome completo', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $sufixo = Str::random(6);
    $nomePai = 'PaiTeste'.$sufixo;
    $nomeSub = 'SubTeste'.$sufixo;

    Livewire::actingAs($user)->test(Categorias::class)
        ->call('novo')
        ->set('nome', $nomePai)
        ->set('tipo', 'despesa')
        ->call('salvar')
        ->assertHasNoErrors();

    $pai = CategoriaFinanceira::where('nome', $nomePai)->first();
    expect($pai)->not->toBeNull()
        ->and($pai->categoria_pai_id)->toBeNull()
        ->and($pai->nomeCompleto())->toBe($nomePai);

    Livewire::actingAs($user)->test(Categorias::class)
        ->call('novo', $pai->id)
        ->assertSet('categoria_pai_id', $pai->id)
        ->assertSet('tipo', 'despesa')
        ->set('nome', $nomeSub)
        ->call('salvar')
        ->assertHasNoErrors();

    $sub = CategoriaFinanceira::where('nome', $nomeSub)->first();
    expect($sub)->not->toBeNull()
        ->and($sub->categoria_pai_id)->toBe($pai->id)
        ->and($sub->nomeCompleto())->toBe("{$nomePai} / {$nomeSub}")
        ->and($pai->subcategorias()->count())->toBe(1);

    $sub->delete();
    $pai->delete();
});

it('bloqueia excluir categoria que ainda tem subcategoria', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $sufixo = Str::random(6);
    $pai = CategoriaFinanceira::create(['nome' => 'PaiBloqueio'.$sufixo, 'tipo' => 'despesa']);
    $sub = CategoriaFinanceira::create(['nome' => 'SubBloqueio'.$sufixo, 'tipo' => 'despesa', 'categoria_pai_id' => $pai->id]);

    Livewire::actingAs($user)->test(Categorias::class)->call('excluir', $pai->id);

    expect(CategoriaFinanceira::find($pai->id))->not->toBeNull('categoria pai nao deveria ter sido excluida');

    $sub->delete();
    $pai->delete();
});

it('roda o seeder de plano de contas padrao de forma idempotente', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);

    (new CategoriaFinanceiraSeeder)->run();
    $totalAposPrimeiraRodada = CategoriaFinanceira::count();

    (new CategoriaFinanceiraSeeder)->run();
    $totalAposSegundaRodada = CategoriaFinanceira::count();

    expect($totalAposSegundaRodada)->toBe($totalAposPrimeiraRodada)
        ->and(CategoriaFinanceira::where('nome', 'Despesas administrativas')->count())->toBe(1);

    $despesasAdmin = CategoriaFinanceira::where('nome', 'Despesas administrativas')->first();
    expect(CategoriaFinanceira::where('nome', 'Salários e comissões')->first()->categoria_pai_id)->toBe($despesasAdmin->id);
});
