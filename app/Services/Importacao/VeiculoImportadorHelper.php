<?php

namespace App\Services\Importacao;

/**
 * Lógica de parse compartilhada entre adapters (aplicar o mapeamento
 * coluna-origem => campo-destino numa linha e normalizar tipos/valores).
 */
class VeiculoImportadorHelper
{
    public static function aplicarMapeamento(array $linha, array $mapeamento): VeiculoImportado
    {
        $valor = fn (string $campoDestino) => static::valorPorCampoDestino($linha, $mapeamento, $campoDestino);

        return new VeiculoImportado(
            marca: static::normalizarTexto($valor('marca')),
            modelo: static::normalizarTexto($valor('modelo')),
            versao: static::normalizarTexto($valor('versao')),
            ano_fabricacao: static::normalizarInteiro($valor('ano_fabricacao')),
            ano_modelo: static::normalizarInteiro($valor('ano_modelo')),
            km: static::normalizarInteiro($valor('km')),
            combustivel: static::normalizarTexto($valor('combustivel')),
            cambio: static::normalizarTexto($valor('cambio')),
            cor: static::normalizarTexto($valor('cor')),
            placa: static::normalizarTexto($valor('placa')),
            numero_chassi: static::normalizarTexto($valor('numero_chassi')),
            preco_venda: static::normalizarDecimal($valor('preco_venda')),
        );
    }

    protected static function valorPorCampoDestino(array $linha, array $mapeamento, string $campoDestino): ?string
    {
        $colunaOrigem = array_search($campoDestino, $mapeamento, true);

        if ($colunaOrigem === false || ! array_key_exists($colunaOrigem, $linha)) {
            return null;
        }

        return $linha[$colunaOrigem];
    }

    protected static function normalizarTexto(?string $valor): ?string
    {
        $valor = trim((string) $valor);

        return $valor === '' ? null : $valor;
    }

    protected static function normalizarInteiro(?string $valor): ?int
    {
        $digitos = preg_replace('/\D/', '', (string) $valor);

        return $digitos === '' ? null : (int) $digitos;
    }

    protected static function normalizarDecimal(?string $valor): ?float
    {
        if (blank($valor)) {
            return null;
        }

        // Aceita "R$ 45.000,00" ou "45000.00" - remove tudo que não for
        // dígito/vírgula/ponto, depois trata vírgula como separador decimal
        // se for o último separador (padrão brasileiro).
        $limpo = preg_replace('/[^\d,.]/', '', (string) $valor);

        if (str_contains($limpo, ',') && strrpos($limpo, ',') > strrpos((string) $limpo, '.')) {
            $limpo = str_replace('.', '', $limpo);
            $limpo = str_replace(',', '.', $limpo);
        } else {
            $limpo = str_replace(',', '', $limpo);
        }

        return $limpo === '' ? null : (float) $limpo;
    }
}
