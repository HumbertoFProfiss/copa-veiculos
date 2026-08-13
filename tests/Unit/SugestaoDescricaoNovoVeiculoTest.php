<?php

use App\Livewire\Veiculos\Form;
use App\Models\Empresa;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

uses(Tests\TestCase::class);

beforeEach(fn () => usarMysqlRealDeDev());

it('pede e usa uma sugestao de descricao da IA na tela de novo veiculo, sem precisar salvar antes', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    config(['services.ia.provider' => 'openai_compatible', 'services.ia.api_key' => 'chave-de-teste']);

    Http::fake([
        '*/chat/completions' => Http::response([
            'choices' => [
                ['message' => ['content' => 'HB20 2016 completo, revisado, pronto pra rodar.']],
            ],
        ], 200),
    ]);

    Livewire::actingAs($user)->test(Form::class)
        ->set('marca', 'Hyundai')
        ->set('modelo', 'HB20')
        ->call('solicitarDescricaoIaNovoVeiculo')
        ->assertSet('descricaoSugeridaPendente', 'HB20 2016 completo, revisado, pronto pra rodar.')
        ->call('usarDescricaoSugeridaPendente')
        ->assertSet('descricao', 'HB20 2016 completo, revisado, pronto pra rodar.')
        ->assertSet('descricaoSugeridaPendente', null);
});
