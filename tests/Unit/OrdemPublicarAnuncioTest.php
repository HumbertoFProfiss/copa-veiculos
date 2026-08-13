<?php

use App\Livewire\Veiculos\Form;
use App\Models\Empresa;
use App\Models\Veiculo;
use Illuminate\Support\Str;
use Livewire\Livewire;

uses(Tests\TestCase::class);

beforeEach(fn () => usarMysqlRealDeDev());

it('mostra publicar anuncio depois de opcionais e custos, nao entre fotos e opcionais', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $veiculo = Veiculo::create([
        'empresa_id' => $empresa->id,
        'marca' => 'OrdemTeste'.Str::random(6),
        'modelo' => 'O',
        'slug' => Str::slug('ordem-teste-'.Str::random(8)),
        'km' => 0, 'portas' => 4, 'tipo_propriedade' => 'proprio',
        'data_entrada' => now(), 'status' => 'disponivel',
    ]);
    $veiculo->refresh();

    $html = Livewire::actingAs($user)->test(Form::class, ['veiculo' => $veiculo])->html();

    $posOpcionais = strpos($html, 'Opcionais');
    $posCustos = strpos($html, 'Custos do veículo');
    $posPublicar = strpos($html, 'Publicar anúncio');

    expect($posOpcionais)->not->toBeFalse()
        ->and($posCustos)->not->toBeFalse()
        ->and($posPublicar)->not->toBeFalse()
        ->and($posPublicar)->toBeGreaterThan($posOpcionais)
        ->and($posPublicar)->toBeGreaterThan($posCustos);

    $veiculo->delete();
});
