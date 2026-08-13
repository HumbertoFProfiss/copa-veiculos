<?php

use App\Models\Empresa;
use App\Models\Veiculo;
use App\Models\VeiculoFoto;
use Illuminate\Support\Str;

uses(Tests\TestCase::class);

beforeEach(fn () => usarMysqlRealDeDev());

it('gira entre as fotos dos veiculos em destaque no hero da home', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);

    $veiculos = collect(range(1, 2))->map(function ($i) use ($empresa) {
        $sufixo = Str::random(6);
        $veiculo = Veiculo::create([
            'empresa_id' => $empresa->id,
            'marca' => 'HeroTeste'.$sufixo,
            'modelo' => 'M'.$i,
            'slug' => Str::slug('hero-teste-'.$sufixo),
            'km' => 0, 'portas' => 4, 'tipo_propriedade' => 'proprio',
            'data_entrada' => now(), 'status' => 'disponivel', 'destaque' => true,
        ]);
        VeiculoFoto::create(['veiculo_id' => $veiculo->id, 'path' => "veiculos/{$veiculo->id}/foto-{$i}.jpg", 'ordem' => 0]);

        return $veiculo;
    });

    $resposta = $this->withServerVariables(['HTTP_HOST' => 'empresa-a.'.config('tenancy.central_domain')])
        ->get(route('home'));

    $resposta->assertOk()
        ->assertSee('heroIndex', false)
        ->assertSee('setInterval', false);

    $veiculos->each(fn (Veiculo $v) => $v->delete());
});
