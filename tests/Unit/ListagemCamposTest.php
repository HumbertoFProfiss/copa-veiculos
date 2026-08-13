<?php

use App\Livewire\Veiculos\Index;
use App\Models\Empresa;
use App\Models\Veiculo;
use Illuminate\Support\Str;
use Livewire\Livewire;

uses(Tests\TestCase::class);

beforeEach(fn () => usarMysqlRealDeDev());

it('mostra cambio, placa e chassi na listagem de estoque', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $sufixo = Str::random(6);
    $veiculo = Veiculo::create([
        'empresa_id' => $empresa->id,
        'marca' => 'ListagemTeste'.$sufixo,
        'modelo' => 'L',
        'slug' => Str::slug('listagem-teste-'.$sufixo),
        'km' => 0, 'portas' => 4, 'tipo_propriedade' => 'proprio',
        'cambio' => 'Automático',
        'placa' => 'LST'.substr($sufixo, 0, 4),
        'numero_chassi' => 'CHASSI'.$sufixo,
        'data_entrada' => now(), 'status' => 'disponivel',
    ]);

    Livewire::actingAs($user)->test(Index::class)
        ->assertSee('Automático')
        ->assertSee('LST'.substr($sufixo, 0, 4))
        ->assertSee('CHASSI'.$sufixo);

    $veiculo->delete();
});
