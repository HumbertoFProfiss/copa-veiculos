<?php

use App\Livewire\Configuracoes\Index as ConfiguracoesIndex;
use App\Livewire\Relatorios\Estoque;
use App\Models\Empresa;
use App\Models\Veiculo;
use Illuminate\Support\Str;
use Livewire\Livewire;

uses(Tests\TestCase::class);

beforeEach(fn () => usarMysqlRealDeDev());

it('classifica a curva abc respeitando os limites configurados na empresa', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $limiteAOriginal = $empresa->abc_limite_a;
    $limiteBOriginal = $empresa->abc_limite_b;
    $empresa->update(['abc_limite_a' => 50, 'abc_limite_b' => 80]);

    // Precos muito acima do estoque real de dev de proposito: essas 3
    // linhas dominam o topo da ordenacao por preco (orderByDesc), tornando
    // a contribuicao do resto do estoque desprezivel (<1%) no acumulado -
    // assim o teste nao depende de quantos outros veiculos existem no banco.
    $sufixo = Str::random(6);
    $v1 = Veiculo::create([
        'empresa_id' => $empresa->id, 'marca' => 'AbcTeste'.$sufixo, 'modelo' => 'Caro'.$sufixo,
        'slug' => Str::slug('abc-caro-'.$sufixo), 'km' => 0, 'portas' => 4, 'tipo_propriedade' => 'proprio',
        'data_entrada' => now(), 'status' => 'disponivel', 'preco_venda' => 36000000,
    ]);
    $v2 = Veiculo::create([
        'empresa_id' => $empresa->id, 'marca' => 'AbcTeste'.$sufixo, 'modelo' => 'Medio'.$sufixo,
        'slug' => Str::slug('abc-medio-'.$sufixo), 'km' => 0, 'portas' => 4, 'tipo_propriedade' => 'proprio',
        'data_entrada' => now(), 'status' => 'disponivel', 'preco_venda' => 34000000,
    ]);
    $v3 = Veiculo::create([
        'empresa_id' => $empresa->id, 'marca' => 'AbcTeste'.$sufixo, 'modelo' => 'Barato'.$sufixo,
        'slug' => Str::slug('abc-barato-'.$sufixo), 'km' => 0, 'portas' => 4, 'tipo_propriedade' => 'proprio',
        'data_entrada' => now(), 'status' => 'disponivel', 'preco_venda' => 30000000,
    ]);

    Livewire::actingAs($user)->test(Estoque::class)
        ->assertSee('50%')
        ->assertSee('80%');

    $classificados = (new Estoque)->classificarAbc();
    $porModelo = $classificados->keyBy(fn ($c) => $c['veiculo']->modelo);

    expect($porModelo['Caro'.$sufixo]['classe'])->toBe('A')
        ->and($porModelo['Medio'.$sufixo]['classe'])->toBe('B')
        ->and($porModelo['Barato'.$sufixo]['classe'])->toBe('C');

    $v1->delete();
    $v2->delete();
    $v3->delete();
    $empresa->update(['abc_limite_a' => $limiteAOriginal, 'abc_limite_b' => $limiteBOriginal]);
});

it('salva os limites da curva abc em configuracoes e rejeita limite A maior ou igual ao B', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);
    $limiteAOriginal = $empresa->abc_limite_a;
    $limiteBOriginal = $empresa->abc_limite_b;

    Livewire::actingAs($user)->test(ConfiguracoesIndex::class)
        ->set('abc_limite_a', 90)
        ->set('abc_limite_b', 90)
        ->call('salvar')
        ->assertHasErrors('abc_limite_a');

    expect($empresa->fresh()->abc_limite_a)->toBe($limiteAOriginal);

    Livewire::actingAs($user)->test(ConfiguracoesIndex::class)
        ->set('abc_limite_a', 70)
        ->set('abc_limite_b', 90)
        ->call('salvar')
        ->assertHasNoErrors();

    expect($empresa->fresh()->abc_limite_a)->toBe(70)
        ->and($empresa->fresh()->abc_limite_b)->toBe(90);

    $empresa->update(['abc_limite_a' => $limiteAOriginal, 'abc_limite_b' => $limiteBOriginal]);
});
