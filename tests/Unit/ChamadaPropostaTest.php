<?php

use App\Livewire\Chamadas\Index;
use App\Models\ChamadaProposta;
use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\Veiculo;
use Illuminate\Support\Str;
use Livewire\Livewire;

uses(Tests\TestCase::class);

beforeEach(fn () => usarMysqlRealDeDev());

it('registra uma chamada de cliente procurando um veiculo que nao esta em estoque', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $cliente = Cliente::create(['empresa_id' => $empresa->id, 'nome' => 'Cliente Chamada Teste '.Str::random(4)]);

    Livewire::actingAs($user)->test(Index::class)
        ->call('novo')
        ->set('cliente_id', $cliente->id)
        ->set('tipo', 'whatsapp')
        ->set('intencao', 'comprar')
        ->set('veiculo_procurado', 'Corolla 2020 automático')
        ->set('resultado', 'sem_resposta')
        ->call('salvar')
        ->assertHasNoErrors();

    $chamada = ChamadaProposta::where('cliente_id', $cliente->id)->first();
    expect($chamada)->not->toBeNull()
        ->and($chamada->veiculo_procurado)->toBe('Corolla 2020 automático')
        ->and($chamada->intencao)->toBe('comprar')
        ->and($chamada->user_id)->toBe($user->id)
        ->and($chamada->tipoLabel())->toBe('WhatsApp')
        ->and($chamada->resultadoVariant())->toBe('neutral');

    $chamada->delete();
    $cliente->delete();
});

it('sugere veiculos do estoque atual que combinam com a busca do cliente', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $sufixo = Str::random(6);
    $veiculo = Veiculo::create([
        'empresa_id' => $empresa->id,
        'marca' => 'Toyota',
        'modelo' => 'CorollaMatch'.$sufixo,
        'slug' => Str::slug('corolla-match-'.$sufixo),
        'km' => 0, 'portas' => 4, 'tipo_propriedade' => 'proprio',
        'data_entrada' => now(), 'status' => 'disponivel',
    ]);

    $component = Livewire::actingAs($user)->test(Index::class)
        ->call('novo')
        ->set('intencao', 'comprar')
        ->set('veiculo_procurado', 'CorollaMatch'.$sufixo);

    expect($component->get('veiculosCompativeis'))->toHaveCount(1);

    $veiculo->delete();
});

it('marca resultado fechado com a cor de sucesso', function () {
    $chamada = new ChamadaProposta(['resultado' => 'fechado']);
    expect($chamada->resultadoVariant())->toBe('success')
        ->and($chamada->resultadoLabel())->toBe('Fechado');
});
