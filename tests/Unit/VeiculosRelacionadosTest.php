<?php

use App\Models\Empresa;
use App\Models\Veiculo;
use Illuminate\Support\Str;

uses(Tests\TestCase::class);

beforeEach(fn () => usarMysqlRealDeDev());

function criarVeiculoRelacionado(Empresa $empresa, string $marca, string $modelo): Veiculo
{
    return Veiculo::create([
        'empresa_id' => $empresa->id,
        'marca' => $marca,
        'modelo' => $modelo,
        'slug' => Str::slug($marca.'-'.$modelo.'-'.Str::random(8)),
        'km' => 0, 'portas' => 4, 'tipo_propriedade' => 'proprio',
        'data_entrada' => now(), 'status' => 'disponivel',
    ]);
}

it('mostra veiculos da mesma marca na pagina do veiculo, sem incluir o proprio veiculo', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);

    $marca = 'RelacionadoTeste'.Str::random(6);
    $principal = criarVeiculoRelacionado($empresa, $marca, 'Principal');
    $irmao = criarVeiculoRelacionado($empresa, $marca, 'Irmao');
    $outraMarca = criarVeiculoRelacionado($empresa, 'OutraMarca'.Str::random(6), 'Modelo');

    $resposta = $this->withServerVariables(['HTTP_HOST' => 'empresa-a.'.config('tenancy.central_domain')])
        ->get(route('veiculo.show', $principal));

    $resposta->assertOk()
        ->assertSee('Outros '.$marca)
        ->assertSee($irmao->modelo)
        ->assertDontSee($outraMarca->modelo);

    $principal->delete();
    $irmao->delete();
    $outraMarca->delete();
});

it('cai no fallback de outros veiculos quando nao ha nenhum da mesma marca', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);

    $marcaUnica = 'MarcaUnicaTeste'.Str::random(8);
    $principal = criarVeiculoRelacionado($empresa, $marcaUnica, 'Solo');
    $outro = criarVeiculoRelacionado($empresa, 'QualquerMarca'.Str::random(6), 'Generico');

    $resposta = $this->withServerVariables(['HTTP_HOST' => 'empresa-a.'.config('tenancy.central_domain')])
        ->get(route('veiculo.show', $principal));

    $resposta->assertOk()
        ->assertSee('Outros veículos em destaque')
        ->assertSee($outro->modelo);

    $principal->delete();
    $outro->delete();
});
