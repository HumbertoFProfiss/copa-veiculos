<?php

use App\Livewire\Veiculos\Form;
use App\Models\Empresa;
use App\Models\Veiculo;
use App\Models\VeiculoVideo;
use Illuminate\Support\Str;
use Livewire\Livewire;

uses(Tests\TestCase::class);

beforeEach(fn () => usarMysqlRealDeDev());

it('adiciona um link de video do youtube, rejeita link invalido, e remove', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $veiculo = Veiculo::create([
        'empresa_id' => $empresa->id,
        'marca' => 'VideoTeste'.Str::random(6),
        'modelo' => 'V',
        'slug' => Str::slug('video-teste-'.Str::random(8)),
        'km' => 0, 'portas' => 4, 'tipo_propriedade' => 'proprio',
        'data_entrada' => now(), 'status' => 'disponivel',
    ]);
    $veiculo->refresh();

    $component = Livewire::actingAs($user)->test(Form::class, ['veiculo' => $veiculo])
        ->set('novoVideoUrl', 'https://exemplo.com/nao-e-youtube')
        ->call('adicionarVideo')
        ->assertHasErrors('novoVideoUrl');

    expect(VeiculoVideo::where('veiculo_id', $veiculo->id)->count())->toBe(0);

    $component->set('novoVideoUrl', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ')
        ->call('adicionarVideo')
        ->assertHasNoErrors();

    $video = VeiculoVideo::where('veiculo_id', $veiculo->id)->first();
    expect($video)->not->toBeNull()
        ->and($video->tipo)->toBe('youtube')
        ->and($video->urlEmbed())->toBe('https://www.youtube.com/embed/dQw4w9WgXcQ');

    $component->call('removerVideo', $video->id);
    expect(VeiculoVideo::where('veiculo_id', $veiculo->id)->count())->toBe(0);

    $veiculo->delete();
});

it('mostra o video incorporado na pagina publica do veiculo', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);

    $veiculo = Veiculo::create([
        'empresa_id' => $empresa->id,
        'marca' => 'VideoPublicoTeste'.Str::random(6),
        'modelo' => 'V',
        'slug' => Str::slug('video-publico-teste-'.Str::random(8)),
        'km' => 0, 'portas' => 4, 'tipo_propriedade' => 'proprio',
        'data_entrada' => now(), 'status' => 'disponivel',
    ]);
    VeiculoVideo::create(['veiculo_id' => $veiculo->id, 'tipo' => 'youtube', 'url' => 'https://youtu.be/dQw4w9WgXcQ']);

    $resposta = $this->withServerVariables(['HTTP_HOST' => 'empresa-a.'.config('tenancy.central_domain')])
        ->get(route('veiculo.show', $veiculo));

    $resposta->assertOk()->assertSee('https://www.youtube.com/embed/dQw4w9WgXcQ', false);

    $veiculo->delete();
});
