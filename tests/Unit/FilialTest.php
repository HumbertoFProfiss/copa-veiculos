<?php

use App\Livewire\Filiais\Index as FiliaisIndex;
use App\Livewire\Veiculos\Index as VeiculosIndex;
use App\Models\Empresa;
use App\Models\Filial;
use App\Models\User;
use App\Models\Veiculo;
use Illuminate\Support\Str;
use Livewire\Livewire;

uses(Tests\TestCase::class);

beforeEach(fn () => usarMysqlRealDeDev());

it('toda empresa ja tem uma filial Matriz e todo veiculo/usuario existente esta associado a ela (backfill)', function () {
    $empresa = Empresa::first();

    $matriz = Filial::where('empresa_id', $empresa->id)->where('principal', true)->first();
    expect($matriz)->not->toBeNull()->and($matriz->nome)->toBe('Matriz');

    // withoutGlobalScope so do EmpresaScope (nao do SoftDeletingScope, senao
    // conta lixo ja soft-deletado de outros testes como se estivesse ativo).
    expect(Veiculo::withoutGlobalScope(\App\Models\Concerns\EmpresaScope::class)->where('empresa_id', $empresa->id)->whereNull('filial_id')->count())->toBe(0);
    expect(User::withoutGlobalScope(\App\Models\Concerns\EmpresaScope::class)->where('empresa_id', $empresa->id)->whereNull('filial_id')->count())->toBe(0);
});

it('cria uma segunda filial via CRUD, atribui um veiculo a ela e o filtro da listagem funciona', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    Livewire::actingAs($user)
        ->test(FiliaisIndex::class)
        ->call('novo')
        ->set('nome', 'Filial Teste '.Str::random(4))
        ->set('cidade', 'Botucatu')
        ->set('uf', 'SP')
        ->call('salvar')
        ->assertHasNoErrors();

    $filial = Filial::where('empresa_id', $empresa->id)->where('nome', 'like', 'Filial Teste%')->first();
    expect($filial)->not->toBeNull();

    $veiculo = Veiculo::create([
        'empresa_id' => $empresa->id,
        'filial_id' => $filial->id,
        'marca' => 'FilialTesteMarca'.Str::random(4),
        'modelo' => 'Modelo',
        'slug' => Str::slug('filial-teste-'.Str::random(8)),
        'km' => 0,
        'portas' => 4,
        'tipo_propriedade' => 'proprio',
        'data_entrada' => now(),
        'status' => 'disponivel',
    ]);

    // O filtro por filial na listagem de veiculos so retorna o que pertence a ela.
    Livewire::actingAs($user)
        ->test(VeiculosIndex::class)
        ->set('filtroFilial', (string) $filial->id)
        ->assertSee($veiculo->marca);

    $matrizOutraFilial = Filial::where('empresa_id', $empresa->id)->where('principal', true)->first();
    Livewire::actingAs($user)
        ->test(VeiculosIndex::class)
        ->set('filtroFilial', (string) $matrizOutraFilial->id)
        ->assertDontSee($veiculo->marca);

    $veiculo->delete();
    $filial->delete();
});

it('nao deixa excluir a filial principal nem uma filial com veiculos vinculados', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $matriz = Filial::where('empresa_id', $empresa->id)->where('principal', true)->first();

    Livewire::actingAs($user)
        ->test(FiliaisIndex::class)
        ->call('excluir', $matriz->id)
        ->assertHasErrors(['geral']);

    expect(Filial::find($matriz->id))->not->toBeNull();

    $filial = Filial::create(['empresa_id' => $empresa->id, 'nome' => 'Filial Com Veiculo '.Str::random(4), 'ativa' => true]);
    $veiculo = Veiculo::create([
        'empresa_id' => $empresa->id,
        'filial_id' => $filial->id,
        'marca' => 'X', 'modelo' => 'Y',
        'slug' => Str::slug('filial-excluir-teste-'.Str::random(8)),
        'km' => 0, 'portas' => 4, 'tipo_propriedade' => 'proprio',
        'data_entrada' => now(), 'status' => 'disponivel',
    ]);

    Livewire::actingAs($user)
        ->test(FiliaisIndex::class)
        ->call('excluir', $filial->id)
        ->assertHasErrors(['geral']);

    expect(Filial::find($filial->id))->not->toBeNull();

    $veiculo->delete();
    $filial->delete();
});

it('a venda herda automaticamente a filial do veiculo vendido', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);

    $filial = Filial::create(['empresa_id' => $empresa->id, 'nome' => 'Filial Venda Teste '.Str::random(4), 'ativa' => true]);

    $veiculo = Veiculo::create([
        'empresa_id' => $empresa->id,
        'filial_id' => $filial->id,
        'marca' => 'X', 'modelo' => 'Y',
        'slug' => Str::slug('filial-venda-teste-'.Str::random(8)),
        'km' => 0, 'portas' => 4, 'tipo_propriedade' => 'proprio',
        'data_entrada' => now(), 'status' => 'disponivel', 'preco_venda' => 50000,
    ]);

    $cliente = \App\Models\Cliente::create(['empresa_id' => $empresa->id, 'nome' => 'Cliente Teste Filial '.Str::random(4)]);
    $user = usuarioProprietario($empresa);

    Livewire::actingAs($user)
        ->test(\App\Livewire\Vendas\Nova::class)
        ->set('veiculo_id', $veiculo->id)
        ->set('cliente_id', $cliente->id)
        ->set('vendedor_id', $user->id)
        ->set('preco_venda', 50000)
        ->set('status', 'confirmada')
        ->call('salvar');

    $venda = \App\Models\Venda::where('veiculo_id', $veiculo->id)->first();
    expect($venda)->not->toBeNull()->and($venda->filial_id)->toBe($filial->id);

    $venda->delete();
    $veiculo->delete();
    $cliente->delete();
    $filial->delete();
});
