<?php

use App\Livewire\Configuracoes\Index as ConfiguracoesIndex;
use App\Models\Empresa;
use Livewire\Livewire;

uses(Tests\TestCase::class);

beforeEach(fn () => usarMysqlRealDeDev());

it('mostra NF-e, assinatura eletronica e MultiBanco como demo configurada, e Renave continua nao configurada', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    Livewire::actingAs($user)->test(ConfiguracoesIndex::class)
        ->assertSeeHtml('Configurada (demo)')
        ->assertSeeHtml('Ver demonstração')
        ->assertSee('Nota Fiscal Eletrônica (NF-e)')
        ->assertSee('Assinatura eletrônica')
        ->assertSee('MultiBanco (financiamento)')
        ->assertSee('Renave')
        ->assertSee('Não configurada');
});
