<?php

use App\Livewire\Veiculos\Form;
use App\Models\Empresa;
use App\Models\Veiculo;
use Illuminate\Support\Str;
use Livewire\Livewire;

uses(Tests\TestCase::class);

beforeEach(fn () => usarMysqlRealDeDev());

it('o botao Salvar fica fora do form, numa barra fixa (nao sticky), mas o Enter no formulario ainda funciona', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $html = Livewire::actingAs($user)->test(Form::class)->html();

    // "fixed" (nao "sticky") e proposital: com sticky, o botao para de seguir a
    // tela assim que aparecem as secoes de Fotos/Video/Opcionais/Custos depois
    // do primeiro salvar (bug relatado - ver commit). Fixed nao tem esse problema.
    expect($html)->toContain('wire:click="salvar"')
        ->and($html)->toContain('fixed bottom-0')
        ->and($html)->not->toContain('sticky bottom-0');

    // o form ainda tem um submit control (mesmo que escondido) pra Enter continuar funcionando
    expect($html)->toContain('type="submit" class="hidden"');
});

it('a barra fixa do Salvar continua fixed (nao sticky) mesmo depois que o veiculo ja existe e as secoes extras aparecem', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $veiculo = Veiculo::create([
        'empresa_id' => $empresa->id,
        'marca' => 'SalvarFixoTeste'.Str::random(6),
        'modelo' => 'F',
        'slug' => Str::slug('salvar-fixo-teste-'.Str::random(8)),
        'km' => 0, 'portas' => 4, 'tipo_propriedade' => 'proprio',
        'data_entrada' => now(), 'status' => 'disponivel',
    ]);
    $veiculo->refresh();

    $html = Livewire::actingAs($user)->test(Form::class, ['veiculo' => $veiculo])
        ->assertSee('Fotos')
        ->assertSee('Opcionais')
        ->html();

    expect($html)->toContain('fixed bottom-0')
        ->and($html)->not->toContain('sticky bottom-0');

    $veiculo->delete();
});
