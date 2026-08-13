<?php

use App\Livewire\Dashboard;
use App\Models\Cliente;
use App\Models\Empresa;
use Illuminate\Support\Str;
use Livewire\Livewire;

uses(Tests\TestCase::class);

beforeEach(fn () => usarMysqlRealDeDev());

it('mostra cliente recem cadastrado no feed de atividades recentes', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $nome = 'ClienteAtividadeTeste'.Str::random(6);
    $cliente = Cliente::create(['empresa_id' => $empresa->id, 'nome' => $nome]);

    Livewire::actingAs($user)->test(Dashboard::class)
        ->assertSee('Atividades recentes')
        ->assertSee("Cliente cadastrado: {$nome}");

    $cliente->delete();
});
