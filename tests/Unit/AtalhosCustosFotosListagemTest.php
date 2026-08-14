<?php

use App\Livewire\Veiculos\Index;
use App\Models\Empresa;
use App\Models\Veiculo;
use Illuminate\Support\Str;
use Livewire\Livewire;

uses(Tests\TestCase::class);

beforeEach(fn () => usarMysqlRealDeDev());

it('mostra atalhos de custos e fotos na listagem de estoque, apontando pras ancoras certas', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $veiculo = Veiculo::create([
        'empresa_id' => $empresa->id,
        'marca' => 'AtalhoTeste'.Str::random(6),
        'modelo' => 'A',
        'slug' => Str::slug('atalho-teste-'.Str::random(8)),
        'km' => 0, 'portas' => 4, 'tipo_propriedade' => 'proprio',
        'data_entrada' => now(), 'status' => 'disponivel',
    ]);

    $rota = route('admin.veiculos.editar', $veiculo);

    Livewire::actingAs($user)->test(Index::class)
        ->assertSee($rota.'#custos', false)
        ->assertSee($rota.'#fotos', false);

    $veiculo->delete();
});
