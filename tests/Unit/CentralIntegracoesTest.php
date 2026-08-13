<?php

use App\Livewire\Integracoes\Canais;
use App\Models\Canal;
use App\Models\CanalCredencial;
use App\Models\Empresa;
use App\Models\Publicacao;
use App\Models\Veiculo;
use Illuminate\Support\Str;
use Livewire\Livewire;

uses(Tests\TestCase::class);

beforeEach(fn () => usarMysqlRealDeDev());

it('testa a conexao de um canal configurado usando um veiculo real do estoque', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $canal = Canal::where('slug', 'site_proprio')->firstOrFail();

    $veiculo = Veiculo::create([
        'empresa_id' => $empresa->id,
        'marca' => 'IntegracaoTeste'.Str::random(6),
        'modelo' => 'I',
        'slug' => Str::slug('integracao-teste-'.Str::random(8)),
        'km' => 0, 'portas' => 4, 'tipo_propriedade' => 'proprio',
        'data_entrada' => now(), 'status' => 'disponivel', 'preco_venda' => 40000,
    ]);

    Livewire::actingAs($user)->test(Canais::class)
        ->call('testarConexao', $canal->id)
        ->assertSet("resultadoTeste.{$canal->id}.ok", true);

    $veiculo->delete();
});

it('reporta canal nao configurado com mensagem clara ao testar', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $canal = Canal::where('slug', 'mercadolivre')->firstOrFail();

    Livewire::actingAs($user)->test(Canais::class)
        ->call('testarConexao', $canal->id)
        ->assertSet("resultadoTeste.{$canal->id}.ok", false);
});

it('salva e remove credencial de um canal', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $canal = Canal::where('slug', 'mercadolivre')->firstOrFail();

    Livewire::actingAs($user)->test(Canais::class)
        ->call('abrirCredenciais', $canal->id)
        ->set('novaCredencialChave', 'client_id')
        ->set('novaCredencialValor', 'abc123')
        ->call('salvarCredencial')
        ->assertHasNoErrors();

    $credencial = CanalCredencial::where('canal_id', $canal->id)->where('chave', 'client_id')->first();
    expect($credencial)->not->toBeNull()
        ->and($credencial->valor)->toBe('abc123');

    Livewire::actingAs($user)->test(Canais::class)
        ->call('removerCredencial', $credencial->id);

    expect(CanalCredencial::find($credencial->id))->toBeNull();
});

it('mostra o historico de publicacoes com link de evidencia', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $canal = Canal::where('slug', 'site_proprio')->firstOrFail();

    $veiculo = Veiculo::create([
        'empresa_id' => $empresa->id,
        'marca' => 'EvidenciaTeste'.Str::random(6),
        'modelo' => 'E',
        'slug' => Str::slug('evidencia-teste-'.Str::random(8)),
        'km' => 0, 'portas' => 4, 'tipo_propriedade' => 'proprio',
        'data_entrada' => now(), 'status' => 'disponivel',
    ]);

    $publicacao = Publicacao::create([
        'empresa_id' => $empresa->id,
        'veiculo_id' => $veiculo->id,
        'canal_id' => $canal->id,
        'status' => 'publicado',
        'url_anuncio' => 'https://exemplo.com/anuncio-teste',
        'ultima_sincronizacao_em' => now(),
    ]);

    Livewire::actingAs($user)->test(Canais::class)
        ->assertSee('Ver anúncio')
        ->assertSee($veiculo->marca);

    $publicacao->delete();
    $veiculo->delete();
});
