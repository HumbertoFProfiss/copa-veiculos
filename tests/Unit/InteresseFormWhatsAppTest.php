<?php

use App\Livewire\Public\InteresseForm;
use App\Models\Empresa;
use App\Models\Lead;
use App\Models\Veiculo;
use Illuminate\Support\Str;
use Livewire\Livewire;

uses(Tests\TestCase::class);

beforeEach(fn () => usarMysqlRealDeDev());

it('exige um whatsapp valido antes de enviar o formulario de interesse', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);

    $veiculo = Veiculo::create([
        'empresa_id' => $empresa->id,
        'marca' => 'InteresseTeste'.Str::random(6),
        'modelo' => 'Z',
        'ano_modelo' => 2024,
        'slug' => Str::slug('interesse-teste-'.Str::random(8)),
        'km' => 0, 'portas' => 4, 'tipo_propriedade' => 'proprio',
        'data_entrada' => now(), 'status' => 'disponivel',
    ]);

    Livewire::test(InteresseForm::class, ['veiculo' => $veiculo])
        ->set('nome', 'Cliente Teste')
        ->set('telefone', '123')
        ->set('email', '')
        ->call('enviar')
        ->assertHasErrors(['telefone' => 'regex']);

    expect(Lead::where('veiculo_id', $veiculo->id)->exists())->toBeFalse();

    $component = Livewire::test(InteresseForm::class, ['veiculo' => $veiculo])
        ->set('nome', 'Cliente Teste')
        ->set('telefone', '(14) 99999-8888')
        ->set('email', '')
        ->call('enviar')
        ->assertHasNoErrors()
        ->assertSet('enviado', true);

    $lead = Lead::where('veiculo_id', $veiculo->id)->first();
    expect($lead)->not->toBeNull()
        ->and($lead->telefone)->toBe('(14) 99999-8888');

    $url = $component->instance()->whatsappContinuarUrl();
    expect($url)->toContain('https://wa.me/5514997542803')
        ->and($url)->toContain(rawurlencode($veiculo->marca));

    $veiculo->delete();
});
