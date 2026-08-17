<?php

use App\Livewire\Configuracoes\Index;
use App\Models\Empresa;
use Livewire\Livewire;

uses(Tests\TestCase::class);

beforeEach(fn () => usarMysqlRealDeDev());

it('aplica mascara de telefone nos campos de telefone e whatsapp da empresa em configuracoes', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $html = Livewire::actingAs($user)->test(Index::class)->html();

    expect(substr_count($html, 'x-on:input="$el.value = maskTelefone($el.value)"'))->toBe(2);
});
