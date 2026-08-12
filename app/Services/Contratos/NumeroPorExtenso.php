<?php

namespace App\Services\Contratos;

/**
 * Escreve valor monetário por extenso em português, pra cláusula de valor
 * dos contratos (ex: "sessenta e dois mil reais"). Cobre a faixa realista
 * de preço de veículo (até bilhões, por segurança).
 */
class NumeroPorExtenso
{
    protected const UNIDADES = ['', 'um', 'dois', 'três', 'quatro', 'cinco', 'seis', 'sete', 'oito', 'nove'];

    protected const DEZ_A_DEZENOVE = [
        'dez', 'onze', 'doze', 'treze', 'catorze', 'quinze', 'dezesseis', 'dezessete', 'dezoito', 'dezenove',
    ];

    protected const DEZENAS = [
        '', '', 'vinte', 'trinta', 'quarenta', 'cinquenta', 'sessenta', 'setenta', 'oitenta', 'noventa',
    ];

    protected const CENTENAS = [
        '', 'cento', 'duzentos', 'trezentos', 'quatrocentos', 'quinhentos',
        'seiscentos', 'setecentos', 'oitocentos', 'novecentos',
    ];

    public function moeda(float $valor): string
    {
        $inteiro = (int) floor($valor);
        $centavos = (int) round(($valor - $inteiro) * 100);

        $texto = $this->porExtenso($inteiro).' '.($inteiro === 1 ? 'real' : 'reais');

        if ($centavos > 0) {
            $texto .= ' e '.$this->porExtenso($centavos).' '.($centavos === 1 ? 'centavo' : 'centavos');
        }

        return $texto;
    }

    public function porExtenso(int $numero): string
    {
        if ($numero === 0) {
            return 'zero';
        }

        if ($numero < 0) {
            return 'menos '.$this->porExtenso(abs($numero));
        }

        $partes = [];

        if ($numero >= 1_000_000_000) {
            $bilhoes = intdiv($numero, 1_000_000_000);
            $partes[] = $this->porExtenso($bilhoes).($bilhoes === 1 ? ' bilhão' : ' bilhões');
            $numero %= 1_000_000_000;
        }

        if ($numero >= 1_000_000) {
            $milhoes = intdiv($numero, 1_000_000);
            $partes[] = $this->porExtenso($milhoes).($milhoes === 1 ? ' milhão' : ' milhões');
            $numero %= 1_000_000;
        }

        if ($numero >= 1000) {
            $milhares = intdiv($numero, 1000);
            $partes[] = ($milhares === 1 ? 'mil' : $this->porExtenso($milhares).' mil');
            $numero %= 1000;
        }

        if ($numero > 0) {
            $partes[] = $this->tresDigitos($numero);
        }

        return implode(' e ', array_filter($partes));
    }

    protected function tresDigitos(int $numero): string
    {
        if ($numero === 100) {
            return 'cem';
        }

        $centena = intdiv($numero, 100);
        $resto = $numero % 100;

        $partes = [];

        if ($centena > 0) {
            $partes[] = self::CENTENAS[$centena];
        }

        if ($resto >= 10 && $resto < 20) {
            $partes[] = self::DEZ_A_DEZENOVE[$resto - 10];
        } elseif ($resto > 0) {
            $dezena = intdiv($resto, 10);
            $unidade = $resto % 10;
            $sub = self::DEZENAS[$dezena];

            if ($unidade > 0) {
                $sub .= ($sub ? ' e ' : '').self::UNIDADES[$unidade];
            }

            $partes[] = $sub;
        }

        return implode(' e ', array_filter($partes));
    }
}
