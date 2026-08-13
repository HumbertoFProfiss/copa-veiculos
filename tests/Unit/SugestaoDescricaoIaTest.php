<?php

use App\Livewire\Ia\SugestaoDescricao;
use App\Livewire\Veiculos\Form;
use App\Models\Empresa;
use App\Models\IaSugestao;
use App\Models\Veiculo;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Livewire\Livewire;

uses(Tests\TestCase::class);

beforeEach(fn () => usarMysqlRealDeDev());

it('pede uma sugestao de descricao a IA e envia pro formulario do veiculo ao usar', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    config(['services.ia.provider' => 'openai_compatible', 'services.ia.api_key' => 'chave-de-teste']);

    Http::fake([
        '*/chat/completions' => Http::response([
            'choices' => [
                ['message' => ['content' => 'Corolla 2020 impecável, único dono, revisões em dia.']],
            ],
        ], 200),
    ]);

    $veiculo = Veiculo::create([
        'empresa_id' => $empresa->id,
        'marca' => 'IaDescricaoTeste'.Str::random(6),
        'modelo' => 'D',
        'slug' => Str::slug('ia-descricao-teste-'.Str::random(8)),
        'km' => 0, 'portas' => 4, 'tipo_propriedade' => 'proprio',
        'data_entrada' => now(), 'status' => 'disponivel',
    ]);
    $veiculo->refresh();

    $sugestaoComponent = Livewire::actingAs($user)->test(SugestaoDescricao::class, ['veiculo' => $veiculo])
        ->call('solicitar')
        ->assertSee('Corolla 2020 impecável');

    $sugestao = IaSugestao::where('sugerivel_id', $veiculo->id)->where('tipo', 'descricao')->first();
    expect($sugestao)->not->toBeNull()
        ->and($sugestao->status)->toBe('pendente');

    $sugestaoComponent->call('usar');

    expect($sugestao->fresh()->status)->toBe('aceita');

    // o formulario do veiculo escuta o evento e preenche a descricao sozinho
    Livewire::actingAs($user)->test(Form::class, ['veiculo' => $veiculo])
        ->dispatch('descricao-sugerida', descricao: 'Corolla 2020 impecável, único dono, revisões em dia.')
        ->assertSet('descricao', 'Corolla 2020 impecável, único dono, revisões em dia.');

    $veiculo->delete();
});
