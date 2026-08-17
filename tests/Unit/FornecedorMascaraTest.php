<?php

use App\Livewire\Fornecedores\Index;
use App\Models\Empresa;
use Livewire\Livewire;

uses(Tests\TestCase::class);

beforeEach(fn () => usarMysqlRealDeDev());

it('aplica mascara de formatacao nos campos de CPF/CNPJ e telefone do cadastro de fornecedor', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $html = Livewire::actingAs($user)->test(Index::class)
        ->call('novo')
        ->html();

    expect($html)->toContain('x-on:input="$el.value = maskCpfCnpj($el.value)"')
        ->and($html)->toContain('x-on:input="$el.value = maskTelefone($el.value)"');
});
