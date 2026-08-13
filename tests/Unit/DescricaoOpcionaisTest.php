<?php

use App\Livewire\Veiculos\Form;
use App\Models\Empresa;
use App\Models\OpcionalCatalogo;
use App\Models\Veiculo;
use Illuminate\Support\Str;
use Livewire\Livewire;

uses(Tests\TestCase::class);

beforeEach(fn () => usarMysqlRealDeDev());

it('salva a descricao do veiculo e ela aparece na pagina publica', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $marca = 'DescricaoTeste'.Str::random(6);

    Livewire::actingAs($user)
        ->test(Form::class)
        ->set('marca', $marca)
        ->set('modelo', 'Modelo X')
        ->set('km', 1000)
        ->set('descricao', 'Único dono, revisões em dia, pneus novos.')
        ->call('salvar');

    $veiculo = Veiculo::where('marca', $marca)->first();
    expect($veiculo)->not->toBeNull()
        ->and($veiculo->descricao)->toBe('Único dono, revisões em dia, pneus novos.');

    $veiculo->update(['status' => 'disponivel']);

    $resposta = $this->withServerVariables(['HTTP_HOST' => 'empresa-a.'.config('tenancy.central_domain')])
        ->get(route('veiculo.show', $veiculo));

    $resposta->assertOk()->assertSee('Único dono, revisões em dia, pneus novos.');

    $veiculo->delete();
});

it('marca e desmarca opcional do catalogo, e mostra check-icon na pagina publica', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $veiculo = Veiculo::create([
        'empresa_id' => $empresa->id,
        'marca' => 'OpcionalTeste'.Str::random(6),
        'modelo' => 'Y',
        'slug' => Str::slug('opcional-teste-'.Str::random(8)),
        'km' => 0, 'portas' => 4, 'tipo_propriedade' => 'proprio',
        'data_entrada' => now(), 'status' => 'disponivel',
    ]);
    $veiculo->refresh();

    $catalogo = OpcionalCatalogo::first();
    expect($catalogo)->not->toBeNull('precisa do catalogo seedado');

    $component = Livewire::actingAs($user)->test(Form::class, ['veiculo' => $veiculo]);

    $component->call('alternarOpcionalCatalogo', $catalogo->id);
    $veiculo->refresh();
    expect($veiculo->opcionais()->where('opcional_catalogo_id', $catalogo->id)->exists())->toBeTrue();

    // pagina publica mostra o opcional marcado
    $resposta = $this->withServerVariables(['HTTP_HOST' => 'empresa-a.'.config('tenancy.central_domain')])
        ->get(route('veiculo.show', $veiculo));
    $resposta->assertOk()->assertSee($catalogo->nome);

    // desmarcar remove
    $component->call('alternarOpcionalCatalogo', $catalogo->id);
    $veiculo->refresh();
    expect($veiculo->opcionais()->where('opcional_catalogo_id', $catalogo->id)->exists())->toBeFalse();

    // custom continua funcionando
    $component->set('novoOpcional', 'Item Personalizado XYZ')->call('adicionarOpcional');
    $veiculo->refresh();
    expect($veiculo->opcionais()->whereNull('opcional_catalogo_id')->where('nome', 'Item Personalizado XYZ')->exists())->toBeTrue();

    $veiculo->delete();
});
