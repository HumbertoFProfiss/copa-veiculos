<?php

use App\Livewire\Shared\GlobalSearch;
use App\Models\Empresa;
use App\Models\User;
use App\Models\Veiculo;
use Illuminate\Support\Str;
use Livewire\Livewire;

uses(Tests\TestCase::class);

beforeEach(fn () => usarMysqlRealDeDev());

it('encontra veiculos por marca/modelo/placa e respeita permissao do modulo', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);

    $termoUnico = 'ZzTeste'.Str::random(6);

    $veiculo = Veiculo::create([
        'empresa_id' => $empresa->id,
        'marca' => $termoUnico,
        'modelo' => 'Modelo Teste',
        'slug' => Str::slug($termoUnico.'-'.Str::random(4)),
        'km' => 0,
        'portas' => 4,
        'tipo_propriedade' => 'proprio',
        'data_entrada' => now(),
        'status' => 'disponivel',
    ]);

    $user = User::withoutGlobalScopes()->where('empresa_id', $empresa->id)->whereHas('roles', fn ($q) => $q->where('name', 'Proprietário'))->first();

    expect($user)->not->toBeNull('precisa de um usuario Proprietario seedado pra empresa de teste');

    Livewire::actingAs($user)
        ->test(GlobalSearch::class)
        ->set('termo', $termoUnico)
        ->assertSee($termoUnico)
        ->assertSee('Veículos')
        ->assertSee(route('admin.veiculos.editar', $veiculo));

    // Termo curto (menos de 2 letras) nao dispara busca nenhuma.
    Livewire::actingAs($user)
        ->test(GlobalSearch::class)
        ->set('termo', 'a')
        ->assertSee('Digite ao menos 2 letras');

    $veiculo->delete();
});
