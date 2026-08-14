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

it('mostra WhatsApp Business e Portais com API como configurados de verdade (nao e simulacao)', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $componente = Livewire::actingAs($user)->test(ConfiguracoesIndex::class)
        ->assertSee('WhatsApp Business')
        ->assertSee('Portais com API');

    $html = $componente->html();
    $posicaoWhatsapp = strpos($html, 'WhatsApp Business');
    $posicaoPortais = strpos($html, 'Portais com API');

    // Ambos os cards mostram "Configurada" (sem sufixo "(demo)") porque sao
    // funcionalidades reais (wa.me e publicacao via CSV), nao simulacoes.
    expect(substr($html, $posicaoWhatsapp, 400))->toContain('Configurada')->not->toContain('Configurada (demo)');
    expect(substr($html, $posicaoPortais, 400))->toContain('Configurada')->not->toContain('Configurada (demo)');
});
