<?php

use App\Livewire\Veiculos\Form;
use App\Models\CategoriaFinanceira;
use App\Models\CustoVeiculo;
use App\Models\Empresa;
use App\Models\Veiculo;
use Illuminate\Support\Str;
use Livewire\Livewire;

uses(Tests\TestCase::class);

beforeEach(fn () => usarMysqlRealDeDev());

it('usa o atalho de categoria pra pre-preencher e adiciona o custo ao veiculo', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $veiculo = Veiculo::create([
        'empresa_id' => $empresa->id,
        'marca' => 'CustoTeste'.Str::random(6),
        'modelo' => 'K',
        'slug' => Str::slug('custo-teste-'.Str::random(8)),
        'km' => 0, 'portas' => 4, 'tipo_propriedade' => 'proprio',
        'data_entrada' => now(), 'status' => 'disponivel',
        'preco_compra' => 40000, 'preco_venda' => 50000,
    ]);
    $veiculo->refresh();

    $component = Livewire::actingAs($user)->test(Form::class, ['veiculo' => $veiculo]);

    $component->call('usarCategoriaCustoRapida', 'Funilaria e pintura')
        ->assertSet('custoDescricao', 'Funilaria e pintura');

    $categoria = CategoriaFinanceira::where('nome', 'Funilaria e pintura')->where('tipo', 'despesa')->first();
    expect($categoria)->not->toBeNull();

    $component->set('custoValor', 1500)
        ->call('adicionarCusto')
        ->assertHasNoErrors();

    $veiculo->refresh();
    $custo = CustoVeiculo::where('veiculo_id', $veiculo->id)->first();
    expect($custo)->not->toBeNull()
        ->and((float) $custo->valor)->toBe(1500.0)
        ->and($custo->categoria_id)->toBe($categoria->id)
        ->and($veiculo->margem())->toBe(8500.0);

    // clicar na mesma categoria de novo nao duplica a categoria financeira
    $component->call('usarCategoriaCustoRapida', 'Funilaria e pintura');
    expect(CategoriaFinanceira::where('nome', 'Funilaria e pintura')->count())->toBe(1);

    $component->call('removerCusto', $custo->id);
    expect(CustoVeiculo::where('veiculo_id', $veiculo->id)->count())->toBe(0);

    $veiculo->delete();
});
