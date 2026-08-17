<?php

use App\Livewire\Dashboard;
use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\Veiculo;
use Illuminate\Support\Str;
use Livewire\Livewire;

uses(Tests\TestCase::class);

beforeEach(fn () => usarMysqlRealDeDev());

it('preenche criado_por sozinho e mostra o nome de quem cadastrou o veiculo no feed de atividades', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $this->actingAs($user);

    $marca = 'AtividadeAutorTeste'.Str::random(6);
    $veiculo = Veiculo::create([
        'empresa_id' => $empresa->id,
        'marca' => $marca,
        'modelo' => 'A',
        'slug' => Str::slug('atividade-autor-teste-'.Str::random(8)),
        'km' => 0, 'portas' => 4, 'tipo_propriedade' => 'proprio',
        'data_entrada' => now(), 'status' => 'disponivel',
    ]);

    expect($veiculo->criado_por)->toBe($user->id);

    Livewire::actingAs($user)->test(Dashboard::class)
        ->assertSee("Veículo cadastrado: {$marca} A — {$user->name}");

    $veiculo->delete();
});

it('preenche criado_por sozinho e mostra o nome de quem cadastrou o cliente no feed de atividades', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $this->actingAs($user);

    $nome = 'ClienteAutorTeste'.Str::random(6);
    $cliente = Cliente::create(['empresa_id' => $empresa->id, 'nome' => $nome]);

    expect($cliente->criado_por)->toBe($user->id);

    Livewire::actingAs($user)->test(Dashboard::class)
        ->assertSee("Cliente cadastrado: {$nome} — {$user->name}");

    $cliente->delete();
});

it('nao quebra e nao mostra autor quando o registro nao tem criado_por (ex: cadastro publico sem staff logado)', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    // sem $this->actingAs() - simula um cadastro feito sem staff autenticado
    $nome = 'ClienteSemAutorTeste'.Str::random(6);
    $cliente = Cliente::create(['empresa_id' => $empresa->id, 'nome' => $nome]);

    expect($cliente->criado_por)->toBeNull();

    Livewire::actingAs($user)->test(Dashboard::class)
        ->assertSee("Cliente cadastrado: {$nome}")
        ->assertDontSee("Cliente cadastrado: {$nome} —");

    $cliente->delete();
});
