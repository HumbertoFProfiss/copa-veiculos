<?php

use App\Livewire\Chamadas\Index;
use App\Models\ChamadaProposta;
use App\Models\Empresa;
use Illuminate\Support\Str;
use Livewire\Livewire;

uses(Tests\TestCase::class);

beforeEach(fn () => usarMysqlRealDeDev());

it('registra uma chamada com intencao outros assuntos', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    Livewire::actingAs($user)->test(Index::class)
        ->assertSee('Outros assuntos')
        ->call('novo')
        ->set('tipo', 'ligacao')
        ->set('intencao', 'outros')
        ->set('resultado', 'sem_resposta')
        ->set('observacoes', 'Cliente ligou perguntando sobre horário de funcionamento')
        ->call('salvar')
        ->assertHasNoErrors();

    $chamada = ChamadaProposta::where('observacoes', 'like', '%horário de funcionamento%')->first();
    expect($chamada)->not->toBeNull()
        ->and($chamada->intencao)->toBe('outros')
        ->and($chamada->intencaoLabel())->toBe('Outros assuntos');

    $chamada->delete();
});
