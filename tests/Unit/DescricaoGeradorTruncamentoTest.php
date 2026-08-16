<?php

use App\Services\Ia\AiProvider;
use App\Services\Ia\DescricaoGerador;

uses(Tests\TestCase::class);

it('corta a descricao no ultimo espaco antes do limite, sem quebrar palavra nem caractere UTF-8', function () {
    $textoLongo = 'Corolla 2020 impecável, único dono, revisões em dia, pneus novos, ar-condicionado gelando, '
        .'multimídia original com câmera de ré, bancos em couro sintético, direção elétrica leve e precisa, '
        .'ideal para quem busca economia e conforto no dia a dia da cidade ou em viagens mais longas pela estrada.';

    $provider = new class implements AiProvider
    {
        public function completar(string $prompt, array $contexto = []): string
        {
            return 'Corolla 2020 impecável, único dono, revisões em dia, pneus novos, ar-condicionado gelando, '
                .'multimídia original com câmera de ré, bancos em couro sintético, direção elétrica leve e precisa, '
                .'ideal para quem busca economia e conforto no dia a dia da cidade ou em viagens mais longas pela estrada.';
        }

        public function disponivel(): bool
        {
            return true;
        }
    };

    $resultado = (new DescricaoGerador($provider))->gerarTexto(['marca' => 'Toyota'], 80);

    expect(mb_strlen($resultado))->toBeLessThanOrEqual(80)
        ->and($resultado)->toEndWith('…')
        ->and($resultado)->not->toContain('  ') // sem espaco duplo sobrando antes do "…"
        ->and($textoLongo)->toStartWith(mb_substr($resultado, 0, -1)); // conteudo antes do "…" e um prefixo real, sem caractere corrompido

    // a palavra cortada nao pode aparecer pela metade - cada palavra do resultado
    // (exceto a reticencia) precisa existir inteira no texto original
    $palavras = explode(' ', rtrim($resultado, '…'));
    foreach ($palavras as $palavra) {
        if ($palavra === '') {
            continue;
        }
        expect($textoLongo)->toContain($palavra);
    }
});

it('nao mexe na descricao quando ela ja esta dentro do limite', function () {
    $provider = new class implements AiProvider
    {
        public function completar(string $prompt, array $contexto = []): string
        {
            return 'Corolla 2020 impecável, único dono.';
        }

        public function disponivel(): bool
        {
            return true;
        }
    };

    $resultado = (new DescricaoGerador($provider))->gerarTexto(['marca' => 'Toyota'], 500);

    expect($resultado)->toBe('Corolla 2020 impecável, único dono.')
        ->and($resultado)->not->toEndWith('…');
});
