<?php

use App\Livewire\Configuracoes\Index as ConfiguracoesIndex;
use App\Livewire\Veiculos\Index;
use App\Models\Empresa;
use App\Models\Veiculo;
use Illuminate\Support\Str;
use Livewire\Livewire;

uses(Tests\TestCase::class);

beforeEach(fn () => usarMysqlRealDeDev());

function criarVeiculoParaDestaque(Empresa $empresa, string $sufixo): Veiculo
{
    return Veiculo::create([
        'empresa_id' => $empresa->id,
        'marca' => 'DestaqueTeste'.$sufixo,
        'modelo' => 'M',
        'slug' => Str::slug('destaque-teste-'.$sufixo.'-'.Str::random(6)),
        'km' => 0, 'portas' => 4, 'tipo_propriedade' => 'proprio',
        'data_entrada' => now(), 'status' => 'disponivel',
    ]);
}

it('respeita o limite configuravel de veiculos em destaque', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $limiteOriginal = $empresa->limite_destaques;
    $destaquesExistentes = Veiculo::where('destaque', true)->count();
    $empresa->update(['limite_destaques' => $destaquesExistentes + 1]);

    $v1 = criarVeiculoParaDestaque($empresa, Str::random(4));
    $v2 = criarVeiculoParaDestaque($empresa, Str::random(4));

    Livewire::actingAs($user)->test(Index::class)
        ->call('alternarDestaque', $v1->id);

    expect($v1->fresh()->destaque)->toBeTrue();

    Livewire::actingAs($user)->test(Index::class)
        ->call('alternarDestaque', $v2->id);

    expect($v2->fresh()->destaque)->toBeFalse('nao deveria marcar destaque acima do limite configurado');

    // removendo o destaque do primeiro libera espaco pro segundo
    Livewire::actingAs($user)->test(Index::class)->call('alternarDestaque', $v1->id);
    expect($v1->fresh()->destaque)->toBeFalse();

    Livewire::actingAs($user)->test(Index::class)->call('alternarDestaque', $v2->id);
    expect($v2->fresh()->destaque)->toBeTrue();

    $empresa->update(['limite_destaques' => $limiteOriginal]);
    $v1->delete();
    $v2->delete();
});

it('salva o limite de destaques em configuracoes', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);
    $limiteOriginal = $empresa->limite_destaques;

    Livewire::actingAs($user)->test(ConfiguracoesIndex::class)
        ->set('limite_destaques', 10)
        ->call('salvar')
        ->assertHasNoErrors();

    expect($empresa->fresh()->limite_destaques)->toBe(10);

    $empresa->update(['limite_destaques' => $limiteOriginal]);
});
